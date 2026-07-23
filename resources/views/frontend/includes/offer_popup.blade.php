@php
    // सिर्फ इन 3 पेजों पर पॉप-अप दिखेगा (अपने राउट्स के नाम चेक कर लेना)
    $allowedRoutes = ['home', 'product.detail', 'checkout.index'];
    $currentRoute = Route::currentRouteName();

    // डेटाबेस से सेटिंग लाओ
    $setting = \App\Models\HomePageSetting::first();
@endphp

@if(in_array($currentRoute, $allowedRoutes) && $setting && $setting->popup_status && $setting->popup_image)
    {{-- Pop-up Modal --}}
    <div class="modal fade" id="offerPopupModal" tabindex="-1" aria-hidden="true" style="z-index: 99999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 position-relative">

                {{-- Close Button --}}
                <button type="button" class="btn-close bg-white rounded-circle position-absolute top-0 end-0 m-2 shadow"
                        data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; opacity: 1; padding: 10px;"></button>

                {{-- Offer Image & Link --}}
                <a href="{{ $setting->popup_link ?? 'javascript:void(0);' }}">
                    <img src="{{ asset($setting->popup_image) }}" alt="Special Offer" class="img-fluid rounded shadow-lg w-100">
                </a>

            </div>
        </div>
    </div>

    {{-- 🧠 Smart Trigger Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 🚀 नया लॉजिक: हर पेज (Route) के लिए अलग मेमोरी की (Key) बनेगी
            var pageKey = 'suyagyaOfferPopupShown_{{ $currentRoute }}';

            if (!sessionStorage.getItem(pageKey)) {
                setTimeout(function() {
                    var offerModal = new bootstrap.Modal(document.getElementById('offerPopupModal'));
                    offerModal.show();

                    // सिर्फ इसी पेज के लिए याद रखो कि पॉप-अप दिख चुका है
                    sessionStorage.setItem(pageKey, 'true');
                }, 2500); // 2.5 सेकंड बाद खुलेगा
            }
        });
    </script>
@endif
