<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إرسال طلب الشحن | OTHY FAST SOLD</title>
    <!-- استبدال Tailwind CDN بـ CSS رسمي مباشر لتجنب مشاكل عدم ظهور الستايل -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('1784465709672.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #020617; color: #f8fafc; }
        .glass-card { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .field-ok input, .field-ok textarea { border-color: #16a34a !important; }
        .field-error input, .field-error textarea { border-color: #dc2626 !important; }
        .spin { animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .platform-card input:checked + label { border-color: #2563eb; background: rgba(37,99,235,0.2); box-shadow: 0 0 0 2px rgba(37,99,235,0.4); }
    </style>
</head>
<body class="min-h-screen">

    <!-- Nav bar -->
    <nav class="w-full border-b border-gray-800 bg-gray-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-lg font-black tracking-tight text-white">
                OTHY <span class="text-blue-400">FAST SOLD</span>
            </a>
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-blue-400 text-sm inline-flex items-center gap-2 font-bold">
                العودة للصفحة الرئيسية <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto p-4 md:p-10">

        <div class="glass-card p-6 md:p-8 rounded-3xl shadow-xl">
            <h2 class="text-3xl font-black text-center mb-2 text-white">
                <span class="text-blue-400">OTHY</span> نموذج طلب الشحن
            </h2>
            <p class="text-gray-400 text-center text-sm mb-8">عبّي المعلومات بدقة، الطلب كيتصيفط مباشرة لفريقنا.</p>

            {{-- رسالة النجاح --}}
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/30 text-green-400 p-4 rounded-xl mb-6 text-center font-bold flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            {{-- رسالة الخطأ العامة --}}
            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl mb-6 text-center font-bold flex flex-col items-center gap-2">
                    <span><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</span>
                    @if(session('retry_after'))
                        <span id="retry-countdown" class="text-xs font-mono text-red-300" data-seconds="{{ session('retry_after') }}"></span>
                    @endif
                </div>
            @endif

            {{-- أخطاء التحقق --}}
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl mb-6 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('recharge.store') }}" method="POST" enctype="multipart/form-data" id="rechargeForm" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-1">
                    {{-- المبلغ --}}
                    <div class="mb-5" id="group-montant">
                        <label for="montant" class="block mb-2 text-sm font-semibold text-gray-300">المبلغ (Amount) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" inputmode="decimal" step="0.01" min="1.01" id="montant" name="montant" value="{{ old('montant') }}"
                                   placeholder="مثال: 50"
                                   class="w-full h-12 p-3 pl-10 rounded-xl bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500 transition"
                                   required>
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">DH</span>
                        </div>
                        <p class="hint text-xs mt-1.5 text-gray-400">يجب أن يكون المبلغ أكبر من 1 درهم.</p>
                    </div>

                    {{-- ID الحساب --}}
                    <div class="mb-5" id="group-account_id">
                        <label for="account_id" class="block mb-2 text-sm font-semibold text-gray-300">ID الحساب <span class="text-red-500">*</span></label>
                        <input type="text" id="account_id" name="account_id" value="{{ old('account_id') }}"
                               placeholder="مثال: 123456789"
                               class="w-full h-12 p-3 rounded-xl bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500 transition"
                               required>
                        <p class="hint text-xs mt-1.5 text-gray-400">تأكد من ID الحساب قبل الإرسال.</p>
                    </div>
                </div>

                {{-- كود التعبئة (تم تعديله إلى 16 رقم) --}}
                <div class="mb-5" id="group-recharge_code">
                    <label for="recharge_code" class="block mb-2 text-sm font-semibold text-gray-300">
                        كود التعبئة <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(16 رقم بالضبط)</span>
                    </label>
                    <input type="text" inputmode="numeric" id="recharge_code" name="recharge_code" value="{{ old('recharge_code') }}"
                           maxlength="16" placeholder="16 رقم"
                           class="w-full h-12 p-3 rounded-xl bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500 transition tracking-widest"
                           required>
                    <div class="flex justify-between mt-1.5">
                        <p class="hint text-xs text-gray-400">أرقام فقط، بدون مسافات أو رموز.</p>
                        <span id="code-counter" class="text-xs text-gray-400 font-mono">0/16</span>
                    </div>
                </div>

                {{-- الاسم الكامل --}}
                <div class="mb-5" id="group-fullName">
                    <label for="fullName" class="block mb-2 text-sm font-semibold text-gray-300">الاسم الكامل <span class="text-red-500">*</span></label>
                    <input type="text" id="fullName" name="fullName" value="{{ old('fullName') }}"
                           placeholder="الاسم الكامل"
                           class="w-full h-12 p-3 rounded-xl bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500 transition"
                           required>
                    <p class="hint text-xs mt-1.5 text-gray-400">الاسم الكامل إجباري.</p>
                </div>

                {{-- المنصة --}}
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-semibold text-gray-300">اختر المنصة <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="group-platform">
                        @foreach($platforms as $platform)
                            <div class="platform-card">
                                <input type="radio" name="platform" id="platform-{{ $platform }}" value="{{ $platform }}"
                                       class="hidden"
                                       {{ strtolower(old('platform', '1xbet')) === strtolower($platform) ? 'checked' : '' }}>
                                <label for="platform-{{ $platform }}"
                                       class="flex items-center justify-center h-14 rounded-xl border-2 border-gray-700 bg-gray-800 cursor-pointer text-sm font-bold uppercase transition hover:border-blue-500">
                                    {{ $platform }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- صورة إثبات التعبئة --}}
                <div class="mb-5" id="group-recharge_image">
                    <label class="block mb-2 text-sm font-semibold text-gray-300">صورة إثبات التعبئة <span class="text-red-500">*</span></label>
                    <label for="recharge_image" class="upload-zone flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-700 rounded-xl p-6 cursor-pointer hover:border-blue-500 transition text-center bg-gray-800/50">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400"></i>
                        <span class="text-sm text-gray-300" data-default-label>اضغط لاختيار صورة أو اسحبها هنا (JPG, PNG, WEBP — أقصى حجم 5124 كيلوبايت)</span>
                        <img class="preview hidden mt-2 max-h-40 rounded-lg border border-gray-700" alt="معاينة الصورة">
                    </label>
                    <input type="file" id="recharge_image" name="recharge_image" accept="image/png,image/jpeg,image/webp" class="hidden" required>
                    <p class="hint text-xs mt-1.5 text-gray-400">إجبارية.</p>
                </div>

                {{-- سكرين شوت اختياري --}}
                <div class="mb-6" id="group-platform_screenshot">
                    <label class="block mb-2 text-sm font-semibold text-gray-300">سكرين شوت (ID + البرومو كود) <span class="text-gray-400 font-normal">(اختياري)</span></label>
                    <label for="platform_screenshot" class="upload-zone flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-700 rounded-xl p-6 cursor-pointer hover:border-blue-500 transition text-center bg-gray-800/50">
                        <i class="fa-solid fa-image text-2xl text-gray-400"></i>
                        <span class="text-sm text-gray-300" data-default-label>اضغط لاختيار صورة (اختياري — أقصى حجم 5124 كيلوبايت)</span>
                        <img class="preview hidden mt-2 max-h-40 rounded-lg border border-gray-700" alt="معاينة الصورة">
                    </label>
                    <input type="file" id="platform_screenshot" name="platform_screenshot" accept="image/png,image/jpeg,image/webp" class="hidden">
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-lg transition flex items-center justify-center gap-2 shadow-lg">
                    <i class="fa-solid fa-spinner spin hidden" id="submitSpinner"></i>
                    <span id="submitLabel">إرسال الطلب</span>
                </button>
            </form>

            {{-- صندوق التنبيهات --}}
            <div class="mt-8 bg-gray-800/60 border border-gray-700 p-6 rounded-2xl">
                <h4 class="text-blue-400 font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> تنبيه مهم جداً
                </h4>
                <ul class="text-gray-300 text-sm space-y-2">
                    <li>- تأكد من ID الحساب قبل إرسال الطلب.</li>
                    <li>- يجب أن تكون صورة التعبئة والكود واضحين وصحيحين.</li>
                    <li>- يجب أن يكون التحويل من طرف صاحب الحساب نفسه.</li>
                    <li>- أي خطأ في المعلومات قد يؤدي إلى رفض الطلب.</li>
                </ul>
            </div>

            <a href="https://wa.me/212798706144" target="_blank"
               class="mt-6 flex items-center justify-center gap-2 text-green-400 hover:text-green-300 text-sm font-bold">
                <i class="fa-brands fa-whatsapp text-lg"></i> عندك مشكل؟ تواصل معنا مباشرة على الواتساب
            </a>
        </div>
    </div>

<script>
(function () {
    const form = document.getElementById('rechargeForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitLabel = document.getElementById('submitLabel');
    const submitSpinner = document.getElementById('submitSpinner');
    const MAX_BYTES = 5124 * 1024;

    const fieldState = {
        montant: false,
        account_id: false,
        recharge_code: false,
        fullName: false,
        platform: true,
        recharge_image: false,
        platform_screenshot: true,
    };

    function setFieldStatus(groupId, ok, message) {
        const group = document.getElementById(groupId);
        if (!group) return;
        group.classList.remove('field-ok', 'field-error');
        const hint = group.querySelector('.hint');
        if (ok === null) return;
        group.classList.add(ok ? 'field-ok' : 'field-error');
        if (hint && message) hint.textContent = message;
    }

    function updateSubmitState() {
        const allValid = Object.values(fieldState).every(Boolean);
        submitBtn.disabled = !allValid;
        if(submitBtn.disabled) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    const montant = document.getElementById('montant');
    montant.addEventListener('input', () => {
        const val = parseFloat(montant.value);
        const ok = montant.value !== '' && !isNaN(val) && val > 1;
        fieldState.montant = ok;
        setFieldStatus('group-montant', ok, ok ? 'المبلغ صحيح.' : 'يجب أن يكون المبلغ أكبر من 1 درهم.');
        updateSubmitState();
    });

    const accountId = document.getElementById('account_id');
    accountId.addEventListener('input', () => {
        const ok = accountId.value.trim().length > 0;
        fieldState.account_id = ok;
        setFieldStatus('group-account_id', ok, ok ? 'تمام.' : 'ID الحساب إجباري.');
        updateSubmitState();
    });

    const rechargeCode = document.getElementById('recharge_code');
    const codeCounter = document.getElementById('code-counter');
    rechargeCode.addEventListener('input', () => {
        // تعديل الحد الأقصى إلى 16 رقم
        rechargeCode.value = rechargeCode.value.replace(/[^0-9]/g, '').slice(0, 16);
        codeCounter.textContent = rechargeCode.value.length + '/16';
        const ok = rechargeCode.value.length === 16;
        fieldState.recharge_code = ok;
        setFieldStatus('group-recharge_code', rechargeCode.value.length === 0 ? null : ok,
            ok ? 'الكود صحيح.' : 'يجب أن يتكون الكود من 16 رقماً بالضبط.');
        updateSubmitState();
    });

    const fullName = document.getElementById('fullName');
    fullName.addEventListener('input', () => {
        const val = fullName.value.trim();
        const ok = val.length >= 3;
        fieldState.fullName = ok;
        setFieldStatus('group-fullName', val.length === 0 ? null : ok,
            ok ? 'تمام.' : 'الاسم الكامل يجب أن يحتوي على 3 أحرف على الأقل.');
        updateSubmitState();
    });

    document.querySelectorAll('#group-platform input[type=radio]').forEach(radio => {
        radio.addEventListener('change', () => {
            fieldState.platform = true;
            updateSubmitState();
        });
    });

    function wireUpload(inputId, required) {
        const input = document.getElementById(inputId);
        const zone = input.previousElementSibling;
        const label = zone.querySelector('[data-default-label]');
        const preview = zone.querySelector('.preview');
        const groupId = 'group-' + inputId;

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) {
                fieldState[inputId] = !required;
                preview.classList.add('hidden');
                label.classList.remove('hidden');
                setFieldStatus(groupId, required ? null : true, required ? 'إجبارية.' : 'اختيارية.');
                updateSubmitState();
                return;
            }

            const isImage = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
            const isSmallEnough = file.size <= MAX_BYTES;

            if (!isImage) {
                fieldState[inputId] = false;
                setFieldStatus(groupId, false, 'الملف يجب أن يكون صورة JPG أو PNG أو WEBP.');
                preview.classList.add('hidden');
                label.classList.remove('hidden');
                updateSubmitState();
                return;
            }

            if (!isSmallEnough) {
                fieldState[inputId] = false;
                const sizeKb = (file.size / 1024).toFixed(0);
                setFieldStatus(groupId, false, `حجم الصورة ${sizeKb} كيلوبايت. الحد الأقصى هو 5124 كيلوبايت.`);
                preview.classList.add('hidden');
                label.classList.remove('hidden');
                updateSubmitState();
                return;
            }

            fieldState[inputId] = true;
            setFieldStatus(groupId, true, `تم اختيار: ${file.name} (${(file.size / 1024).toFixed(0)} كيلوبايت)`);

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                label.classList.add('hidden');
            };
            reader.readAsDataURL(file);
            updateSubmitState();
        });
    }

    wireUpload('recharge_image', true);
    wireUpload('platform_screenshot', false);

    form.addEventListener('submit', (e) => {
        if (submitBtn.disabled) {
            e.preventDefault();
            return;
        }
        submitBtn.disabled = true;
        submitSpinner.classList.remove('hidden');
        submitLabel.textContent = 'جاري الإرسال...';
    });

    const countdownEl = document.getElementById('retry-countdown');
    if (countdownEl) {
        let seconds = parseInt(countdownEl.dataset.seconds, 10) || 0;
        const tick = () => {
            if (seconds <= 0) {
                countdownEl.textContent = 'يمكنك الآن إعادة المحاولة.';
                submitBtn.disabled = !Object.values(fieldState).every(Boolean);
                return;
            }
            countdownEl.textContent = `يمكنك إعادة المحاولة بعد ${seconds} ثانية`;
            seconds -= 1;
            setTimeout(tick, 1000);
        };
        submitBtn.disabled = true;
        tick();
    }

    updateSubmitState();
})();
</script>

</body>
</html>
