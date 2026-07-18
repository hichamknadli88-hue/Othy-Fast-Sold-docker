<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>OTHY FAST SOLD | بوابتك للخدمات الرقمية</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #020617; }
        .glass-card { background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }

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
            flex: 1 1 0%;
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
    </style>
</head>
<body class="text-slate-200 min-h-screen overflow-x-hidden">

    <div class="w-full bg-blue-600/10 py-2.5 border-b border-blue-500/20 text-xs font-bold text-center text-blue-400 marquee-wrap">
        <span class="marquee-track">
             سرعة، أمان، وموثوقية | تم إتمام أكثر من 200 عملية اليوم | استخدم كود OTHY للحصول على أفضل سعر!
        </span>
    </div>

    <nav class="w-full border-b border-slate-800/80 bg-slate-950/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="text-lg font-black tracking-tight">
                OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm text-slate-400 font-bold">
                <a href="#pricing" class="hover:text-blue-400 transition-colors">الأسعار</a>
                <a href="#platforms" class="hover:text-blue-400 transition-colors">المنصات</a>
                <a href="#contact" class="hover:text-blue-400 transition-colors">تواصل معنا</a>
            </div>
            <a href="/portal" id="nav-cta" class="px-5 py-2.5 rounded-lg text-sm font-bold bg-blue-600 hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20">
               لتعبئة الحساب
            </a>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-10 flex flex-col items-center text-center">
        <span class="inline-block mb-6 px-4 py-1.5 rounded-full bg-blue-600/10 border border-blue-500/20 text-blue-400 text-xs font-bold tracking-wide">
            بوابتك الموثوقة للمعالجة الرقمية
        </span>

        <h1 class="text-5xl md:text-6xl font-black mb-6 tracking-tighter leading-tight">
            OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
        </h1>
        <p class="text-slate-400 text-lg md:text-xl mb-12 max-w-xl leading-relaxed">
            وجهتك الأولى للمعالجة الرقمية الآمنة. سرعة فائقة في التنفيذ وتجربة مستخدم لا تضاهى.
        </p>

        <div id="hero-cta-group" class="flex items-stretch justify-center gap-4 w-full max-w-md mb-16">
            <a href="https://api.whatsapp.com/send/?phone=212725415898&text&type=phone_number&app_absent=0" target="_blank"
               class="cta-btn bg-slate-800 hover:bg-slate-700 text-white border border-slate-700">
                <i class="fab fa-whatsapp text-lg"></i>
               للسحب
            </a>
            <a href="/portal" id="hero-cta" class="cta-btn bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-600/20">
               لتعبئة الحساب
            </a>
        </div>

        <div class="grid grid-cols-3 gap-6 md:gap-12 w-full max-w-2xl">
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-bolt text-blue-400 text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">سرعة فائقة</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-shield-halved text-blue-400 text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">أمان تام</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <i class="fa-solid fa-circle-check text-blue-400 text-2xl"></i>
                <span class="text-xs md:text-sm text-slate-400 font-bold">موثوقية عالية</span>
            </div>
        </div>
    </main>

    <section id="pricing" class="max-w-4xl mx-auto px-6 mb-24">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black mb-3">جدول الأسعار</h2>
            <p class="text-slate-500 text-sm max-w-lg mx-auto">أسعار واضحة وثابتة، مع مزايا إضافية عند استخدام كود OTHY.</p>
        </div>
        <div class="glass-card rounded-3xl p-6 border border-slate-700/50">
            <table class="w-full text-center">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-700/50">
                        <th class="py-4 font-normal">المبلغ</th>
                        <th class="py-4 font-normal">عادي</th>
                        <th class="py-4 font-normal text-blue-400">مع كود OTHY</th>
                    </tr>
                </thead>
                <tbody id="price-table-body" class="text-slate-200"></tbody>
            </table>
        </div>
    </section>

    <div class="max-w-2xl mx-auto px-6 text-center mb-12">
        <h2 class="text-3xl font-black mb-4">المنصات الشريكة</h2>
        <p class="text-slate-400 text-base leading-relaxed">
            هذه أكواد ترويجية خاصة بمنصاتنا الشريكة. اضغط على زر "سجل الآن" أسفل كل منصة لنسخ الكود تلقائياً
            والانتقال إلى صفحة التسجيل الرسمية، حيث يمنحك الكود مزايا إضافية عند فتح حسابك.
        </p>
    </div>

    <section id="platforms" class="max-w-4xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-6"></section>

    <p class="text-center text-slate-500 text-sm mt-8 mb-24 px-6">
        ملاحظة: عند الضغط على زر "سجل الآن"، سيتم نسخ الكود تلقائياً لضمان حصولك على أفضل العروض عند التسجيل.
    </p>

    <footer id="contact" class="bg-slate-900/50 border-t border-slate-800 py-16 w-full">
        <div class="max-w-5xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10 text-right">
            <div>
                <h4 class="text-blue-500 font-bold mb-4 border-b border-slate-800 pb-2">واتساب</h4>
                <div class="space-y-3 text-slate-400 text-sm">
                    <p class="flex items-center"><i class="fab fa-whatsapp text-green-500 ml-2 text-lg"></i> 0798706144</p>
                    <p class="flex items-center"><i class="fab fa-whatsapp text-green-500 ml-2 text-lg"></i> 0725415898</p>
                </div>
            </div>
            <div>
                <h4 class="text-blue-500 font-bold mb-4 border-b border-slate-800 pb-2">تيلغرام</h4>
                <div class="space-y-3">
                    <a href="https://t.me/Othy_fast_sold_07" target="_blank" class="block text-slate-400 text-sm hover:text-blue-400"><i class="fab fa-telegram text-sky-500 ml-2 text-lg"></i> القناة الرسمية</a>
                    <a href="https://t.me/Othyfastsold" target="_blank" class="block text-slate-400 text-sm hover:text-blue-400"><i class="fab fa-telegram text-sky-500 ml-2 text-lg"></i> الدعم الفوري</a>
                </div>
            </div>
            <div>
                <h4 class="text-blue-500 font-bold mb-4 border-b border-slate-800 pb-2">تابعنا</h4>
                <div class="flex gap-6 text-2xl text-slate-400">
                    <a href="https://www.facebook.com/share/1982M7TqU9/" class="hover:text-blue-600 transition"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/koo.ra07?igsh=YnlhdGNiOThyNjVl" class="hover:text-pink-600 transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center text-slate-700 text-xs mt-10 border-t border-slate-800 pt-6">
            &copy; 2026 OTHY FAST SOLD. جميع الحقوق محفوظة.
        </div>
    </footer>

    <script>
        const priceRows = [
            ['10 DH', '6 Sold', '7 Sold'],
            ['20 DH', '13 Sold', '14 Sold'],
            ['50 DH', '33 Sold', '37 Sold'],
            ['100 DH', '65 Sold', '75 Sold']
        ];

        const platforms = {
            '1XBT':      { code: 'OTHY077', url: 'https://1xbet.com/registration/' },
            'LINEBET':   { code: 'OTHY07',  url: 'https://linebet.com/registration/' },
            'MELBET':    { code: 'OTHY08',  url: 'https://melbet.com/registration/' },
            'PARIPULSE': { code: 'OTHY07',  url: 'https://paripulsema.com/fr/registration/' }
        };

        const tbody = document.getElementById('price-table-body');
        priceRows.forEach(([amount, normal, withCode]) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-white/5 transition-colors';
            tr.innerHTML = `
                <td class="py-4 font-bold">${amount}</td>
                <td class="py-4">${normal}</td>
                <td class="py-4 text-blue-400 font-bold">${withCode}</td>
            `;
            tbody.appendChild(tr);
        });

        const codesSection = document.getElementById('platforms');
        Object.entries(platforms).forEach(([platform, data]) => {
            const card = document.createElement('div');
            card.className = 'glass-card p-6 rounded-3xl text-center hover:-translate-y-2 hover:border-blue-500/40 transition-all border border-slate-700';
            card.innerHTML = `
                <div class="w-12 h-12 mx-auto mb-4 rounded-xl bg-blue-600/10 border border-blue-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-dice text-blue-400 text-lg"></i>
                </div>
                <div class="text-base md:text-lg font-black tracking-wide text-white mb-1 uppercase">${platform}</div>
                <div class="text-[11px] text-slate-500 mb-4">كود ترويجي</div>
                <div class="text-2xl font-black text-blue-400 mb-6 tracking-wider">${data.code}</div>
                <button class="w-full py-2.5 bg-slate-800 hover:bg-blue-600 rounded-lg text-xs font-bold transition-all">
                    سجل الآن
                </button>
            `;
            card.querySelector('button').addEventListener('click', () => copyAndRedirect(data.code, data.url));
            codesSection.appendChild(card);
        });

        function copyAndRedirect(code, url) {
            navigator.clipboard.writeText(code).catch(() => {
                const tmp = document.createElement('textarea');
                tmp.value = code;
                document.body.appendChild(tmp);
                tmp.select();
                document.execCommand('copy');
                document.body.removeChild(tmp);
            });
            alert('تم نسخ الكود: ' + code);
            window.open(url, '_blank');
        }

        const navCta = document.getElementById('nav-cta');
        const heroCta = document.getElementById('hero-cta');

        const ctaObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    navCta.classList.add('nav-cta-hidden');
                } else {
                    navCta.classList.remove('nav-cta-hidden');
                }
            });
        }, { threshold: 0.3 });

        ctaObserver.observe(heroCta);
    </script>
</body>
</html>