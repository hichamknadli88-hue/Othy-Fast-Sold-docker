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

        /* Floating WhatsApp Buttons Animation */
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }
        .whatsapp-float-btn {
            animation: pulse-subtle 3s infinite ease-in-out;
            box-shadow: 0 10px 25px -5px rgba(37, 211, 102, 0.4);
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
<body class="text-slate-200 min-h-screen overflow-x-hidden relative">

    @include('partials.annonce-popup')

    <div class="w-full bg-blue-600/10 py-2.5 border-b border-blue-500/20 text-xs font-bold text-center text-blue-400 marquee-wrap">
        <span class="marquee-track">
            سرعة، أمان، وموثوقية | تم إتمام أكثر من 1000 عملية اليوم | استخدم كود OTHY للحصول على أفضل سعر!
        </span>
    </div>

    <!-- Header / Navbar تم تبسيطه واستعادة شكله الأصلي النظيف -->
    <nav class="w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="text-lg font-black tracking-tight text-white">
                OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-sm text-slate-400 font-bold">
                <a href="#pricing" class="hover:text-blue-400 transition-colors">الأسعار</a>
                <a href="#platforms" class="hover:text-blue-400 transition-colors">المنصات</a>
                <a href="#contact" class="hover:text-blue-400 transition-colors">تواصل معنا</a>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('recharge.form') }}" id="nav-cta" class="px-4 py-2.5 rounded-lg text-sm font-bold bg-blue-600 hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 text-white">
                    لتعبئة الحساب
                </a>
                @auth
                    @admin
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 rounded-lg text-sm font-bold bg-slate-800 hover:bg-slate-700 transition-all text-white">
                            لوحة التحكم
                        </a>
                    @endadmin
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-bold bg-white/5 border border-white/10 text-slate-400 hover:bg-white/10 hover:text-slate-200 transition-all">
                            <i class="fa-solid fa-right-from-bracket ml-1"></i> تسجيل الخروج
                        </button>
                    </form>
                @elseguest
                    <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-lg text-sm font-bold bg-slate-800 hover:bg-slate-700 transition-all text-white">
                        تسجيل الدخول  
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- أزرار الواتساب العائمة في أسفل اليسار (مثل زري التيلغرام والواتساب المشهورة) -->
    <div class="fixed bottom-5 left-5 z-50 flex flex-col gap-3">
        <!-- زر واتساب 2 -->
        <a href="https://wa.me/qr/DJTJUSLCYYWTO1" target="_blank" class="whatsapp-float-btn w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-400 text-white rounded-full flex items-center justify-center transition-all transform hover:scale-110 shadow-lg border-2 border-emerald-300/30 group relative" title="واتساب 2">
            <i class="fab fa-whatsapp text-2xl md:text-3xl"></i>
            <span class="absolute left-16 bg-slate-900/90 text-white text-xs font-bold py-1 px-2.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-slate-700 pointer-events-none">
                واتساب 2
            </span>
        </a>

        <!-- زر واتساب 1 -->
        <a href="https://wa.me/212725415898" target="_blank" class="whatsapp-float-btn w-12 h-12 md:w-14 md:h-14 bg-emerald-500 hover:bg-emerald-400 text-white rounded-full flex items-center justify-center transition-all transform hover:scale-110 shadow-lg border-2 border-emerald-300/30 group relative" title="واتساب 1">
            <i class="fab fa-whatsapp text-2xl md:text-3xl"></i>
            <span class="absolute left-16 bg-slate-900/90 text-white text-xs font-bold py-1 px-2.5 rounded-md opacity-0 group-hover:opacity-
