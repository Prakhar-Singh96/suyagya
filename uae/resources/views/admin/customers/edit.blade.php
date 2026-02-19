@extends('admin.layout.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Users /</span> Edit Customer & KYC
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">User Information</h5>
                    <form action="{{ route('admin.customers.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                {{-- Basic Details --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control" type="text" name="name" value="{{ $user->name }}"
                                        required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">E-mail</label>
                                    <input class="form-control" type="email" name="email" value="{{ $user->email }}"
                                        required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input class="form-control" type="text" name="phone" value="{{ $user->phone }}" />
                                </div>

                                {{-- User Type Switch --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">User Role / Type</label>
                                    <select id="user_type" name="user_type" class="select2 form-select">
                                        <option value="customer" {{ $user->user_type == 'customer' ? 'selected' : '' }}>
                                            Standard Customer</option>
                                        <option value="affiliate" {{ $user->user_type == 'affiliate' ? 'selected' : '' }}>
                                            Affiliate (Pandit Ji / Influencer)</option>
                                    </select>
                                    <small class="text-warning">* Affiliate users get 10% commission on referrals.</small>
                                </div>
                                {{-- Status Switch --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Account Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <small class="text-muted">* Inactive users won't be able to login.</small>
                                </div>
                            </div>

                            {{-- 🚀 KYC & BANK DETAILS SECTION (Hidden by default if not affiliate) --}}
                            <div id="affiliate_details_section"
                                style="{{ $user->user_type == 'affiliate' ? '' : 'display:none;' }}">
                                <hr class="my-4">
                                <h5 class="mb-4 text-primary"><i class="bx bx-id-card me-1"></i> KYC & Bank Details</h5>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">PAN Card Number</label>
                                        <input type="text" name="pan_card_no" class="form-control"
                                            placeholder="ABCDE1234F" value="{{ $user->details->pan_card_no ?? '' }}" />
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Aadhar Number</label>
                                        <input type="text" name="aadhar_card_no" class="form-control"
                                            placeholder="1234 5678 9012"
                                            value="{{ $user->details->aadhar_card_no ?? '' }}" />
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control"
                                            placeholder="HDFC, SBI, etc." value="{{ $user->details->bank_name ?? '' }}" />
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" name="account_no" class="form-control"
                                            value="{{ $user->details->account_no ?? '' }}" />
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">IFSC Code</label>
                                        <input type="text" name="ifsc_code" class="form-control"
                                            placeholder="HDFC0001234" value="{{ $user->details->ifsc_code ?? '' }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 🚀 JavaScript to toggle KYC section --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeSelect = document.getElementById('user_type');
            const affiliateSection = document.getElementById('affiliate_details_section');

            userTypeSelect.addEventListener('change', function() {
                if (this.value === 'affiliate') {
                    affiliateSection.style.display = 'block';
                } else {
                    affiliateSection.style.display = 'none';
                }
            });
        });
    </script>
@endsection
