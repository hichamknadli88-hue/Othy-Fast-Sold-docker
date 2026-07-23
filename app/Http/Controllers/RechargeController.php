<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RechargeController extends Controller
{
    // الحد الأقصى لحجم الصورة بالكيلوبايت (25 ميجابايت = 25600 KB)
    private const MAX_IMAGE_KB = 25600;

    public function index()
    {
        // قائمة المنصات المتاحة محدثة بـ Paripulse
        $platforms = ['1xbet', 'melbet', 'paripulse', 'linebet'];
        return view('recharge', compact('platforms'));
    }

    public function store(Request $request)
    {
        // رفع الذاكرة المؤقتة ووقت التنفيذ لمعالجة الصور الكبيرة بسلاسة
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        // نظام الحماية من التكرار المؤقت (Rate Limiting based on IP)
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

        // التحقق من المدخلات مع رفع الحد الأقصى للصورة إلى 25 ميغابايت
        $validated = $request->validate([
            'montant' => ['required', 'numeric', 'min:1.01'],
            'account_id' => ['required', 'string', 'max:255'],
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'platform' => ['required', 'string'],
            'recharge_image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:' . self::MAX_IMAGE_KB],
            'platform_screenshot' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:' . self::MAX_IMAGE_KB],
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
            'recharge_image.mimes' => 'صورة الإثبات يجب أن تكون من نوع: jpeg, png, webp.',
            'recharge_image.max' => 'حجم صورة الإثبات يتجاوز الحد المسموح (25 ميجابايت).',
            'platform_screenshot.image' => 'السكرين شوت يجب أن يكون صورة.',
            'platform_screenshot.mimes' => 'السكرين شوت يجب أن يكون من نوع: jpeg, png, webp.',
            'platform_screenshot.max' => 'حجم السكرين شوت يتجاوز الحد المسموح (25 ميجابايت).',
        ]);

        try {
            // تخزين صورة إثبات التعبئة بشكل آمن في التخزين العام
            $imagePath = null;
            if ($request->hasFile('recharge_image')) {
                $imagePath = $request->file('recharge_image')->store('recharges', 'public');
            }

            // تخزين السكرين شوت إن وُجد
            $screenshotPath = null;
            if ($request->hasFile('platform_screenshot')) {
                $screenshotPath = $request->file('platform_screenshot')->store('screenshots', 'public');
            }

            // وضع فترة حماية للمستخدم (60 ثانية قبل الإرسال مرة أخرى)
            session([$ipKey => time() + 60]);

            return back()->with('success', 'تم إرسال طلب الشحن بنجاح، سيتم مراجعته قريباً.');

        } catch (\Exception $e) {
            Log::error('Recharge Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.')->withInput();
        }
        } catch (\Exception $e) {
            Log::error('Recharge Error: ' . $e->getMessage());
            
            // قم بتعطيل هذا السطر مؤقتاً لترى الخطأ الحقيقي على الشاشة
            dd($e->getMessage(), $e->getFile(), $e->getLine());
            
            return back()->with('error', 'حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.')->withInput();
        }
    }
}
