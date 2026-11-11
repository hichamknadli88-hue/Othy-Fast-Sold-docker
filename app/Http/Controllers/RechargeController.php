<?php

namespace App\Http\Controllers;

use App\Models\RechargeOrder;
use App\Models\UserPlatformAccount;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RechargeController extends Controller
{
    private const PLATFORMS = ['1xbet', 'melbet', 'paripulse', 'linebet'];

    public function index(Request $request)
    {
        $user = $request->user();

        $savedPlatforms = $user
            ? UserPlatformAccount::where('user_id', $user->id)->orderBy('platform')->get()
            : collect();

        return view('recharge', [
            'platforms' => self::PLATFORMS,
            'savedPlatforms' => $savedPlatforms,
            'canRepeatRecharge' => $savedPlatforms->isNotEmpty(),
        ]);
    }

    public function success()
    {
        if (!session('order')) {
            return redirect()->route('recharge.form');
        }

        session()->keep(['order']);

        return view('recharge-success');
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $user = $request->user();

        $ipKey = 'recharge_ip_' . $request->ip();
        if (session()->has($ipKey)) {
            $secondsLeft = session($ipKey) - time();
            if ($secondsLeft > 0) {
                return back()->with([
                    'error' => 'الرجاء الانتظار قليلاً قبل إرسال طلب آخر.',
                    'retry_after' => $secondsLeft,
                ])->withInput();
            }
        }

        // Mode "existing" kaykhdem ghir l user connecté
        $mode = ($user && $request->input('recharge_mode') === 'existing') ? 'existing' : 'new';

        $rules = [
            'montant' => ['required', 'integer', 'min:2'],
            'recharge_code' => ['required', 'string', 'size:16', 'regex:/^[0-9]{16}$/'],
            'recharge_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp'],
        ];

        if ($mode === 'new') {
            $rules += [
                'account_id' => ['required', 'string', 'regex:/^[0-9]{7,13}$/'],
                'fullName' => ['required', 'string', 'min:3', 'max:15'],
                'platform' => ['required', 'string', Rule::in(self::PLATFORMS)],
                'platform_screenshot' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp'],
            ];
        } else {
            $rules['saved_platform'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules, [
            'montant.required' => 'المبلغ إجباري.',
            'montant.integer' => 'المبلغ غير صالح.',
            'montant.min' => 'يجب أن يكون المبلغ أكبر من 1 درهم.',
            'account_id.required' => 'ID الحساب إجباري.',
            'account_id.regex' => 'ID الحساب يجب أن يتكون من 7 إلى 13 رقماً.',
            'fullName.required' => 'الاسم الكامل إجباري.',
            'fullName.min' => 'الاسم الكامل يجب أن يحتوي على 3 أحرف على الأقل.',
            'fullName.max' => 'الاسم الكامل يجب ألا يتجاوز 15 حرفاً.',
            'recharge_code.required' => 'كود التعبئة إجباري.',
            'recharge_code.size' => 'يجب أن يتكون كود التعبئة من 16 رقماً بالضبط.',
            'recharge_code.regex' => 'كود التعبئة يجب أن يتكون من أرقام فقط.',
            'platform.required' => 'يرجى اختيار المنصة.',
            'platform.in' => 'المنصة غير صالحة.',
            'recharge_image.required' => 'صورة إثبات التعبئة إجبارية.',
            'recharge_image.image' => 'الملف يجب أن يكون صورة صالحاً.',
        ]);

        if ($mode === 'existing') {
            $saved = UserPlatformAccount::where('user_id', $user->id)->get();

            if ($saved->isEmpty()) {
                return back()
                    ->with('error', 'لا يوجد أي حساب محفوظ. اختر "تعبئة منصة جديدة".')
                    ->withInput();
            }

            $account = $saved->count() === 1
                ? $saved->first()
                : $saved->firstWhere('platform', $request->input('saved_platform'));

            if (!$account) {
                return back()
                    ->withErrors(['saved_platform' => 'يرجى اختيار المنصة المحفوظة.'])
                    ->withInput();
            }

            // ID/ism/platform kay-jiw mn DB, ma kan3tamdoush 3la l client
            $accountId = $account->account_id;
            $fullName = $account->full_name;
            $platform = $account->platform;
        } else {
            $accountId = trim($validated['account_id']);
            $fullName = trim($validated['fullName']);
            $platform = $validated['platform'];
        }

        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            Log::error('Telegram credentials are missing from config/services.php or .env.');
            return back()->with('error', 'خطأ في إعدادات الخادم. المرجو التواصل مع الدعم.')->withInput();
        }

        $order = null;
        $rechargeStream = null;
        $screenshotStream = null;

        try {
            $imageBinary = $this->compressImage($request->file('recharge_image'));

            $screenshotBinary = null;
            if ($mode === 'new' && $request->hasFile('platform_screenshot')) {
                $screenshotBinary = $this->compressImage($request->file('platform_screenshot'));
            }

            $codeHash = hash_hmac('sha256', $validated['recharge_code'], (string) config('app.key'));

            $order = RechargeOrder::create([
                'user_id' => $user?->id,
                'montant' => (int) $validated['montant'],
                'account_id' => $accountId,
                'platform' => $platform,
                'full_name' => $fullName,
                'is_repeat' => $mode === 'existing',
                'status' => 'pending',
                'code_hash' => $codeHash,
            ]);

            $reference = 'WIN-' . strtoupper(substr($order->id, -8));

            $esc = fn ($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
            $typeLabel = $mode === 'existing' ? 'تعبئة حساب موجود' : 'منصة جديدة';

            $message = "🔔 <b>طلب شحن جديد</b>\n\n" .
                "📌 <b>المرجع:</b> <code>{$reference}</code>\n" .
                "🔁 <b>النوع:</b> {$typeLabel}\n" .
                "💰 <b>المبلغ:</b> <code>{$esc($validated['montant'])} DH</code>\n" .
                "🆔 <b> الحساب:</b> <code>{$esc($accountId)}</code>\n" .
                "👤 <b>الاسم الكامل:</b> <code>{$esc($fullName)}</code>\n" .
                "🎟 <b>الكود:</b> <code>{$esc($validated['recharge_code'])}</code>\n" .
                "🎮 <b>المنصة:</b> <code>{$esc(strtoupper($platform))}</code>" .
                ($user ? "\n🙋 <b>المستخدم:</b> <code>{$esc($user->name)}</code>" : '');

            $media = [
                [
                    'type' => 'photo',
                    'media' => 'attach://recharge_image',
                    'caption' => $message,
                    'parse_mode' => 'HTML',
                ],
            ];

            if ($screenshotBinary) {
                $media[] = [
                    'type' => 'photo',
                    'media' => 'attach://platform_screenshot',
                ];
            }

            $rechargeStream = fopen('php://temp', 'r+');
            fwrite($rechargeStream, $imageBinary);
            rewind($rechargeStream);

            $http = Http::timeout(60)
                ->attach('recharge_image', $rechargeStream, 'recharge.jpg');

            if ($screenshotBinary) {
                $screenshotStream = fopen('php://temp', 'r+');
                fwrite($screenshotStream, $screenshotBinary);
                rewind($screenshotStream);

                $http->attach('platform_screenshot', $screenshotStream, 'screenshot.jpg');
            }

            $response = $http->post("https://api.telegram.org/bot{$token}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($media),
            ]);

            if ($response->successful()) {
                $order->update(['status' => 'sent', 'telegram_sent_at' => now()]);

                // Mode "new" + user connecté => kansavi l compte
                if ($mode === 'new' && $user) {
                    UserPlatformAccount::updateOrCreate(
                        ['user_id' => $user->id, 'platform' => $platform],
                        ['account_id' => $accountId, 'full_name' => $fullName]
                    );
                }

                session([$ipKey => time() + 60]);

                return redirect()
                    ->route('recharge.success')
                    ->with('order', [
                        'reference' => $reference,
                        'amount' => $validated['montant'],
                        'platform' => strtoupper($platform),
                    ]);
            }

            Log::error('Telegram API responded with an error:', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            $this->releaseOrder($order);

            return back()
                ->with('error', 'حدث خطأ أثناء إرسال الطلب. حاول مرة أخرى أو تواصل معنا عبر واتساب.')
                ->withInput();

        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withErrors(['recharge_code' => 'هذا الكود تم استعماله من قبل.'])
                ->withInput();
        } catch (\Throwable $e) {
            if ($order) {
                $this->releaseOrder($order);
            }

            Log::error('Recharge Error: ' . str_replace((string) $token, '***', $e->getMessage()));

            return back()
                ->with('error', 'حدث خطأ غير متوقع أثناء إرسال الطلب، يجب المحاولة لاحقاً.')
                ->withInput();
        } finally {
            if (is_resource($rechargeStream)) {
                fclose($rechargeStream);
            }
            if (is_resource($screenshotStream)) {
                fclose($screenshotStream);
            }
        }
    }

    // Telegram t3attal: kan7errer l code bach l client y9der y3awd
    private function releaseOrder(RechargeOrder $order): void
    {
        $order->update(['status' => 'failed', 'code_hash' => null]);
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
