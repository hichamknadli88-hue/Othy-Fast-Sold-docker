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
