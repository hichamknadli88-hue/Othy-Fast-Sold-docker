<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>لوحة التحكم | OTHY FAST SOLD</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        html { scroll-behavior: smooth; }
        body { font-family: 'Cairo', sans-serif; background-color: #020617; }
        .glass-card { background: rgba(15,23,42,.85); border: 1px solid rgba(255,255,255,.1); }
        /* Blur only on desktop: backdrop-filter on an ancestor makes position:fixed children
           (the mobile action bar) stick to the card instead of the screen */
        @media (min-width: 1024px) { .glass-card { background: rgba(15,23,42,.65); backdrop-filter: blur(12px); } }
        .dropzone { border: 2px dashed rgba(255,255,255,.15); border-radius: 1rem; transition: border-color .2s, background .2s; cursor: pointer; }
        .dropzone:hover, .dropzone.drag-over { border-color: rgba(59,130,246,.5); background: rgba(59,130,246,.05); }
        .dropzone.is-invalid { border-color: rgba(239,68,68,.5); }
        .dropzone.has-image { border-style: solid; border-color: rgba(255,255,255,.1); padding: .5rem; background: rgba(2,6,23,.4); }
        .submit-btn { height: 48px; border-radius: .75rem; font-weight: 700; font-size: .9rem; color: #fff; background: #2563eb;
            display: flex; align-items: center; justify-content: center; gap: .5rem; transition: all .2s; border: none; cursor: pointer; padding: 0 1.5rem; }
        .submit-btn:hover:not(:disabled) { background: #3b82f6; }
        .submit-btn:disabled { background: rgba(51,65,85,.6); color: #64748b; cursor: not-allowed; }
        .field-error { display: flex; align-items: center; gap: .4rem; color: #f87171; font-size: .78rem; font-weight: 700; margin-top: .6rem; }
        .thumb { outline: 2px solid transparent; outline-offset: 2px; transition: outline-color .15s; }
        .thumb.selected { outline-color: #3b82f6; }

        /* Mobile first: fixed bottom bar, only once an image is ready */
        .action-bar { display: none; position: fixed; left: 0; right: 0; bottom: 0; z-index: 30; gap: .75rem; align-items: center;
            padding: .75rem 1rem calc(.75rem + env(safe-area-inset-bottom, 0px));
            background: rgba(2,6,23,.95); border-top: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(8px); }
        .action-bar.ready { display: flex; }
        .action-bar .submit-btn { flex: 1; }
        @media (min-width: 1024px) {
            .action-bar { display: flex; position: static; padding: 0; margin-top: 1.25rem; background: none; border: 0; backdrop-filter: none; justify-content: flex-end; }
            .action-bar .submit-btn { flex: none; }
            #barThumb { display: none !important; }
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex flex-col">
    <nav class="w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
        <div class="w-full px-4 sm:px-6 lg:px-10 py-3 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-base sm:text-lg font-black tracking-tight text-white">
                OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-bold bg-white/5 border border-white/10 text-slate-400 hover:bg-white/10 hover:text-slate-200 transition-all">
                    <i class="fa-solid fa-right-from-bracket ml-1"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </nav>

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-10 py-6 lg:py-10 pb-28 lg:pb-10">
        <h1 class="text-xl sm:text-2xl font-black text-white mb-1">لوحة التحكم</h1>
        <p class="text-slate-400 text-xs sm:text-sm mb-5">إدارة صور الإعلانات المعروضة في الموقع.</p>

        @if (session('success'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-sm font-bold text-center">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/25 text-red-400 text-sm font-bold text-center">{{ session('error') }}</div>
        @endif

        {{-- RTL: first column is on the RIGHT on desktop; mobile order is flipped with order-* --}}
        <div class="grid gap-5 lg:gap-6 lg:grid-cols-2 items-start">

            <!-- Gallery -->
            <section id="gallery" class="order-2 lg:order-1 glass-card rounded-2xl p-4 sm:p-6 scroll-mt-4">
                <h2 class="text-white font-bold text-base sm:text-lg mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-photo-film text-blue-400"></i>
                    الصور المرفوعة
                    <span class="text-slate-500 text-sm font-normal">({{ $annonces->total() }})</span>
                </h2>

                @if ($annonces->isEmpty())
                    <p class="text-slate-500 text-sm">لم يتم رفع أي صور بعد.</p>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        @foreach ($annonces as $annonce)
                            <div class="relative group">
                                <button type="button" class="thumb block w-full aspect-square rounded-xl overflow-hidden bg-slate-800"
                                        data-url="{{ $annonce->image_url }}"
                                        data-activate="{{ route('admin.annonces.activate', $annonce) }}">
                                    <img src="{{ $annonce->image_url }}" alt="" loading="lazy" class="w-full h-full object-cover">
                                </button>

                                @if ($annonce->is_active)
                                    @if ($annonce->is_paused)
                                        <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-full bg-amber-500/90 text-white text-[10px] font-bold pointer-events-none">متوقف</span>
                                    @else
                                        <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-full bg-emerald-500/90 text-white text-[10px] font-bold pointer-events-none">الحالي</span>
                                    @endif
                                @endif

                                <form method="POST" action="{{ route('admin.annonces.destroy', $annonce) }}"
                                      data-delete-form data-image="{{ $annonce->image_url }}"
                                      @if ($annonce->is_active) data-warning="هذا هو الإعلان الحالي، وسيتوقف عرضه للزوار بعد الحذف." @endif
                                      class="absolute top-1.5 left-1.5 lg:opacity-0 lg:group-hover:opacity-100 focus-within:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="حذف" class="w-7 h-7 rounded-full bg-red-500/90 hover:bg-red-500 text-white text-xs flex items-center justify-center">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    @if ($annonces->hasPages())
                        <div class="flex items-center justify-between mt-5 text-sm">
                            @if ($annonces->onFirstPage())
                                <span class="px-4 py-2 rounded-lg bg-white/5 text-slate-600">السابق</span>
                            @else
                                <a href="{{ $annonces->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10">السابق</a>
                            @endif
                            <span class="text-slate-500">{{ $annonces->currentPage() }} / {{ $annonces->lastPage() }}</span>
                            @if ($annonces->hasMorePages())
                                <a href="{{ $annonces->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10">التالي</a>
                            @else
                                <span class="px-4 py-2 rounded-lg bg-white/5 text-slate-600">التالي</span>
                            @endif
                        </div>
                    @endif
                @endif
            </section>

            <!-- Current annonce + Annonce form -->
            <section class="order-1 lg:order-2 space-y-4">

                @if ($current)
                    <div class="glass-card rounded-2xl p-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $current->image_url }}" alt="" class="w-20 h-14 object-cover rounded-lg shrink-0 {{ $current->is_paused ? 'opacity-50' : '' }}">
                            <div class="min-w-0 flex-1">
                                @if ($current->is_paused)
                                    <p class="text-amber-400 text-xs font-bold"><i class="fa-solid fa-circle-pause ml-1"></i> الإعلان متوقف</p>
                                    <p class="text-slate-500 text-xs mt-1">لا يظهر للزوار حاليًا</p>
                                @else
                                    <p class="text-emerald-400 text-xs font-bold"><i class="fa-solid fa-circle-check ml-1"></i> الإعلان الحالي</p>
                                    <p class="text-slate-500 text-xs mt-1">{{ $current->activated_at?->format('Y-m-d H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-3">
                            @if ($current->is_paused)
                                <form method="POST" action="{{ route('admin.annonces.resume', $current) }}">
                                    @csrf
                                    <button type="submit" class="w-full h-10 rounded-lg text-xs font-bold flex items-center justify-center gap-2 bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 hover:bg-emerald-500/20 transition-all">
                                        <i class="fa-solid fa-play"></i> استئناف
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.annonces.pause', $current) }}">
                                    @csrf
                                    <button type="submit" class="w-full h-10 rounded-lg text-xs font-bold flex items-center justify-center gap-2 bg-amber-500/10 border border-amber-500/25 text-amber-400 hover:bg-amber-500/20 transition-all">
                                        <i class="fa-solid fa-pause"></i> إيقاف مؤقت
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.annonces.destroy', $current) }}"
                                  data-delete-form data-image="{{ $current->image_url }}"
                                  data-warning="هذا هو الإعلان الحالي، وسيتوقف عرضه للزوار بعد الحذف.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full h-10 rounded-lg text-xs font-bold flex items-center justify-center gap-2 bg-red-500/10 border border-red-500/25 text-red-400 hover:bg-red-500/20 transition-all">
                                    <i class="fa-solid fa-trash"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-white/10 p-4 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-500 shrink-0">
                            <i class="fa-solid fa-eye-slash"></i>
                        </div>
                        <div>
                            <p class="text-slate-300 text-sm font-bold">لا يوجد إعلان حالي</p>
                            <p class="text-slate-500 text-xs mt-0.5">ارفع صورة أو اختر واحدة من القائمة لنشرها.</p>
                        </div>
                    </div>
                @endif

                <div class="glass-card rounded-2xl p-4 sm:p-6">
                    <h2 class="text-white font-bold text-base sm:text-lg mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-blue-400"></i>
                        Annonce
                    </h2>

                    <form id="annonceForm" method="POST" action="{{ route('admin.annonces.store') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        <input type="hidden" name="page" value="{{ $annonces->currentPage() }}">

                        <div class="relative">
                            <label for="imageInput" id="dropzone" class="dropzone flex flex-col items-center justify-center text-center px-4 py-7 sm:py-9">
                                <div id="dropzonePlaceholder" class="flex flex-col items-center gap-2">
                                    <span class="w-12 h-12 rounded-full bg-blue-500/10 text-blue-400 flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </span>
                                    <p class="text-slate-200 text-sm font-bold">
                                        اختر صورة<span class="hidden lg:inline"> أو اسحبها هنا</span>
                                    </p>
                                    <p class="text-slate-500 text-xs">JPG, PNG, WEBP, GIF — حتى 5 ميجابايت</p>
                                </div>

                                <div id="previewWrap" class="hidden w-full">
                                    <img id="imagePreview" src="" alt="" class="w-full h-44 sm:h-56 object-contain rounded-lg bg-slate-950/60">
                                    <p id="fileNameLabel" class="mt-2 text-slate-400 text-xs font-bold truncate"></p>
                                </div>
                            </label>

                            <button type="button" id="clearBtn" aria-label="إزالة الصورة"
                                    class="hidden absolute top-3 left-3 w-8 h-8 rounded-full bg-slate-950/80 border border-white/10 text-slate-300 hover:text-white items-center justify-center">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/png,image/jpeg,image/webp,image/gif" class="hidden">

                        <p id="imageError" class="field-error hidden" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span></span></p>
                        @error('image')
                            <p class="field-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $message }}</span></p>
                        @enderror

                        <div id="actionBar" class="action-bar">
                            <img id="barThumb" src="" alt="" class="w-12 h-12 rounded-lg object-cover border border-white/10 shrink-0">
                            <button type="submit" id="submitBtn" class="submit-btn" disabled>
                                <i class="fa-solid fa-bullhorn"></i>
                                Annonce
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- Delete confirmation dialog -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-end sm:items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="deleteTitle">
        <div id="deleteBackdrop" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-sm rounded-2xl bg-slate-900 border border-white/10 p-6 text-center shadow-2xl">
            <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center text-red-400 text-xl">
                <i class="fa-solid fa-trash"></i>
            </div>
            <img id="deleteImg" src="" alt="" class="mx-auto mb-4 h-28 w-28 rounded-xl object-cover border border-white/10">
            <h3 id="deleteTitle" class="text-white font-black text-lg mb-1">حذف الصورة؟</h3>
            <p id="deleteText" class="text-slate-400 text-sm mb-6"></p>
            <div class="flex gap-3">
                <button type="button" id="deleteCancel" class="flex-1 h-11 rounded-lg text-sm font-bold bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-all">إلغاء</button>
                <button type="button" id="deleteConfirm" class="flex-1 h-11 rounded-lg text-sm font-bold bg-red-500 hover:bg-red-600 text-white transition-all disabled:opacity-60 disabled:cursor-not-allowed">حذف</button>
            </div>
        </div>
    </div>

    <script>
        const MAX_BYTES = 5 * 1024 * 1024;
        const STORE_URL = @json(route('admin.annonces.store'));
        const DEFAULT_WARNING = 'سيتم حذف هذه الصورة نهائيًا ولا يمكن التراجع عن ذلك.';
        const $ = (id) => document.getElementById(id);
        const dropzone = $('dropzone'), imageInput = $('imageInput'), imagePreview = $('imagePreview'),
              placeholder = $('dropzonePlaceholder'), previewWrap = $('previewWrap'), fileNameLabel = $('fileNameLabel'),
              clearBtn = $('clearBtn'), imageError = $('imageError'), submitBtn = $('submitBtn'),
              form = $('annonceForm'), actionBar = $('actionBar'), barThumb = $('barThumb');
        let mode = 'upload';

        function setReady(ready, url = '') {
            submitBtn.disabled = !ready;
            actionBar.classList.toggle('ready', ready);
            if (ready) barThumb.src = url;
        }
        function showError(m) {
            imageError.querySelector('span').textContent = m;
            imageError.classList.remove('hidden');
            dropzone.classList.add('is-invalid');
            setReady(false);
        }
        function clearError() { imageError.classList.add('hidden'); dropzone.classList.remove('is-invalid'); }
        function clearSelectedThumb() { document.querySelectorAll('.thumb.selected').forEach(t => t.classList.remove('selected')); }

        function showPreview(url, label) {
            imagePreview.src = url;
            fileNameLabel.textContent = label;
            previewWrap.classList.remove('hidden');
            placeholder.classList.add('hidden');
            dropzone.classList.add('has-image');
            clearBtn.classList.remove('hidden'); clearBtn.classList.add('flex');
        }
        function resetPreview() {
            imagePreview.src = '';
            previewWrap.classList.add('hidden');
            placeholder.classList.remove('hidden');
            dropzone.classList.remove('has-image');
            clearBtn.classList.add('hidden'); clearBtn.classList.remove('flex');
        }
        function clearSelection() {
            mode = 'upload';
            form.action = STORE_URL;
            imageInput.value = '';
            clearError();
            clearSelectedThumb();
            resetPreview();
            setReady(false);
        }

        // Upload from device
        function handleFile(file) {
            mode = 'upload';
            form.action = STORE_URL;
            clearSelectedThumb();
            if (!file) { resetPreview(); setReady(false); return; }
            if (!file.type.startsWith('image/')) { resetPreview(); showError('الملف المختار ليس صورة.'); return; }
            if (file.size > MAX_BYTES) {
                resetPreview();
                showError(`حجم الصورة ${(file.size / 1048576).toFixed(1)} ميجابايت — الحد الأقصى هو 5 ميجابايت.`);
                return;
            }
            clearError();
            const reader = new FileReader();
            reader.onload = (e) => { showPreview(e.target.result, file.name); setReady(true, e.target.result); };
            reader.readAsDataURL(file);
        }

        // Pick an existing image (tap again to deselect); the page never scrolls
        document.querySelectorAll('.thumb').forEach((thumb) => {
            thumb.addEventListener('click', () => {
                if (thumb.classList.contains('selected')) { clearSelection(); return; }
                mode = 'existing';
                imageInput.value = '';
                clearError();
                clearSelectedThumb();
                thumb.classList.add('selected');
                form.action = thumb.dataset.activate;
                showPreview(thumb.dataset.url, 'صورة مرفوعة سابقًا');
                setReady(true, thumb.dataset.url);
            });
        });

        imageInput.addEventListener('change', () => handleFile(imageInput.files[0]));
        clearBtn.addEventListener('click', clearSelection);
        ['dragover', 'dragenter'].forEach(evt => dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.add('drag-over'); }));
        ['dragleave', 'drop'].forEach(evt => dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.remove('drag-over'); }));
        dropzone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (file) { imageInput.files = e.dataTransfer.files; handleFile(file); }
        });

        form.addEventListener('submit', (e) => {
            if (mode === 'existing') return;
            const file = imageInput.files[0];
            if (!file || file.size > MAX_BYTES || !file.type.startsWith('image/')) { e.preventDefault(); handleFile(file); }
        });

        // Delete confirmation dialog
        const deleteModal = $('deleteModal'), deleteImg = $('deleteImg'), deleteText = $('deleteText'),
              deleteCancel = $('deleteCancel'), deleteConfirm = $('deleteConfirm');
        let pendingForm = null;

        function openDelete(f) {
            pendingForm = f;
            deleteImg.src = f.dataset.image;
            deleteText.textContent = f.dataset.warning || DEFAULT_WARNING;
            deleteConfirm.disabled = false;
            deleteConfirm.textContent = 'حذف';
            deleteModal.classList.replace('hidden', 'flex');
            document.body.classList.add('overflow-hidden');
            deleteCancel.focus();
        }
        function closeDelete() {
            pendingForm = null;
            deleteModal.classList.replace('flex', 'hidden');
            document.body.classList.remove('overflow-hidden');
        }
        document.querySelectorAll('[data-delete-form]').forEach((f) =>
            f.addEventListener('submit', (e) => { e.preventDefault(); openDelete(f); }));
        deleteCancel.addEventListener('click', closeDelete);
        $('deleteBackdrop').addEventListener('click', closeDelete);
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && pendingForm) closeDelete(); });
        deleteConfirm.addEventListener('click', () => {
            if (!pendingForm) return;
            deleteConfirm.disabled = true;
            deleteConfirm.textContent = '...';
            pendingForm.submit(); // native submit skips the listener above
        });
    </script>
</body>
</html>