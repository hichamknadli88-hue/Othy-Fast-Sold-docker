<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('1784465709672.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>OTHY FAST SOLD | بوابتك للخدمات الرقمية</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        html {
            scroll-behavior: smooth;
        }
        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: #020617; 
        }
        .glass-card { background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }

        .marquee-wrap { overflow: hidden; white-space: nowrap; }
        .marquee-track {
            display: inline-block;
            padding-left: 100%;
            animation: marquee-scroll 18s linear infinite;
        }
        @keyframes marquee-scroll {
            0%   { transform: translateX(15%); }
            100% { transform: translateX(-100%); }
        }

        .cta-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            height: 54px;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 700;
            white-space: nowrap;
            transition: all .3s ease;
        }

        #nav-cta {
            transition: opacity .25s ease, transform .25s ease, visibility .25s ease;
        }
        #nav-cta.nav-cta-hidden {
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            visibility: hidden;
        }

        /* Copy/redirect confirmation modal */
        #copyModalOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.75);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 1.5rem;
        }
        #copyModalOverlay.active {
            display: flex;
        }
        .copy-modal-box {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(59, 130, 246, 0.25);
            border-radius: 1.25rem;
            padding: 2rem 1.75rem;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            animation: copyModalPop .2s ease;
        }
        @keyframes copyModalPop {
            from { transform: scale(.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .copy-modal-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 1rem;
            border-radius: 9999px;
            background: rgba(59, 130, 246, 0.12);
            border: 1px solid rgba(59, 130, 246, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #60a5fa;
            font-size: 1.25rem;
        }
        .copy-modal-code {
            display: inline-block;
            margin-top: .5rem;
            padding: .4rem 1rem;
            border-radius: .5rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #60a5fa;
            font-weight: 900;
            letter-spacing: .05em;
        }
        .copy-modal-actions {
            display: flex;
            gap: .75rem;
            margin-top: 1.75rem;
        }
        .copy-modal-btn {
            flex: 1;
            padding: .7rem 0;
            border-radius: .6rem;
            font-weight: 700;
            font-size: .85rem;
            transition: all .2s ease;
            cursor: pointer;
            border: none;
        }
        .copy-modal-btn.confirm {
            background: #2563eb;
            color: #fff;
        }
        .copy-modal-btn.confirm:hover {
            background: #3b82f6;
        }
        .copy-modal-btn.cancel {
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .copy-modal-btn.cancel:hover {
            background: rgba(255,255,255,0.1);
            color: #e2e8f0;
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen overflow-x-hidden">

    @include('partials.annonce-popup')

    <div class="w-full bg-blue-600/10 py-2.5 border-b border-blue-500/20 text-xs font-bold text-center text-blue-400 marquee-wrap">
        <span class="marquee-track">
            سرعة، أمان، وموثوقية | تم إتمام أكثر من 1000 عملية اليوم | استخدم كود OTHY للحصول على أفضل سعر!
        </span>
    </div>

    <!-- Header / Navbar تم تحسينه للشاشات الصغيرة -->
    <nav class="w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between gap-2">
            <!-- الشعار -->
            <div class="text-base sm:text-lg font-black tracking-tight text-white shrink-0">
                OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
            </div>
            
            <!-- روابط التنقل (تظهر فقط في الشاشات المتوسطة والكبيرة) -->
            <div class="hidden md:flex items-center gap-8 text-sm text-slate-400 font-bold">
                <a href="#pricing" class="hover:text-blue-400 transition-colors">الأسعار</a>
                <a href="#platforms" class="hover:text-blue-400 transition-colors">المنصات</a>
                <a href="#contact" class="hover:text-blue-400 transition-colors">تواصل معنا</a>
            </div>
            
            <!-- الأزرار اليمنية -->
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <!-- أزرار الواتساب (أيقونة فقط في الهاتف، ونص كامل في الشاشات الكبيرة) -->
                <a href="https://wa.me/212725415898" target="_blank" class="h-9 px-2.5 rounded-lg text-xs font-bold bg-emerald-500/15 border border-emerald-500/30 hover:bg-emerald-500/30 text-emerald-400 transition-all flex items-center justify-center gap-1" title="واتساب 1">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span class="hidden md:inline">واتساب 1</span>
                </a>
                <a href="https://wa.me/qr/DJTJUSLCYYWTO1" target="_blank" class="h-9 px-2.5 rounded-lg text-xs font-bold bg-emerald-500/15 border border-emerald-500/30 hover:bg-emerald-500/30 text-emerald-400 transition-all flex items-center justify-center gap-1" title="واتساب 2">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span class="hidden md:inline">واتساب 2</span>
                </a>

                <a href="{{ route('recharge.form') }}" id="nav-cta" class="h-9 px-3 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 text-white flex items-center justify-center">
                    تعبئة
                </a>

                @auth
                    @admin
                        <a href="{{ route('admin.dashboard') }}" class="h-9 px-3 rounded-lg text-xs font-bold bg-slate-800 hover:bg-slate-700 transition-all text-white flex items-center justify-center">
                            اللوحة
                        </a>
                    @endadmin
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="h-9 px-2.5 rounded-lg text-xs font-bold bg-white/5 border border-white/10 text-slate-400 hover:bg-white/10 hover:text-slate-200 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                @elseguest
                    <a href="{{ route('login') }}" class="h-9 px-3 rounded-lg text-xs font-bold bg-slate-800 hover:bg-slate-700 transition-all text-white flex items-center justify-center">
                        دخول
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 flex flex-col items-center text-center">
        <span class="inline-block mb-6 px-4 py-1.5 rounded-full bg-blue-600/10 border border-blue-500/20 text-blue-400 text-xs font-bold tracking-wide">
            بوابتك الموثوقة للمعالجة الرقمية
        </span>

        <h1 class="text-4xl md:text-6xl font-black mb-2 tracking-tighter leading-tight text-white">
            OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
        </h1>

        <!-- Lottie animation -->
        <div id="hero-lottie" class="w-44 h-44 md:w-64 md:h-44 mx-auto mb-2"></div>

        <p class="text-slate-400 text-base md:text-xl mb-10 max-w-xl leading-relaxed">
            وجهتك الأولى للمعالجة الرقمية الآمنة. سرعة فائقة في التنفيذ وتجربة مستخدم لا تضاهى.
        </p>

        <div id="hero-cta-group" class="flex flex-col items-center justify-center gap-4 w-full max-w-md mb-4">
            <a href="{{ route('recharge.form') }}" id="hero-cta" class="cta-btn bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-600/20 w-full">
                لتعبئة الحساب
            </a>
        </div>

        <div class="w-full max-w-md flex flex-col gap-3 mb-16">
            <a href="https://t.me/Othy_fast_sold_07" target="_blank" class="cta-btn bg-sky-600 hover:bg-sky-500 text-white shadow-lg shadow-sky-600/20 w-full text-xs md:text-sm">
                <i class="fab fa-telegram text-lg"></i>
                القناة الرسمية للعروض
            </a>
            <a href="https://t.me/OthyFastSold" target="_blank" class="cta-btn bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/20 w-full text-xs md:text-sm">
                <i class="fab fa-telegram text-lg"></i>
                للتواصل و السحب
            </a>
            <a href="https://t.me/othy_fast_prono" target="_blank" class="cta-btn bg-slate-800 hover:bg-slate-700 text-sky-400 border border-slate-700 w-full text-xs md:text-sm">
                <i class="fab fa-telegram text-lg"></i>
                قناة التوقعات
            </a>
        </div>

        <div class="grid grid-cols-3 gap-4 md:gap-12 w-full max-w-2xl">
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-bolt text-blue-400 text-xl md:text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">سرعة فائقة</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-shield-halved text-blue-400 text-xl md:text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">أمان تام</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-circle-check text-blue-400 text-xl md:text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">موثوقية عالية</span>
            </div>
        </div>
