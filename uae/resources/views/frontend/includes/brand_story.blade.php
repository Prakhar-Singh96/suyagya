{{-- Check if Content Exists (Empty hai to section mat dikhao) --}}
@if(!empty($storyTitle) && !empty($storyContent))

<section class="py-3 brand-story-section" style="background-color: #f7f1de;">
    <div class="container">

        <div class="accordion" id="brandStoryAccordion">
            <div class="accordion-item bg-transparent border-0 border-bottom border-dark">

                {{-- 1. Dynamic Header --}}
                <h2 class="accordion-header" id="headingStory">
                    <button class="accordion-button collapsed bg-transparent shadow-none text-dark fw-bold fs-5 px-0"
                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseStory"
                        aria-expanded="false" aria-controls="collapseStory">
                        {{ $storyTitle }}
                    </button>
                </h2>

                {{-- 2. Dynamic Content --}}
                <div id="collapseStory" class="accordion-collapse collapse" aria-labelledby="headingStory"
                    data-bs-parent="#brandStoryAccordion">
                    <div class="accordion-body px-0 pt-4 brand-story-content text-secondary">
                        {{-- CKEditor HTML Content --}}
                        {!! $storyContent !!}
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endif
