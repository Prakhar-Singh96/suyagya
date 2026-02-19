@if($addresses->count() > 0)
    <h6 class="fw-bold mb-3">Select Delivery Address</h6>

    <div class="address-scroll mb-3" style="max-height: 250px; overflow-y: auto;">
        @foreach($addresses as $addr)
            <label class="card p-3 mb-2 border cursor-pointer shadow-sm position-relative"
                   onclick="$('.saved-addr-radio').prop('checked', false); $(this).find('input').prop('checked', true);">
                <div class="d-flex">
                    <div class="me-3 mt-1">
                        <input class="form-check-input saved-addr-radio" type="radio" name="selected_address"
                               value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }}>
                    </div>
                    <div>
                        <span class="fw-bold text-dark">{{ $addr->name }}</span>
                        <span class="badge bg-light text-dark border ms-2" style="font-size: 10px;">{{ strtoupper($addr->type) }}</span>
                        <p class="text-muted small mb-1 mt-1 text-wrap" style="line-height: 1.4;">
                            {{ $addr->address_line1 }}, {{ $addr->city }} - <strong>{{ $addr->pincode }}</strong>
                        </p>
                        <small class="text-dark fw-bold">📱 {{ $addr->phone }}</small>
                    </div>
                </div>
            </label>
        @endforeach
    </div>

    <button type="button" class="btn btn-warning w-100 py-3 rounded-3 fw-bold text-white mb-3"
            style="background-color: #ff6f00; border: none;" onclick="useSavedAddress()">
        DELIVER HERE
    </button>

    <button type="button" class="btn btn-outline-dark w-100 py-2 border-dashed text-uppercase x-small fw-bold"
            onclick="$('#saved_address_list').slideUp(); $('#new_address_form').slideDown();">
        + Add New Address
    </button>
@else
    {{-- Agar koi address nahi hai to JS handle karega --}}
@endif
