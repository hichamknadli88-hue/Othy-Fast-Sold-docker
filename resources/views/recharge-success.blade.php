<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إرسال طلبك | OTHY FAST SOLD</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('1784465709672.png') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #020617; color: #f8fafc; }
        .glass-card { background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .step-bar { height: 4px; border-radius: 9999px; background: linear-gradient(90deg, #2563eb, #60a5fa); }
        .pop { animation: pop .5s cubic-bezier(.2,1.4,.4,1) both; }
        @keyframes pop { from { transform: scale(.4); opacity: 0; } to { transform: scale(1); opacity: 1; } }
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

    <div class="max-w-lg mx-auto p-4 md:p-10">
        <div class="glass-card p-6 md:p-8 rounded-3xl shadow-xl">

            {{-- Steps --}}
            <div class="grid grid-cols-3 gap-3 mb-8 text-center text-xs font-bold">
                <div>
                    <div class="mb-2 text-gray-400">المعلومات</div>
                    <div class="step-bar"></div>
                </div>
                <div>
                    <div class="mb-2 text-gray-400">الإثبات</div>
                    <div class="step-bar"></div>
                </div>
                <div>
                    <div class="mb-2 text-blue-400">النتيجة</div>
                    <div class="step-bar"></div>
                </div>
            </div>

            {{-- Check icon --}}
            <div class="flex justify-center mb-6">
                <div class="pop w-20 h-20 rounded-full flex items-center justify-center bg-green-500/10 border-2 border-green-500/40 text-green-400 text-3xl">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h2 class="text-3xl font-black text-center mb-2 text-white">تم إرسال طلبك بنجاح</h2>
            <p class="text-gray-400 text-center text-sm mb-8">
                طلبك قيد المعالجة. الوقت المتوقع حوالي <span class="text-blue-400 font-bold">دقيقتين</span>.
            </p>

            {{-- Details --}}
            @php
                $order = session('order', []);
                $reference = $order['reference'] ?? ($reference ?? '—');
                $amount    = $order['amount']    ?? ($amount ?? '—');
                $platform  = $order['platform']  ?? ($platform ?? '—');
            @endphp

            <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-5 mb-6 space-y-4 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-400">رقم المرجع</span>
                    <span class="font-mono font-bold text-blue-400 tracking-wider" dir="ltr">{{ $reference }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-400">المبلغ</span>
                    <span class="font-black text-white">{{ $amount }} DH</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-400">المنصة</span>
                    <span class="font-black text-white uppercase">{{ $platform }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-400">الوقت المتوقع</span>
                    <span class="font-bold text-green-400">~2 دقائق</span>
                </div>
            </div>

            {{-- Actions --}}
            <a href="https://t.me/Othyfastsold" target="_blank"
               class="w-full mb-3 py-3.5 rounded-xl border border-blue-500/30 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center gap-2 transition">
                <i class="fa-brands fa-telegram text-lg"></i> تواصل عبر تيليغرام
            </a>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('home') }}"
                   class="py-4 rounded-xl border border-gray-700 bg-gray-800 hover:border-gray-600 text-white font-bold text-center transition">
                    العودة للرئيسية
                </a>
              <a href="{{ route('recharge.form') }}"
                   class="py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-center shadow-lg transition">
                    طلب جديد
                </a>
            </div>
        </div>

        <a href="https://wa.me/212725415898" target="_blank"
           class="mt-6 flex items-center justify-center gap-2 text-green-400 hover:text-green-300 text-sm font-bold">
            <i class="fa-brands fa-whatsapp text-lg"></i> عندك مشكل؟ تواصل معنا مباشرة على الواتساب
        </a>
    </div>

</body>
</html> 
