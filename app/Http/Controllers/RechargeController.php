<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class RechargeController extends Controller
{
    private const PLATFORMS = ['1xbet', 'melbet', 'linebet', 'paripulse'];

    private const MAX_ATTEMPTS = 3;

    private const DECAY_SECONDS = 300;

    private const MAX_IMAGE_KB = 5120;

    public function index()
    {
        return view('recharge', [
            'platforms' => self::PLATFORMS,
        ]);
    }

    public function store(Request $request)
    {
        $rateLimitKey = 'recharge-submit:' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            return back()
                ->withInput($request->except(['recharge_image', 'platform_screenshot']))
                ->with('error', 'لقد تجاوزت عدد المحاولات المسموح بها. المرجو إعادة المحاولة بعد ' . $seconds . ' ثانية.')
                ->with('retry_after', $seconds);
        }

        $validated = $request->validate([
            'montant' => ['required', 'numeric', 'gt:1'],
            'account_id' => ['required', 'string', 'max:50'],

            'recharge_code' => ['required', 'digits:16'],

            'whatsapp_number' => [
                'required',
                'string',
                'regex:/^(0[5-7][0-9]{8}|\+212[5-7][0-9]{8})$/',
            ],

            'platform' => ['required', 'string', Rule::in(self::PLATFORMS)],

            'recharge_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_IMAGE_KB,
            ],
            'platform_screenshot' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:' . self::MAX_IMAGE_KB,
            ],
        ], [
            'montant.gt' => 'يجب أن يكون المبلغ أكبر من 1 درهم.',
            'recharge_code.digits' => 'يجب أن يتكون كود التعبئة من 14 رقماً بالضبط.',
            'whatsapp_number.regex' => 'رقم الواتساب غير صحيح. استعمل صيغة 06XXXXXXXX أو +212XXXXXXXXX.',
            'platform.in' => 'المنصة المختارة غير صحيحة.',
            'recharge_image.required' => 'صورة إثبات التعبئة إجبارية.',
            'recharge_image.max' => 'حجم صورة التعبئة يجب أن لا يتجاوز 5 ميغا.',
            'recharge_image.uploaded' => 'فشل رفع صورة التعبئة، حاول مرة أخرى بصورة أصغر حجماً أو تحقق من اتصالك بالإنترنت.',
            'recharge_image.image' => 'صورة إثبات التعبئة غير صالحة.',
            'recharge_image.mimes' => 'صيغة صورة التعبئة يجب أن تكون JPG أو PNG أو WEBP.',
            'platform_screenshot.max' => 'حجم السكرين شوت يجب أن لا يتجاوز 5 ميغا.',
            'platform_screenshot.uploaded' => 'فشل رفع السكرين شوت، حاول مرة أخرى بصورة أصغر حجماً.',
            'platform_screenshot.image' => 'السكرين شوت غير صالح.',
            'platform_screenshot.mimes' => 'صيغة السكرين شوت يجب أن تكون JPG أو PNG أو WEBP.',
        ]);

        RateLimiter::hit($rateLimitKey, self::DECAY_SECONDS);

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::error('Telegram credentials are missing from config/services.php or .env.');

            return back()
                ->withInput($request->except(['recharge_image', 'platform_screenshot']))
                ->with('error', 'خطأ في إعدادات الخادم. المرجو التواصل مع الدعم.');
        }

        $message = "🔔 *طلب شحن جديد*\n\n" .
            "💰 المبلغ: {$validated['montant']}\n" .
            "🆔 ID الحساب: {$validated['account_id']}\n" .
            "🎟 الكود: {$validated['recharge_code']}\n" .
            "📞 الواتساب: {$validated['whatsapp_number']}\n" .
            "🎮 المنصة: " . strtoupper($validated['platform']);

        $media = [
            [
                'type' => 'photo',
                'media' => 'attach://recharge_image',
                'caption' => $message,
                'parse_mode' => 'Markdown',
            ],
        ];

        try {
            $http = Http::asMultipart()->timeout(20);

            if (app()->environment('local')) {
                $http = $http->withOptions(['verify' => false]);
            }

            $http = $http->attach(
                'recharge_image',
                file_get_contents($request->file('recharge_image')->getRealPath()),
                'recharge.jpg'
            );

            if ($request->hasFile('platform_screenshot')) {
                $media[] = ['type' => 'photo', 'media' => 'attach://platform_screenshot'];
                $http = $http->attach(
                    'platform_screenshot',
                    file_get_contents($request->file('platform_screenshot')->getRealPath()),
                    'screenshot.jpg'
                );
            }

            $response = $http->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($media),
            ]);

            if ($response->successful()) {
                return redirect()
                    ->route('recharge.form')
                    ->with('success', 'تم إرسال طلبك بنجاح. سيتم التواصل معك قريباً.');
            }

            Log::error('Telegram API responded with an error.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return back()
                ->withInput($request->except(['recharge_image', 'platform_screenshot']))
                ->with('error', 'حدث خطأ أثناء إرسال الطلب. حاول مرة أخرى أو تواصل معنا عبر واتساب.');
        } catch (ConnectionException $e) {
            Log::error('Could not reach the Telegram API.', ['message' => $e->getMessage()]);

            return back()
                ->withInput($request->except(['recharge_image', 'platform_screenshot']))
                ->with('error', 'تعذر الاتصال بخادم تيلغرام. تحقق من الاتصال بالإنترنت وحاول مجدداً.');
        }
    }
}
