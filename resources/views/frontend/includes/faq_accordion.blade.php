@if(!empty($faqs) && count($faqs) > 0)
    {{-- Parent Accordion Wrapper --}}
    <div class="accordion" id="faqAccordion{{ $idSuffix ?? '' }}">

        @foreach($faqs as $index => $faq)
            @php
                // Data extract karein (Array ya Object handle karne ke liye)
                $q = is_array($faq) ? ($faq['question'] ?? '') : $faq->question;
                $a = is_array($faq) ? ($faq['answer'] ?? '') : $faq->answer;

                // Unique ID generate karein
                $uid = ($idSuffix ?? 'gen') . $index;
            @endphp

            @if(!empty($q))
                {{-- ITEM WRAPPER (Aapka Purana Class Structure) --}}
                <div class="faq-item mb-3">

                    {{-- HEADER --}}
                    <h2 class="accordion-header" id="heading{{ $uid }}">
                        <button class="accordion-button faq-btn collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $uid }}"
                                aria-expanded="false"
                                aria-controls="collapse{{ $uid }}" style="margin-left: 10px;">
                            {{ $q }}
                        </button>
                    </h2>

                    {{-- BODY --}}
                    <div id="collapse{{ $uid }}" class="accordion-collapse collapse"
                         aria-labelledby="heading{{ $uid }}"
                         data-bs-parent="#faqAccordion{{ $idSuffix ?? '' }}">
                        <div class="accordion-body faq-answer">
                            {!! $a !!}
                        </div>
                    </div>

                </div>
            @endif
        @endforeach

    </div>
@endif
