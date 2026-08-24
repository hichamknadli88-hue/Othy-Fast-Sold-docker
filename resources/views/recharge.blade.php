<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة الشحن - Othy Fast Sold</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* تنسيق بطاقات المنصات بالصور والتأثيرات البصرية */
        .platform-card input:checked + label {
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.25);
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.4);
            transform: scale(1.02);
        }
        .platform-card label img {
            transition: transform 0.2s ease;
        }
        .platform-card input:checked + label img {
            transform: scale(1.08);
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-xl bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 md:p-8">
        
        <!-- عنوان الصفحة -->
        <div class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-extrabold text-blue-500 mb-2">Othy Fast Sold</h1>
            <p class="text-sm text-gray-400">قم بملء المعلومات أدناه لإتمام عملية الشحن بسرعة</p>
        </div>

        <!-- عرض رسائل النجاح أو الأخطاء إن وجدت -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-500/20 border border-green-500 text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500 text-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- الفورم -->
        <form action="#" method="POST">
            @csrf

            {{-- 1. اختيار المنصة بالصور --}}
            <div class="mb-6">
                <label class="block mb-3 text-sm font-semibold text-gray-300">اختر المنصة <span class="text-red-500">*</span></label>
                
                @php
                    // مصفوفة كتعرف بأسماء المنصات والملف الموافق ليها في public
                    $platforms = ['1xbet', 'paripulse', 'linebet', 'melbet'];
                    $platformImages = [
                        '1xbet'     => '1xbet.png',
                        'paripulse' => 'paripulse.png',
                        'linebet'   => 'linebet.png',
                        'melbet'    => 'melbet.png'
                    ];
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3" id="group-platform">
                    @foreach($platforms as $platform)
                        @php
                            $key = strtolower($platform);
                            $imgName = $platformImages[$key] ?? 'default.png';
                        @endphp
                        <div class="platform-card">
                            <input type="radio" name="platform" id="platform-{{ $platform }}" value="{{ $platform }}"
                                   class="hidden"
                                   {{ strtolower(old('platform', '1xbet')) === $key ? 'checked' : '' }}>
                            
                            <label for="platform-{{ $platform }}"
                                   class="flex flex-col items-center justify-center p-3 h-24 rounded-xl border-2 border-gray-800 bg-gray-800/60 cursor-pointer transition hover:border-blue-500 hover:bg-gray-800 gap-2">
                                
                                {{-- عرض اللوغو من public (إذا كان الدوسي فرعي مثلا public/images/ بدلها بـ asset('images/' . $imgName)) --}}
                                <img src="{{ asset($imgName) }}" alt="{{ $platform }}" class="max-h-12 max-w-full object-contain drop-shadow">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-300">{{ $platform }}</span>
                            
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. رقم الحساب (ID) --}}
            <div class="mb-5">
                <label for="account_id" class="block mb-2 text-sm font-semibold text-gray-300">رقم الحساب (ID) <span class="text-red-500">*</span></label>
                <input type="text" id="account_id" name="account_id" value="{{ old('account_id') }}" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                       placeholder="أدخل رقم الحساب الخاص بك">
            </div>

            {{-- 3. المبلغ المراد شحنه --}}
            <div class="mb-5">
                <label for="amount" class="block mb-2 text-sm font-semibold text-gray-300">المبلغ <span class="text-red-500">*</span></label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition"
                       placeholder="أدخل المبلغ">
            </div>

            {{-- 4. إثبات الدفع (صورة الوصل) --}}
            <div class="mb-6">
                <label for="receipt" class="block mb-2 text-sm font-semibold text-gray-300">إثبات الدفع (صورة الوصل) <span class="text-red-500">*</span></label>
                <input type="file" id="receipt" name="receipt" accept="image/*" required
                       class="w-full text-sm text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer bg-gray-800 border border-gray-700 rounded-xl cursor-pointer">
            </div>

            {{-- زر الإرسال --}}
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-600/30 transition duration-200">
                تأكيد طلب الشحن
            </button>

        </form>
    </div>

</body>
</html>
