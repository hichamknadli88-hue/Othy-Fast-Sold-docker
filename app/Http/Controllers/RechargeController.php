<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'platform' => ['required', 'string'],
            'recharge_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp'],
            'platform_screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp'],
        ], [
            'montant.required' => 'المبلغ إجباري.',
            'montant.min' => 'يجب أن يكون المبلغ أكبر من 1 درهم.',
            'account_id.required' => 'ID الحساب إجباري.',
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
            // ضغط الصور في الذاكرة مباشرة (بدون تخزينها في storage)
            $imageBinary = $this->compressImage($request->file('recharge_image'));

            $screenshotBinary = null;
            if ($request->hasFile('platform_screenshot')) {
                $screenshotBinary = $this->compressImage($request->file('platform_screenshot'));
            }

            $message = "🔔 *طلب شحن جديد*\n\n" .
                "💰 المبلغ: {$validated['montant']}\n" .
                "🆔 ID الحساب: {$validated['account_id']}\n" .
                "🎟 الكود: {$validated['recharge_code']}\n" .
                "🎮 المنصة: " . strtoupper($validated['platform']);

            $media = [
                [
                    'type' => 'photo',
                    'media' => 'attach://recharge_image',
                    'caption' => $message,
                    'parse_mode' => 'Markdown',
                ],
            ];

            $http = Http::asMultipart()->timeout(30)
                ->attach('recharge_image', $imageBinary, 'recharge.jpg');

            if ($screenshotBinary) {
                $media[] = ['type' => 'photo', 'media' => 'attach://platform_screenshot'];
                $http = $http->attach('platform_screenshot', $screenshotBinary, 'screenshot.jpg');
            }

            $response = $http->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($media),
            ]);

            if ($response->successful()) {
                session([$ipKey => time() + 60]);
                return back()->with('success', 'تم إرسال طلبك بنجاح. سيتم التواصل معك قريباً.');
            }

            Log::error('Telegram API responded with an error.', [
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

    /**
     * تضغط الصورة وتعيد المحتوى الثنائي (binary) جاهزاً للإرسال مباشرة لتيليغرام
     * دون الحاجة لحفظها في storage (لا حاجة لقاعدة بيانات أو تخزين دائم)
     */
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
