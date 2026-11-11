<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إرسال طلب الشحن | OTHY FAST SOLD</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('1784465709672.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #020617; color: #f8fafc; }
        .glass-card { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .field-ok input, .field-ok textarea, .field-ok select { border-color: #16a34a !important; }
        .field-error input, .field-error textarea, .field-error select { border-color: #dc2626 !important; }
        .platform-card input:checked + label { border-color: #2563eb; background: rgba(37,99,235,0.2); box-shadow: 0 0 0 2px rgba(37,99,235,0.4); }
    </style>
</head>
<body class="min-h-screen">


    <!-- Nav bar -->
    <nav class="w-full border-b border-gray-800 bg-gray-black/100 backdrop-blur-md sticky top-0 z-40">
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
                <span class="text-blue-400">OTHY FAST SOLD</span> نموذج طلب الشحن
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

            @php
                $loggedIn = auth()->check();
                $showRechargeModes = $loggedIn && ($canRepeatRecharge ?? false);
                $defaultMode = old('recharge_mode', $showRechargeModes ? 'existing' : 'new');
                $savedCount = isset($savedPlatforms) ? $savedPlatforms->count() : 0;
            @endphp

            @if($loggedIn)
                <p class="text-center text-sm text-gray-400 mb-6">
                    مسجل الدخول كـ <span class="text-blue-400 font-bold">{{ auth()->user()->name }}</span>
                </p>
            @endif

            <form action="{{ route('recharge.store') }}" method="POST" enctype="multipart/form-data" id="rechargeForm" novalidate>
                @csrf

                @if($showRechargeModes)
                    <input type="hidden" name="recharge_mode" id="recharge_mode" value="{{ $defaultMode }}">
                    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 gap-3" id="recharge-mode-section">
                        <button type="button" data-mode="existing"
                                class="recharge-mode-btn rounded-2xl border-2 p-4 text-right transition {{ $defaultMode === 'existing' ? 'border-blue-500 bg-blue-500/10' : 'border-gray-700 bg-gray-800/50 hover:border-gray-600' }}">
                            <span class="block text-sm font-black text-white mb-1">تعبئة حساب موجود</span>
                            <span class="block text-xs text-gray-400">اختر المبلغ، أدخل الكود وارفع صورة التعبئة فقط.</span>
                        </button>
                        <button type="button" data-mode="new"
                                class="recharge-mode-btn rounded-2xl border-2 p-4 text-right transition {{ $defaultMode === 'new' ? 'border-blue-500 bg-blue-500/10' : 'border-gray-700 bg-gray-800/50 hover:border-gray-600' }}">
                            <span class="block text-sm font-black text-white mb-1">تعبئة منصة جديدة</span>
                            <span class="block text-xs text-gray-400">املأ كل معلومات الحساب والمنصة كما في المرة الأولى.</span>
                        </button>
                    </div>
                @else
                    <input type="hidden" name="recharge_mode" id="recharge_mode" value="new">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-1">
                    {{-- المبلغ --}}
                    <div class="mb-5 md:col-span-2" id="group-montant">
                        <label for="montant" class="block mb-2 text-sm font-semibold text-gray-300">المبلغ (Amount) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select id="montant" name="montant"
                                    class="w-full h-12 p-3 pl-10 rounded-xl bg-gray-800 border border-gray-700 text-white focus:outline-none focus:border-blue-500 transition cursor-pointer"
                                    required>
                                <option value="" disabled {{ old('montant') ? '' : 'selected' }}>اختر المبلغ</option>
                                @foreach([5, 10, 20, 30, 50, 100, 200, 500, 1000] as $amount)
                                    <option value="{{ $amount }}" {{ (string) old('montant') === (string) $amount ? 'selected' : '' }}>{{ $amount }} DH</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
