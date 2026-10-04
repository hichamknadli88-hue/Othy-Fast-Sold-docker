<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('1784465709672.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>تسجيل الدخول | OTHY FAST SOLD</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
        body { font-family: 'Cairo', sans-serif; background-color: #020617; }

        .glass-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #6366f1, #3b82f6);
            background-size: 200% 100%;
            animation: shimmer 4s linear infinite;
        }
        @keyframes shimmer { 0% { background-position: 0% 0; } 100% { background-position: -200% 0; } }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp .5s ease both; }

        .field-input {
            width: 100%;
            background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 0.6rem;
            padding: 0.5rem 1rem;
            color: #e2e8f0;
            font-size: 0.95rem;
            transition: border-color .2s ease, box-shadow .2s ease;
            outline: none;
        }
        .field-input.ltr { direction: ltr; text-align: left; }
        .field-input.has-toggle { padding-right: 2.75rem; }
        .field-input::placeholder { color: #64748b; }
        .field-input:focus { border-color: rgba(59, 130, 246, 0.6); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        .field-input.is-invalid { border-color: rgba(239, 68, 68, 0.6); }
        .field-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
        .field-input.is-valid { border-color: rgba(34, 197, 94, 0.55); }
        .field-input.is-valid:focus { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15); }

        .field-error {
            display: flex; align-items: center; gap: 0.4rem;
            color: #f87171; font-size: 0.78rem; font-weight: 700;
            margin-top: 0.5rem;
            max-height: 0; opacity: 0; overflow: hidden;
            transition: max-height .2s ease, opacity .2s ease;
        }
        .field-error.show { max-height: 3rem; opacity: 1; }

        .form-alert {
            display: none; align-items: flex-start; gap: 0.6rem;
            margin-bottom: 1.25rem; padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5; font-size: 0.8rem; font-weight: 700; line-height: 1.6;
        }
        .form-alert.show { display: flex; animation: fadeInUp .25s ease both; }

        .submit-btn {
            width: 100%; height: 54px; border-radius: 0.6rem;
            font-weight: 700; font-size: 0.95rem; color: #fff; background: #2563eb;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            transition: all .25s ease; border: none; cursor: pointer;
        }
        .submit-btn:hover:not(:disabled) { background: #3b82f6; transform: translateY(-1px); }
        .submit-btn:disabled { background: rgba(51, 65, 85, 0.6); color: #64748b; cursor: not-allowed; }

        .spin { animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .mode-toggle {
            display: flex; background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem; padding: 0.25rem; margin-bottom: 1.5rem;
        }
        .mode-btn {
            flex: 1; padding: 0.6rem; border-radius: 0.5rem;
            font-size: 0.85rem; font-weight: 800; color: #64748b;
            background: transparent; border: none; cursor: pointer;
            transition: all .2s ease;
        }
        .mode-btn.active { background: #2563eb; color: #fff; }

        .checkbox-row { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-row input[type="checkbox"] {
            width: 1.1rem; height: 1.1rem; accent-color: #3b82f6; cursor: pointer;
        }
        .checkbox-row label { font-size: 0.82rem; color: #94a3b8; cursor: pointer; }
        .checkbox-row.age-row input[type="checkbox"] { accent-color: #f59e0b; }
        .checkbox-row.age-row label { color: #cbd5e1; font-weight: 700; }
        .marquee-wrap { overflow: hidden; white-space: nowrap; }
        .marquee-track { display: inline-block; padding-inline-start: 100%; animation: marquee 18s linear infinite; }
        @keyframes marquee { to { transform: translateX(100%); } }
    </style>
</head>
<body class="text-slate-200 min-h-screen overflow-x-hidden flex flex-col">

    <div class="w-full bg-blue-600/10 py-2.5 border-b border-blue-500/20 text-xs font-bold text-center text-blue-400 marquee-wrap">
        <span class="marquee-track">
            سرعة، أمان، وموثوقية | تم إتمام أكثر من 200 عملية اليوم | استخدم كود OTHY للحصول على أفضل سعر!
        </span>
    </div>

    <nav class="w-full border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="text-lg font-black tracking-tight text-white">
                OTHY <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">FAST SOLD</span>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('recharge.form') }}" id="nav-cta" class="px-4 py-2.5 rounded-lg text-sm font-bold bg-blue-600 hover:bg-blue-500 transition-all shadow-lg shadow-blue-600/20 text-white">
                    لتعبئة الحساب
                </a>
                
            </div>
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">

            <div class="text-center mb-6 fade-in-up">
                <h1 id="pageTitle" class="text-3xl md:text-4xl font-black text-white tracking-tight">تسجيل الدخول</h1>
                <p id="pageSubtitle" class="text-slate-400 text-sm mt-3">أدخل بريدك الإلكتروني وكلمة المرور</p>
            </div>

            <div class="glass-card rounded-3xl p-6 md:p-8 border border-slate-700/50 fade-in-up">

                <div class="mode-toggle">
                    <button type="button" id="tabLogin" class="mode-btn active">تسجيل الدخول</button>
                    <button type="button" id="tabRegister" class="mode-btn">إنشاء حساب</button>
                </div>

                <div id="formAlert" class="form-alert" role="alert" aria-live="assertive">
                    <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                    <span id="formAlertText"></span>
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="mb-5 text-right">
                        <label for="loginEmail" class="block text-slate-300 text-sm font-bold mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" id="loginEmail" class="field-input ltr"
                               placeholder="example@mail.com" autocomplete="email" required>
                        <p id="loginEmailError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="mb-4 text-right">
                        <label for="loginPassword" class="block text-slate-300 text-sm font-bold mb-2">كلمة المرور</label>
                        <div class="relative">
                            <input type="password" name="password" id="loginPassword" class="field-input ltr has-toggle"
                                   placeholder="••••••••" autocomplete="current-password" required>
                            <button type="button" data-toggle="loginPassword" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <p id="loginPasswordError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                        <p id="loginCapsLockHint" class="hidden mt-2 text-[11px] font-bold text-amber-400">
                            <i class="fa-solid fa-triangle-exclamation"></i> مفتاح Caps Lock مفعل
                        </p>
                    </div>

                    <div class="mb-4 text-right hidden" id="loginPasswordConfirmWrap">
                        <label for="loginPasswordConfirm" class="block text-slate-300 text-sm font-bold mb-2">
                            تأكيد كلمة المرور (أول إنشاء لحساب المدير)
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="loginPasswordConfirm" class="field-input ltr has-toggle"
                                   placeholder="أعد كتابة كلمة المرور" autocomplete="new-password">
                            <button type="button" data-toggle="loginPasswordConfirm" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <p id="loginPasswordConfirmError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="checkbox-row mb-4">
                        <input type="checkbox" name="remember" id="rememberMe">
                        <label for="rememberMe">تذكرني</label>
                    </div>

                    <div class="mb-6">
                        <div class="checkbox-row age-row">
                            <input type="checkbox" name="age_confirmation" id="loginAge18" required>
                            <label for="loginAge18">أؤكد أن عمري 18 سنة فما فوق</label>
                        </div>
                        <p id="loginAge18Error" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <button type="submit" id="loginSubmitBtn" class="submit-btn" disabled>
                        <i class="fa-solid fa-right-to-bracket"></i> دخول
                    </button>
                </form>

                <form id="registerForm" method="POST" action="{{ route('register') }}" class="hidden" novalidate>
                    @csrf

                    <div class="mb-5 text-right">
                        <label for="regName" class="block text-slate-300 text-sm font-bold mb-2">الاسم الكامل</label>
                        <input type="text" name="name" id="regName" class="field-input"
                               placeholder="الاسم الكامل" autocomplete="name" required>
                        <p id="regNameError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="mb-5 text-right">
                        <label for="regEmail" class="block text-slate-300 text-sm font-bold mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" id="regEmail" class="field-input ltr"
                               placeholder="example@mail.com" autocomplete="email" required>
                        <p id="regEmailError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="mb-5 text-right">
                        <label for="regPhone" class="block text-slate-300 text-sm font-bold mb-2">رقم الهاتف</label>
                        <input type="tel" name="phone" id="regPhone" class="field-input ltr"
                               placeholder="+212600000000" autocomplete="tel" required>
                        <p id="regPhoneError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="mb-5 text-right">
                        <label for="regPassword" class="block text-slate-300 text-sm font-bold mb-2">كلمة المرور</label>
                        <div class="relative">
                            <input type="password" name="password" id="regPassword" class="field-input ltr has-toggle"
                                   placeholder="8 أحرف على الأقل" autocomplete="new-password" required>
                            <button type="button" data-toggle="regPassword" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <p id="regPasswordError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                        <p id="regCapsLockHint" class="hidden mt-2 text-[11px] font-bold text-amber-400">
                            <i class="fa-solid fa-triangle-exclamation"></i> مفتاح Caps Lock مفعل
                        </p>
                    </div>

                    <div class="mb-5 text-right">
                        <label for="regPasswordConfirm" class="block text-slate-300 text-sm font-bold mb-2">تأكيد كلمة المرور</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="regPasswordConfirm" class="field-input ltr has-toggle"
                                   placeholder="أعد كتابة كلمة المرور" autocomplete="new-password" required>
                            <button type="button" data-toggle="regPasswordConfirm" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <p id="regPasswordConfirmError" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <div class="mb-6">
                        <div class="checkbox-row age-row">
                            <input type="checkbox" name="age_confirmation" id="regAge18" required>
                            <label for="regAge18">أؤكد أن عمري 18 سنة فما فوق</label>
                        </div>
                        <p id="regAge18Error" class="field-error" role="alert" aria-live="polite">
                            <i class="fa-solid fa-circle-exclamation"></i><span></span>
                        </p>
                    </div>

                    <button type="submit" id="registerSubmitBtn" class="submit-btn" disabled>
                        <i class="fa-solid fa-user-plus"></i> إنشاء حساب
                    </button>
                </form>

            </div>
        </div>
    </main>

    <script>
        
        const $ = (id) => document.getElementById(id);
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^\+?[0-9]{8,15}$/;
        const NAME_MIN = 3;
        const PASSWORD_MIN = 8;
        const CHECK_ADMIN_URL = "{{ route('auth.check-admin') }}";

        const tabLogin = $('tabLogin');
        const tabRegister = $('tabRegister');
        const loginForm = $('loginForm');
        const registerForm = $('registerForm');
        const pageTitle = $('pageTitle');
        const pageSubtitle = $('pageSubtitle');
        const alertBox = $('formAlert');
        const alertText = $('formAlertText');

        let mode = 'login';

        function hideAlert() {
            alertBox.classList.remove('show');
            alertText.textContent = '';
        }

        function showAlert(message) {
            alertText.textContent = message;
            alertBox.classList.add('show');
        }

        function switchMode(next) {
            if (mode === next) return;
            mode = next;
            hideAlert();

            const isLogin = mode === 'login';
            loginForm.classList.toggle('hidden', !isLogin);
            registerForm.classList.toggle('hidden', isLogin);
            tabLogin.classList.toggle('active', isLogin);
            tabRegister.classList.toggle('active', !isLogin);
            pageTitle.textContent = isLogin ? 'تسجيل الدخول' : 'إنشاء حساب';
            pageSubtitle.textContent = isLogin
                ? 'أدخل بريدك الإلكتروني وكلمة المرور'
                : 'أدخل بياناتك لإنشاء حساب جديد';
        }

        tabLogin.addEventListener('click', () => switchMode('login'));
        tabRegister.addEventListener('click', () => switchMode('register'));

        document.querySelectorAll('[data-toggle]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = $(btn.dataset.toggle);
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.firstElementChild.classList.toggle('fa-eye', !show);
                btn.firstElementChild.classList.toggle('fa-eye-slash', show);
            });
        });

        function setError(input, errorEl, message) {
            if (input.dataset.serverError) return;
            errorEl.querySelector('span').textContent = message;
            errorEl.classList.toggle('show', !!message);
            input.classList.toggle('is-invalid', !!message);
            input.classList.toggle('is-valid', !message && input.value !== '');
        }

        function setCheckboxError(errorEl, message) {
            errorEl.querySelector('span').textContent = message;
            errorEl.classList.toggle('show', !!message);
        }

        function showServerError(input, errorEl, message) {
            input.dataset.serverError = '1';
            errorEl.querySelector('span').textContent = message;
            errorEl.classList.add('show');
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        }

        function clearServerError(input, errorEl) {
            if (!input.dataset.serverError) return;
            delete input.dataset.serverError;
            setError(input, errorEl, '');
        }

        function bindLiveClear(input, errorEl, validateFn) {
            input.addEventListener('input', () => {
                clearServerError(input, errorEl);
                hideAlert();
                validateFn();
            });
            input.addEventListener('blur', validateFn);
        }

        // ===== Login form =====
        const loginEmail = $('loginEmail');
        const loginPassword = $('loginPassword');
        const loginPasswordConfirmWrap = $('loginPasswordConfirmWrap');
        const loginPasswordConfirm = $('loginPasswordConfirm');
        const loginAge18 = $('loginAge18');
        const loginSubmitBtn = $('loginSubmitBtn');
        const loginSubmitHtml = loginSubmitBtn.innerHTML;
        const loginState = { email: false, password: false, passwordConfirm: true, age: false };
        let loginSubmitting = false;
        let confirmFieldVisible = false;
        let checkAdminAbort = null;
        let emailCheckTimer = null;

        function updateLoginUi() {
            loginSubmitBtn.disabled = loginSubmitting
                || !(loginState.email && loginState.password && loginState.passwordConfirm && loginState.age);
        }

        function validateLoginEmail() {
            const v = loginEmail.value.trim();
            loginState.email = emailRegex.test(v);
            let msg = '';
            if (v === '') msg = 'حقل البريد الإلكتروني مطلوب';
            else if (!loginState.email) msg = 'صيغة البريد الإلكتروني غير صحيحة';
            setError(loginEmail, $('loginEmailError'), msg);
            updateLoginUi();
        }

        function validateLoginPassword() {
            const v = loginPassword.value;
            loginState.password = v.length > 0;
            setError(loginPassword, $('loginPasswordError'), v === '' ? 'حقل كلمة المرور مطلوب' : '');
            if (confirmFieldVisible) validateLoginPasswordConfirm();
            updateLoginUi();
        }

        function validateLoginPasswordConfirm() {
            if (!confirmFieldVisible) {
                loginState.passwordConfirm = true;
                return;
            }
            const v = loginPasswordConfirm.value;
            loginState.passwordConfirm = v !== '' && v === loginPassword.value;
            let msg = '';
            if (v === '') msg = 'حقل تأكيد كلمة المرور مطلوب';
            else if (v !== loginPassword.value) msg = 'كلمتا المرور غير متطابقتين';
            setError(loginPasswordConfirm, $('loginPasswordConfirmError'), msg);
            updateLoginUi();
        }

        function validateLoginAge18() {
            loginState.age = loginAge18.checked;
            setCheckboxError($('loginAge18Error'), loginState.age ? '' : 'يجب تأكيد أن عمرك 18 سنة فما فوق للمتابعة');
            updateLoginUi();
        }
        loginAge18.addEventListener('change', () => { hideAlert(); validateLoginAge18(); });

        function setConfirmFieldVisibility(show) {
            if (confirmFieldVisible === show) return;
            confirmFieldVisible = show;
            loginPasswordConfirmWrap.classList.toggle('hidden', !show);
            if (!show) {
                loginPasswordConfirm.value = '';
                loginState.passwordConfirm = true;
                setError(loginPasswordConfirm, $('loginPasswordConfirmError'), '');
            } else {
                validateLoginPasswordConfirm();
            }
            updateLoginUi();
        }

        async function checkAdminEmail(email) {
            if (checkAdminAbort) checkAdminAbort.abort();
            checkAdminAbort = new AbortController();
            try {
                const res = await fetch(`${CHECK_ADMIN_URL}?email=${encodeURIComponent(email)}`, {
                    headers: { 'Accept': 'application/json' },
                    signal: checkAdminAbort.signal,
                });
                if (!res.ok) { setConfirmFieldVisibility(false); return; }
                const data = await res.json();
                setConfirmFieldVisibility(!!data.requiresConfirmation);
            } catch (_) {
            }
        }

        bindLiveClear(loginEmail, $('loginEmailError'), validateLoginEmail);
        bindLiveClear(loginPassword, $('loginPasswordError'), validateLoginPassword);
        bindLiveClear(loginPasswordConfirm, $('loginPasswordConfirmError'), validateLoginPasswordConfirm);

        loginEmail.addEventListener('input', () => {
            clearTimeout(emailCheckTimer);
            const v = loginEmail.value.trim();
            if (!emailRegex.test(v)) { setConfirmFieldVisibility(false); return; }
            emailCheckTimer = setTimeout(() => checkAdminEmail(v), 350);
        });

        function checkCapsLock(input, hintEl, e) {
            const on = typeof e.getModifierState === 'function' && e.getModifierState('CapsLock');
            hintEl.classList.toggle('hidden', !on);
        }
        loginPassword.addEventListener('keydown', (e) => checkCapsLock(loginPassword, $('loginCapsLockHint'), e));
        loginPassword.addEventListener('keyup', (e) => checkCapsLock(loginPassword, $('loginCapsLockHint'), e));
        loginPassword.addEventListener('blur', () => $('loginCapsLockHint').classList.add('hidden'));

        function setLoginSubmitting(on) {
            loginSubmitting = on;
            if (on) {
                loginSubmitBtn.disabled = true;
                loginSubmitBtn.innerHTML = '<i class="fa-solid fa-circle-notch spin"></i>';
            } else {
                loginSubmitBtn.innerHTML = loginSubmitHtml;
                updateLoginUi();
            }
        }

        // ===== Register form =====
        const regName = $('regName');
        const regEmail = $('regEmail');
        const regPhone = $('regPhone');
        const regPassword = $('regPassword');
        const regPasswordConfirm = $('regPasswordConfirm');
        const regAge18 = $('regAge18');
        const registerSubmitBtn = $('registerSubmitBtn');
        const registerSubmitHtml = registerSubmitBtn.innerHTML;
        const regState = { name: false, email: false, phone: false, password: false, confirm: false, age: false };
        let registerSubmitting = false;

        function updateRegisterUi() {
            registerSubmitBtn.disabled = registerSubmitting
                || !(regState.name && regState.email && regState.phone && regState.password && regState.confirm && regState.age);
        }

        function validateRegName() {
            const v = regName.value.trim();
            regState.name = v.length >= NAME_MIN;
            let msg = '';
            if (v === '') msg = 'حقل الاسم مطلوب';
            else if (!regState.name) msg = `الاسم يجب ألا يقل عن ${NAME_MIN} أحرف`;
            setError(regName, $('regNameError'), msg);
            updateRegisterUi();
        }

        function validateRegEmail() {
            const v = regEmail.value.trim();
            regState.email = emailRegex.test(v);
            let msg = '';
            if (v === '') msg = 'حقل البريد الإلكتروني مطلوب';
            else if (!regState.email) msg = 'صيغة البريد الإلكتروني غير صحيحة';
            setError(regEmail, $('regEmailError'), msg);
            updateRegisterUi();
        }

        function validateRegPhone() {
            const v = regPhone.value.trim();
            regState.phone = phoneRegex.test(v);
            let msg = '';
            if (v === '') msg = 'حقل رقم الهاتف مطلوب';
            else if (!regState.phone) msg = 'رقم الهاتف غير صحيح (8-15 رقم)';
            setError(regPhone, $('regPhoneError'), msg);
            updateRegisterUi();
        }

        function validateRegPassword() {
            const len = regPassword.value.length;
            regState.password = len >= PASSWORD_MIN;
            let msg = '';
            if (len === 0) msg = 'حقل كلمة المرور مطلوب';
            else if (!regState.password) msg = `يجب ألا تقل عن ${PASSWORD_MIN} أحرف (${len}/${PASSWORD_MIN})`;
            setError(regPassword, $('regPasswordError'), msg);
            if (regPasswordConfirm.value !== '') validateRegPasswordConfirm();
            updateRegisterUi();
        }

        function validateRegPasswordConfirm() {
            const v = regPasswordConfirm.value;
            regState.confirm = v !== '' && v === regPassword.value;
            let msg = '';
            if (v === '') msg = 'حقل تأكيد كلمة المرور مطلوب';
            else if (v !== regPassword.value) msg = 'كلمتا المرور غير متطابقتين';
            setError(regPasswordConfirm, $('regPasswordConfirmError'), msg);
            updateRegisterUi();
        }

        function validateRegAge18() {
            regState.age = regAge18.checked;
            setCheckboxError($('regAge18Error'), regState.age ? '' : 'يجب تأكيد أن عمرك 18 سنة فما فوق للمتابعة');
            updateRegisterUi();
        }
        regAge18.addEventListener('change', () => { hideAlert(); validateRegAge18(); });

        bindLiveClear(regName, $('regNameError'), validateRegName);
        bindLiveClear(regEmail, $('regEmailError'), validateRegEmail);
        bindLiveClear(regPhone, $('regPhoneError'), validateRegPhone);
        bindLiveClear(regPassword, $('regPasswordError'), validateRegPassword);
        bindLiveClear(regPasswordConfirm, $('regPasswordConfirmError'), validateRegPasswordConfirm);

        regPassword.addEventListener('keydown', (e) => checkCapsLock(regPassword, $('regCapsLockHint'), e));
        regPassword.addEventListener('keyup', (e) => checkCapsLock(regPassword, $('regCapsLockHint'), e));
        regPassword.addEventListener('blur', () => $('regCapsLockHint').classList.add('hidden'));

        function setRegisterSubmitting(on) {
            registerSubmitting = on;
            if (on) {
                registerSubmitBtn.disabled = true;
                registerSubmitBtn.innerHTML = '<i class="fa-solid fa-circle-notch spin"></i>';
            } else {
                registerSubmitBtn.innerHTML = registerSubmitHtml;
                updateRegisterUi();
            }
        }

        // ===== Shared submit handling =====
        const FIELD_MAP = {
            loginForm: {
                email: { input: loginEmail, error: $('loginEmailError') },
                password: { input: loginPassword, error: $('loginPasswordError') },
                password_confirmation: { input: loginPasswordConfirm, error: $('loginPasswordConfirmError') },
            },
            registerForm: {
                name: { input: regName, error: $('regNameError') },
                email: { input: regEmail, error: $('regEmailError') },
                phone: { input: regPhone, error: $('regPhoneError') },
                password: { input: regPassword, error: $('regPasswordError') },
                password_confirmation: { input: regPasswordConfirm, error: $('regPasswordConfirmError') },
            },
        };

        function applyServerErrors(formKey, errors) {
            const map = FIELD_MAP[formKey];
            Object.values(map).forEach((f) => {
                delete f.input.dataset.serverError;
                setError(f.input, f.error, '');
            });

            let firstField = null;
            let firstMessage = '';

            Object.keys(map).forEach((key) => {
                const messages = errors[key];
                if (!messages) return;
                const message = Array.isArray(messages) ? messages[0] : messages;
                if (!firstMessage) firstMessage = message;
                showServerError(map[key].input, map[key].error, message);
                if (!firstField) firstField = map[key].input;
            });

            showAlert(firstMessage || 'تعذر إكمال الطلب، تحقق من البيانات المدخلة.');
            if (firstField) firstField.focus();
        }

        function handleFailure(formKey, status, data, stopSubmitting) {
            stopSubmitting();

            if (status === 422 && data && data.errors) {
                applyServerErrors(formKey, data.errors);
            } else if (status === 419) {
                showAlert('انتهت صلاحية الجلسة. سيتم تحديث الصفحة...');
                setTimeout(() => location.reload(), 1500);
            } else if (status === 429) {
                showAlert('محاولات كثيرة جداً. انتظر قليلاً ثم حاول مرة أخرى.');
            } else if (status >= 500) {
                showAlert('حدث خطأ في الخادم. حاول مرة أخرى بعد قليل.');
            } else {
                showAlert('تعذر إكمال الطلب. حاول مرة أخرى.');
            }
        }

        async function submitForm(form, formKey, setSubmitting) {
            hideAlert();
            setSubmitting(true);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                let data = null;
                try { data = await response.json(); } catch (_) {}

                if (response.ok && data && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                handleFailure(formKey, response.status, data, () => setSubmitting(false));
            } catch (_) {
                setSubmitting(false);
                showAlert('تعذر الاتصال بالخادم. تحقق من اتصالك بالإنترنت وحاول مرة أخرى.');
            }
        }

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            validateLoginEmail();
            validateLoginPassword();
            if (confirmFieldVisible) validateLoginPasswordConfirm();
            validateLoginAge18();
            if (!(loginState.email && loginState.password && loginState.passwordConfirm && loginState.age)) return;
            submitForm(loginForm, 'loginForm', setLoginSubmitting);
        });

        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            validateRegName();
            validateRegEmail();
            validateRegPhone();
            validateRegPassword();
            validateRegPasswordConfirm();
            validateRegAge18();
            if (!(regState.name && regState.email && regState.phone && regState.password && regState.confirm && regState.age)) return;
            submitForm(registerForm, 'registerForm', setRegisterSubmitting);
        });

        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                setLoginSubmitting(false);
                setRegisterSubmitting(false);
            }
        });

        updateLoginUi();
        updateRegisterUi();
        function revalidateAll() {
            if (loginEmail.value) validateLoginEmail();
            if (loginPassword.value) validateLoginPassword();
            if (regEmail.value) validateRegEmail();
            if (regPassword.value) validateRegPassword();
        }
        ['change', 'animationstart'].forEach(evt => {
            [loginEmail, loginPassword].forEach(el => el.addEventListener(evt, revalidateAll));
        });
        window.addEventListener('load', () => setTimeout(revalidateAll, 300));
        window.addEventListener('pageshow', revalidateAll);
    </script>
</body>
</html>
