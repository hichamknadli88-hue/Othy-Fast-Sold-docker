<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use BcMath\Number;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 300;

    private string $adminEmail;

    public function __construct()
    {
        $this->adminEmail = config('app.admin_email', 'yousseflaayadiasape2@gmail.com');
    }

    public function show(): View
    {
        return view('login');
    }

    /**
     * GET /admin/private/login/check?email=...
     * Called by login.blade.php's checkAdminEmail(). Must stay a GET route —
     * the frontend deliberately sends no CSRF token for this lightweight probe.
     */
    public function checkAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $email = strtolower($validated['email']);

        $requiresConfirmation = $this->needsAdminBootstrap($email);

        return response()->json(['requiresConfirmation' => $requiresConfirmation]);
    }

    public function register(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => [
                'required', 'string', 'email', 'max:255', 'unique:users,email',
                // Without this, anyone who races the real admin to /register
                // with the reserved admin email becomes the admin — isAdmin()
                // is just an email match, so account creation IS the privilege
                // escalation here. The admin account may only come to exist
                // through the login-bootstrap flow in login().
                function ($attribute, $value, $fail) {
                    if (strtolower($value) === strtolower($this->adminEmail)) {
                        $fail('هذا البريد الإلكتروني محجوز. الرجاء استخدام صفحة تسجيل دخول المسؤول.');
                    }
                },
            ],
            'phone'    => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/', 'unique:users,phone'],
            'password' => ['required', 'string', Password::min(8)->uncompromised(), 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => strtolower($validated['email']),
            'phone'    => $validated['phone'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->respondWithRedirect($request, route('home'));
    }

    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $email = strtolower((string) $request->input('email'));

        $isAdminBootstrap = $this->needsAdminBootstrap($email);

        $rules = [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
        $validated = $request->validate($rules);

        if ($isAdminBootstrap) {
            try {
                DB::transaction(function () use ($email, $validated) {
                    $user = User::where('email', $email)->lockForUpdate()->first();

                    // Another request finished the setup first.
                    if ($user && $user->personal_pwd_initialized) {
                        return;
                    }

                    $user ??= new User([
                        'name'  => 'Admin',
                        'email' => $email,
                        'phone' => '0610101010',
                    ]);

                    $user->password = $validated['password'];
                    $user->personal_pwd_initialized = true;
                    $user->save();
                });
            } catch (QueryException $e) {
                // Lost a race on the unique email; fall through to Auth::attempt().
            }
        }
        $email = strtolower($validated['email']);

        $throttleKey = $email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "محاولات كثيرة جداً. حاول بعد {$seconds} ثانية.",
            ]);
        }

        $remember = $request->boolean('remember');

        if ($isAdminBootstrap) {
            try {
                User::create([
                    'name'     => 'Admin',
                    'email'    => $email,
                    'phone'    => '0610101010',
                    'password' => $validated['password'],
                ]);
            } catch (QueryException $e) {
                // Another request bootstrapped the admin account first (race
                // condition) — fall through to the normal Auth::attempt()
                // below, which will now correctly validate against whichever
                // request actually won the race.
            }
        }

        if (! Auth::attempt(['email' => $email, 'password' => $validated['password']], $remember)) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            throw ValidationException::withMessages([
                'password' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        // Only a real admin should ever land on admin.dashboard. A non-admin
        // user authenticating through this same form (register() allows
        // creating ordinary accounts) has no protected area to return to,
        // so send them home instead of into a guaranteed 403.
        $user = Auth::user();
        $target = $user->isAdmin()
            ? redirect()->intended(route('admin.dashboard'))->getTargetUrl()
            : route('home');

        return $this->respondWithRedirect($request, $target);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function respondWithRedirect(Request $request, string $url): RedirectResponse|JsonResponse
        {
            if ($request->expectsJson()) {
                return response()->json(['redirect' => $url]);
            }

            return redirect()->to($url);
    }

    private function needsAdminBootstrap(string $email): bool
    {
        if ($email !== strtolower($this->adminEmail)) {
            return false;
        }

        $user = User::where('email', $email)->first();

        return ! $user || ! $user->personal_pwd_initialized;
    }
}
