<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RechargeController extends Controller
{
    public function index()
    {
        $platforms = ['1xbet', 'melbet', 'paripulse', 'linebet'];
        return view('recharge', compact('platforms'));
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

        // تم توسيع نوع الملفات المسموحة
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

        try {
            $imagePath = null;
            if ($request->hasFile('recharge_image')) {
                $image = $request->file('recharge_image');
                $imagePath = $this->compressAndStoreImage($image, 'recharges');
            }

            $screenshotPath = null;
            if ($request->hasFile('platform_screenshot')) {
                $screenshot = $request->file('platform_screenshot');
                $screenshotPath = $this->compressAndStoreImage($screenshot, 'screenshots');
            }

            session([$ipKey => time() + 60]);

            return back()->with('success', 'تم إرسال طلب الشحن بنجاح، سيتم مراجعته قريباً.');

        } CATCH (\Exception $e) {
            Log::error('Recharge Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.')->withInput();
        }
    }

    /**
     * دالة لضغط الصور الكبيرة وتصغير حجمها باستخدام PHP الأصلي لتتخطى أي قيود
     */
    private function compressAndStoreImage($file, $folder)
    {
        $filename = Str::uuid() . '.jpg';
        $destinationPath = storage_path('app/public/' . $folder);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filePath = $destinationPath . '/' . $filename;
        $tmpPath = $file->getRealPath();

        // قراءة أبعاد الصورة ونوعها
        list($width, $height, $type) = getimagesize($tmpPath);

        // تصغير الأبعاد الكبيرة إذا تجاوزت 1200 بكسل لتخفيف الحجم
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

        // إنشاء صورة جديدة بالأبعاد الجديدة
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($tmpPath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($tmpPath);
                // الحفاظ على الشفافية في الصور الشفافة
                imagealphablending($dstImage, false);
                imagesavealpha($dstImage, true);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = imagecreatefromwebp($tmpPath);
                break;
            default:
                return $file->store($folder, 'public');
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // حفظ الصورة بجودة 75% (وهي جودة ممتازة وواضحة جداً وتصغر الحجم بشكل هائل)
        imagejpeg($dstImage, $filePath, 75);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $folder . '/' . $filename;
    }
}
