<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RechargeController extends Controller
{
    private const PLATFORMS = ['1xbet', 'melbet', 'paripulse', 'linebet'];

    public function index()
    {
        return view('recharge', ['platforms' => self::PLATFORMS]);
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $ipKey = 'recharge_ip_' . $request->ip();
        if (session()->has($ipKey)) {
            $secondsLeft = session($ipKey) - time();
            if ($secondsLeft > 0) {
                return back()->with([
                    'error' => 'الرجاء الانتظار قليلاً قبل إرسال طلب آخر.',
                    'retry_after' => $secondsLeft
                ])->withInput();
            }
        }

        $validated = $request->validate([
            'montant' => ['required', 'numeric', 'min:1.01'],
            'account_id' => ['required', 'string', 'max:255'],
            'fullName' => ['required', 'string', 'min:3', 'max:12'],
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'platform' => ['required', 'string'],
            'recharge_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp'],
            'platform_screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp'],
        ], [
            'montant.required' => 'المبلغ إجباري.',
            'montant.min' => 'يجب أن يكون المبلغ أكبر من 1 درهم.',
            'account_id.required' => 'ID الحساب إجباري.',
            'fullName.required' => 'الاسم الكامل إجباري.',
            'fullName.min' => 'الاسم الكامل يجب أن يحتوي على 3 أحرف على الأقل.',
            'fullName.max' => 'الاسم الكامل يجب ألا يتجاوز 12 حرفاً.',
            'recharge_code.required' => 'كود التعبئة إجباري.',
            'recharge_code.size' => 'يجب أن يتكون كود التعبئة من 16 رقماً بالضبط.',
            'recharge_code.regex' => 'كود التعبئة يجب أن يتكون من أرقام فقط.',
            'platform.required' => 'يرجى اختيار المنصة.',
            'recharge_image.required' => 'صورة إثبات التعبئة إجبارية.',
            'recharge_image.image' => 'الملف يجب أن يكون صورة صالحاً.',
        ]);

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::error('Telegram credentials are missing from config/services.php or .env.');
            return back()->with('error', 'خطأ في إعدادات الخادم. المرجو التواصل مع الدعم.')->withInput();
        }

        try {
            $imageBinary = $this->compressImage($request->file('recharge_image'));

            $screenshotBinary = null;
            if ($request->hasFile('platform_screenshot')) {
                $screenshotBinary = $this->compressImage($request->file('platform_screenshot'));
            }

            // Sanitize values for HTML mode to prevent parsing exceptions
            $montant = htmlspecialchars($validated['montant'], ENT_QUOTES, 'UTF-8');
            $accountId = htmlspecialchars($validated['account_id'], ENT_QUOTES, 'UTF-8');
            $fullName = htmlspecialchars($validated['fullName'], ENT_QUOTES, 'UTF-8');
            $code = htmlspecialchars($validated['recharge_code'], ENT_QUOTES, 'UTF-8');
            $platform = htmlspecialchars(strtoupper($validated['platform']), ENT_QUOTES, 'UTF-8');

            // Caption attached to the primary photo
            $message = "🔔 <b>طلب شحن جديد</b>\n\n" .
                "💰 <b>المبلغ:</b> <code>{$montant} DH</code>\n" .
                "🆔 <b>ID الحساب:</b> <code>{$accountId}</code>\n" .
                "👤 <b>الاسم الكامل:</b> <code>{$fullName}</code>\n" .
                "🎟 <b>الكود:</b> <code>{$code}</code>\n" .
                "🎮 <b>المنصة:</b> <code>{$platform}</code>";

            $media = [
                [
                    'type' => 'photo',
                    'media' => 'attach://recharge_image',
                    'caption' => $message,
                    'parse_mode' => 'HTML',
                ],
            ];

            // Build multipart payload reliably
            $httpRequest = Http::timeout(60)
                ->attach('recharge_image', $imageBinary, 'recharge.jpg');

            if ($screenshotBinary) {
                $media[] = [
                    'type' => 'photo',
                    'media' => 'attach://platform_screenshot',
                ];
                $httpRequest = $httpRequest->attach('platform_screenshot', $screenshotBinary, 'screenshot.jpg');
            }

            $response = $httpRequest->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($media),
            ]);

            if ($response->successful()) {
                session([$ipKey => time() + 60]);
                return back()->with('success', 'تم إرسال طلبك بنجاح. سيتم التواصل معك قريباً.');
            }

            Log::error('Telegram API error response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return back()
                ->with('error', 'حدث خطأ أثناء إرسال الطلب. حاول مرة أخرى أو تواصل معنا عبر واتساب.')
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Recharge Error: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.')
                ->withInput();
        }
    }

    private function compressImage($file): string
    {
        $tmpPath = $file->getRealPath();
        list($width, $height, $type) = getimagesize($tmpPath);

        $maxDimension = 1200;
        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = intval($height * ($maxDimension / $width));
            } else {
                $newHeight = $maxDimension;
                $newWidth = intval($width * ($maxDimension / $height));
            }
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($tmpPath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($tmpPath);
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = imagecreatefromwebp($tmpPath);
                break;
            default:
                return file_get_contents($tmpPath);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($dstImage, null, 75);
        $binary = ob_get_clean();

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $binary;
    }
}
