<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\RechargeOrder;
use App\Models\User;
use App\Models\UserPlatformAccount;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class RechargeController extends Controller
{
    private const PLATFORMS = ['1xbet', 'melbet', 'paripulse', 'linebet'];

    private const ALLOWED_AMOUNTS = [5, 10, 20, 30, 50, 100, 200, 500, 1000];

    // max = 4 MB file, 3000x3000 px (GD needs ~4 bytes per pixel in memory)
    private const IMAGE_RULES = [
        'image',
        'mimes:jpeg,png,jpg,webp',
        'max:4096',
        'dimensions:max_width=3000,max_height=3000',
    ];

    public function index()
    {
        $user = Auth::user();
        $savedPlatforms = $user
            ? $user->platformAccounts()->orderBy('platform')->get()
            : collect();

        return view('recharge', [
            'platforms' => self::PLATFORMS,
            'savedPlatforms' => $savedPlatforms,
            'canRepeatRecharge' => $savedPlatforms->isNotEmpty(),
        ]);
    }

    public function store(Request $request)
    {
        // The per-IP limit is now a real one: ->middleware('throttle:5,1') on the
        // POST route (see routes). The old session-based check was bypassable by
        // dropping the cookie, so it was removed.

        $user = $request->user();

        $mode = 'new';
        if ($user) {
            $hasSaved = $user->platformAccounts()->exists();
            $mode = $request->input('recharge_mode', $hasSaved ? 'existing' : 'new');

            if ($mode === 'existing' && ! $hasSaved) {
                return $this->backWithError('لا توجد منصات محفوظة. استخدم تعبئة منصة جديدة.', $request);
            }
        }

        return $mode === 'existing'
            ? $this->storeExistingPlatformRecharge($request, $user)
            : $this->storeNewPlatformRecharge($request, $user);
    }

    private function storeExistingPlatformRecharge(Request $request, User $user)
    {
        $accounts = $user->platformAccounts()->get();
        $savedPlatforms = $accounts->pluck('platform')->all();

        $rules = [
            'montant' => ['required', 'numeric', 'in:' . implode(',', self::ALLOWED_AMOUNTS)],
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'recharge_image' => array_merge(['required'], self::IMAGE_RULES),
            'recharge_mode' => ['required', 'in:existing'],
        ];

        if (count($savedPlatforms) > 1) {
            $rules['saved_platform'] = ['required', 'string', Rule::in($savedPlatforms)];
        }

        $validated = $request->validate($rules, $this->messages());

        $platform = count($savedPlatforms) === 1
            ? $savedPlatforms[0]
            : $validated['saved_platform'];

        $platformKey = strtolower($platform);
        $account = $accounts->first(fn ($row) => strtolower($row->platform) === $platformKey);

        if (! $account) {
            return $this->backWithError('المنصة المختارة غير موجودة في حسابك.', $request);
        }

        $payload = [
            'montant' => (int) $validated['montant'],
            'account_id' => $account->account_id,
            'fullName' => $account->full_name,
            'recharge_code' => $validated['recharge_code'],
            'platform' => strtolower($account->platform),
        ];

        $message = $this->buildMessage('طلب شحن (حساب موجود)', $payload, $user);

        return $this->finalizeRecharge(
            $request,
            $payload,
            $message,
            $user,
            true,
            $request->file('recharge_image'),
            null
        );
    }

    private function storeNewPlatformRecharge(Request $request, ?User $user)
    {
        $validated = $request->validate([
            'montant' => ['required', 'numeric', 'min:1.01'],
            'account_id' => ['required', 'string', 'max:255'],
            'fullName' => ['required', 'string', 'min:3', 'max:12'], // تم تحديث الحد الأقصى إلى 12 حرف
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'platform' => ['required', 'string', Rule::in(self::PLATFORMS)],
            'recharge_image' => array_merge(['required'], self::IMAGE_RULES),
            'platform_screenshot' => array_merge(['nullable'], self::IMAGE_RULES),
            'recharge_mode' => ['nullable', 'in:new,existing'],
        ], $this->messages());

        $payload = [
            'montant' => (int) $validated['montant'],
            'account_id' => $validated['account_id'],
            'fullName' => $validated['fullName'],
            'recharge_code' => $validated['recharge_code'],
            'platform' => strtolower($validated['platform']),
        ];

        $message = $this->buildMessage(
            'طلب شحن جديد',
            $payload,
            $user,
            $user ? '📌 <b>منصة جديدة / تعبئة كاملة</b>' : null
        );

        return $this->finalizeRecharge(
            $request,
            $payload,
            $message,
            $user,
            false,
            $request->file('recharge_image'),
            $request->file('platform_screenshot')
        );
    }

    private function finalizeRecharge(
        Request $request,
        array $payload,
        string $message,
        ?User $user,
        bool $isRepeat,
        UploadedFile $rechargeImage,
        ?UploadedFile $screenshot = null,
    ) {
        $token = config('services.telegram.bot_token');
        $chatId = $user
            ? config('services.telegram.recharge_chat_id')
            : config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::error('Telegram credentials are missing from config/services.php or .env.');

            return $this->backWithError('خطأ في إعدادات الخادم. المرجو التواصل مع الدعم.', $request);
        }

        $codeHash = $this->hashRechargeCode($payload['recharge_code']);

        // Fast path: this code was already submitted.
        if (RechargeOrder::where('code_hash', $codeHash)->exists()) {
            return $this->duplicateCodeResponse($request);
        }

        // 1) Process images first: a bad image must not create an order.
        try {
            $imageBinary = $this->compressImage($rechargeImage);
            $screenshotBinary = $screenshot ? $this->compressImage($screenshot) : null;
        } catch (Throwable $e) {
            Log::warning('Recharge image processing failed: ' . $e->getMessage());

            return $this->backWithError('تعذر معالجة الصورة. تأكد أنها صورة صالحة وحاول مرة أخرى.', $request);
        }

        // 2) Reserve the order BEFORE calling Telegram. The unique index on
        //    code_hash makes a double-click or a replayed code fail here.
        try {
            $order = new RechargeOrder([
                'user_id' => $user?->id,
                'montant' => $payload['montant'],
                'account_id' => $payload['account_id'],
                'platform' => $payload['platform'],
                'full_name' => $payload['fullName'],
                'is_repeat' => $isRepeat,
            ]);
            $order->forceFill(['code_hash' => $codeHash, 'status' => 'pending'])->save();
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) { // MySQL duplicate entry
                return $this->duplicateCodeResponse($request);
            }

            Log::error('Recharge DB error while creating order: ' . $e->getMessage());

            return $this->backWithError('حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.', $request);
        }

        // 3) Send to Telegram.
        if (! $this->sendToTelegram($token, $chatId, $message, $imageBinary, $screenshotBinary)) {
            // Free the code so the user can retry with the same one.
            $order->delete();

            return $this->backWithError(
                'حدث خطأ أثناء إرسال الطلب. حاول مرة أخرى أو تواصل معنا عبر واتساب.',
                $request
            );
        }

        // 4) Mark as sent and remember the platform account (failures here are
        //    logged but never shown as an error: the order did reach Telegram).
        try {
            $order->forceFill(['status' => 'sent', 'telegram_sent_at' => now()])->save();

            if ($user && ! $isRepeat) {
                UserPlatformAccount::updateOrCreate(
                    ['user_id' => $user->id, 'platform' => $payload['platform']],
                    ['account_id' => $payload['account_id'], 'full_name' => $payload['fullName']]
                );
            }
        } catch (Throwable $e) {
            Log::error('Recharge post-send DB error: ' . $e->getMessage());
        }

        return back()->with('success', 'تم إرسال طلبك بنجاح. سيتم التواصل معك قريباً.');
    }

    /**
     * Sends one photo (sendPhoto) or two (sendMediaGroup, which requires 2-10 items).
     * Never lets the bot token reach the logs.
     */
    private function sendToTelegram(
        string $token,
        string $chatId,
        string $caption,
        string $imageBinary,
        ?string $screenshotBinary = null,
    ): bool {
        try {
            $http = Http::asMultipart()->timeout(30);

            if ($screenshotBinary) {
                $media = [
                    [
                        'type' => 'photo',
                        'media' => 'attach://recharge_image',
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                    ],
                    ['type' => 'photo', 'media' => 'attach://platform_screenshot'],
                ];

                $response = $http
                    ->attach('recharge_image', $imageBinary, 'recharge.jpg')
                    ->attach('platform_screenshot', $screenshotBinary, 'screenshot.jpg')
                    ->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                        'chat_id' => $chatId,
                        'media' => json_encode($media),
                    ]);
            } else {
                $response = $http
                    ->attach('photo', $imageBinary, 'recharge.jpg')
                    ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                    ]);
            }

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram API responded with an error.', [
                'status' => $response->status(),
                'body' => $this->redact($response->body(), $token),
            ]);
        } catch (Throwable $e) {
            // Do NOT pass $e in the log context: its message contains the full
            // request URL, and therefore the bot token.
            Log::error('Telegram request failed: ' . $this->redact($e->getMessage(), $token));
        }

        return false;
    }

    private function compressImage(UploadedFile $file): string
    {
        $path = $file->getRealPath();
        $info = $path ? @getimagesize($path) : false;

        if ($info === false) {
            throw new RuntimeException('Not a valid image.');
        }

        [$width, $height, $type] = $info;

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default => false,
        };

        if (! $src) {
            throw new RuntimeException('Unsupported or corrupt image.');
        }

        $scale = min(1, 1200 / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // JPEG has no alpha channel: paint white first so transparent PNGs
        // don't turn black.
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($dst, null, 75);
        $binary = ob_get_clean();

        unset($src, $dst);

        if (! is_string($binary) || $binary === '') {
            throw new RuntimeException('Image encoding failed.');
        }

        return $binary;
    }

    /** HMAC so duplicates can be detected without storing the real code. */
    private function hashRechargeCode(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    /** Escape for Telegram parse_mode=HTML (only & < > need escaping). */
    private function h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_NOQUOTES, 'UTF-8');
    }

    private function buildMessage(string $title, array $payload, ?User $user, ?string $footer = null): string
    {
        $lines = ["🔔 <b>{$title}</b>", ''];

        if ($user) {
            $lines[] = '👤 المستخدم: <code>' . $this->h($user->name) . '</code> (' . $this->h($user->email) . ')';
        }

        $lines[] = '💰 المبلغ: <code>' . (int) $payload['montant'] . ' DH</code>';
        $lines[] = '🆔 ID الحساب: <code>' . $this->h($payload['account_id']) . '</code>';
        $lines[] = '👤 الاسم الكامل: <code>' . $this->h($payload['fullName']) . '</code>';
        $lines[] = '🎟 الكود: <code>' . $this->h($payload['recharge_code']) . '</code>';
        $lines[] = '🎮 المنصة: <code>' . $this->h(strtoupper($payload['platform'])) . '</code>';

        if ($footer) {
            $lines[] = '';
            $lines[] = $footer;
        }

        return implode("\n", $lines);
    }

    /** Removes the bot token (and anything shaped like one) from a string. */
    private function redact(string $text, string $token): string
    {
        $text = str_replace($token, '[redacted-token]', $text);

        return preg_replace('/bot\d+:[A-Za-z0-9_-]+/', 'bot[redacted-token]', $text) ?? $text;
    }

    /** Never flash the recharge code (or files) back into the session. */
    private function safeInput(Request $request): array
    {
        return $request->except(['recharge_code', 'recharge_image', 'platform_screenshot']);
    }

    private function backWithError(string $message, Request $request)
    {
        return back()->with('error', $message)->withInput($this->safeInput($request));
    }

    private function duplicateCodeResponse(Request $request)
    {
        return $this->backWithError('كود التعبئة هذا تم إرساله مسبقاً.', $request);
    }

    private function messages(): array
    {
        return [
            'montant.required' => 'المبلغ إجباري.',
            'montant.in' => 'يرجى اختيار مبلغ صالح من القائمة.',
            'account_id.required' => 'ID الحساب إجباري.',
            'account_id.regex' => 'ID الحساب يجب أن يتكون من 7 إلى 13 رقم.',
            'fullName.required' => 'الاسم الكامل إجباري.',
            'fullName.min' => 'الاسم الكامل يجب أن يحتوي على 3 أحرف على الأقل.',
            'fullName.max' => 'الاسم الكامل يجب ألا يتجاوز 15 حرفاً.',
            'recharge_code.required' => 'كود التعبئة إجباري.',
            'recharge_code.size' => 'يجب أن يتكون كود التعبئة من 16 رقماً بالضبط.',
            'recharge_code.regex' => 'كود التعبئة يجب أن يتكون من أرقام فقط.',
            'platform.required' => 'يرجى اختيار المنصة.',
            'saved_platform.required' => 'يرجى اختيار المنصة.',
            'recharge_image.required' => 'صورة إثبات التعبئة إجبارية.',
            'recharge_image.image' => 'الملف يجب أن يكون صورة صالحة.',
            'recharge_image.max' => 'الحد الأقصى لحجم الصورة هو 4 ميجابايت.',
            'recharge_image.dimensions' => 'أبعاد الصورة كبيرة جداً (الحد الأقصى 3000×3000).',
            'platform_screenshot.image' => 'الملف يجب أن يكون صورة صالحة.',
            'platform_screenshot.max' => 'الحد الأقصى لحجم الصورة هو 4 ميجابايت.',
            'platform_screenshot.dimensions' => 'أبعاد الصورة كبيرة جداً (الحد الأقصى 3000×3000).',
        ];
    }
}
