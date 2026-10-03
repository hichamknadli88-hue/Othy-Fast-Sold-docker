@if ($annonce ?? null)
    <div id="annoncePopup" class="annonce-popup hidden" role="dialog" aria-modal="true" aria-label="إعلان">
        <div class="annonce-popup-backdrop" data-annonce-dismiss></div>
        <div class="annonce-popup-panel">
            <button type="button" class="annonce-popup-close" data-annonce-dismiss aria-label="إخفاء الإعلان">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img src="{{ $annonce->image_url }}" alt="" class="annonce-popup-image">
            <button type="button" class="annonce-popup-hide-btn" data-annonce-dismiss>
                إخفاء الإعلان
            </button>
        </div>
    </div>

    <style>
        .annonce-popup {
            position: fixed;
            inset: 0;
            z-index: 10050;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .annonce-popup.hidden {
            display: none !important;
        }
        .annonce-popup-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, 0.82);
            backdrop-filter: blur(6px);
        }
        .annonce-popup-panel {
            position: relative;
            z-index: 1;
            width: min(100%, 520px);
            max-height: min(90vh, 720px);
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
            animation: annoncePopIn 0.25s ease;
        }
        @keyframes annoncePopIn {
            from { transform: scale(0.96); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .annonce-popup-close {
            position: absolute;
            top: -0.25rem;
            left: -0.25rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: rgba(15, 23, 42, 0.95);
            color: #e2e8f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: background 0.2s ease, color 0.2s ease;
            z-index: 2;
        }
        .annonce-popup-close:hover {
            background: rgba(30, 41, 59, 1);
            color: #fff;
        }
        .annonce-popup-image {
            width: 100%;
            min-height: 120px;
            max-height: min(70vh, 560px);
            object-fit: contain;
            border-radius: 1rem;
            border: 1px solid rgba(59, 130, 246, 0.25);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
            background: #0f172a;
        }
        .annonce-popup-hide-btn {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 0.65rem;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .annonce-popup-hide-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }
    </style>

    <script>
        (function () {
            const popup = document.getElementById('annoncePopup');
            if (!popup) return;

            function hideAnnonce() {
                popup.classList.add('hidden');
                document.body.style.overflow = '';
            }

            popup.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            popup.querySelectorAll('[data-annonce-dismiss]').forEach((el) => {
                el.addEventListener('click', hideAnnonce);
            });

            document.addEventListener('keydown', function onKey(e) {
                if (e.key === 'Escape') {
                    hideAnnonce();
                    document.removeEventListener('keydown', onKey);
                }
            });
        })();
    </script>
@endif
