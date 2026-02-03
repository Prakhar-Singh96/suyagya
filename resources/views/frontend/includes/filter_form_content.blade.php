{{-- Price Filter --}}
<div class="filter-group border-bottom py-3">
    <a class="d-flex justify-content-between align-items-center text-dark text-decoration-none fw-bold mb-3"
        data-bs-toggle="collapse" href="#collapsePrice" role="button">
        Price <i class="las la-angle-down"></i>
    </a>
    <div class="collapse show" id="collapsePrice">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="position-relative w-100">
                <span class="position-absolute text-muted small" style="left: 8px; top: 7px;">₹</span>
                <input type="number" name="min_price" id="input-min" class="form-control form-control-sm ps-3 shadow-none"
                       placeholder="0" value="{{ request('min_price') }}">
            </div>
            <span class="text-muted">-</span>
            <div class="position-relative w-100">
                <span class="position-absolute text-muted small" style="left: 8px; top: 7px;">₹</span>
                <input type="number" name="max_price" id="input-max" class="form-control form-control-sm ps-3 shadow-none"
                       placeholder="Max" value="{{ request('max_price') }}">
            </div>
            <button type="submit" class="btn btn-dark btn-sm rounded-1 px-3"><i class="las la-angle-right"></i></button>
        </div>
        {{-- 🚀 ये रही वो लाइन जो मिसिंग थी! --}}
        <div class="px-2 pb-3">
            <div class="price-slider"></div> {{-- यहाँ class="price-slider" होना ज़रूरी है --}}
        </div>
    </div>
</div>

{{-- Dynamic Filters (Mukhi, Bead, etc.) --}}
@foreach ($filters as $filter)
    <div class="filter-group border-bottom py-3">
        <a class="d-flex justify-content-between align-items-center text-dark text-decoration-none fw-bold mb-2"
            data-bs-toggle="collapse" href="#collapse{{ $filter->id }}" role="button">
            {{ $filter->name }} <i class="las la-angle-down"></i>
        </a>
        <div class="collapse show" id="collapse{{ $filter->id }}">
            <div class="filter-options mt-2">
                @foreach ($filter->filterValues as $value)
                    @php
                        $isChecked = request('filter') && isset(request('filter')[$filter->id]) && in_array($value->id, request('filter')[$filter->id]);
                    @endphp
                    <div class="form-check mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <input class="form-check-input filter-checkbox shadow-none" type="checkbox"
                                   name="filter[{{ $filter->id }}][]" value="{{ $value->id }}"
                                   id="val_{{ $value->id }}" {{ $isChecked ? 'checked' : '' }}
                                   onchange="this.form.submit()">
                            <label class="form-check-label text-muted small ms-1" for="val_{{ $value->id }}">
                                {{ $value->value }}
                            </label>
                        </div>
                        <span class="text-muted x-small">({{ $value->products_count }})</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
