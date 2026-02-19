@extends('admin.layout.layout') {{-- Apne Admin Layout ka naam check kar lein --}}

@section('title', 'Payment Gateway Settings')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Razorpay Configuration</h6>

                    {{-- Status Badge --}}
                    @if($setting->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>

                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.payment.update') }}" method="POST">
                        @csrf

                        {{-- Razorpay Logo (Optional) --}}
                        <div class="text-center mb-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/89/Razorpay_logo.svg" width="150" alt="Razorpay">
                        </div>

                        {{-- Key ID --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Razorpay Key ID <span class="text-danger">*</span></label>
                            <input type="text" name="razor_key" class="form-control"
                                   value="{{ old('razor_key', $setting->key_id) }}"
                                   placeholder="Ex: rzp_test_xxxxxxxxxx">
                        </div>

                        {{-- Key Secret --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Razorpay Key Secret <span class="text-danger">*</span></label>
                            <input type="text" name="razor_secret" class="form-control"
                                   value="{{ old('razor_secret', $setting->key_secret) }}"
                                   placeholder="Ex: xxxxxxxxxxxxxxxxxx">
                        </div>

                        {{-- Enable/Disable Toggle --}}
                        <div class="mb-4 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="statusSwitch" name="is_active"
                                   {{ $setting->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="statusSwitch">Enable Razorpay Payment Gateway</label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            Update Settings
                        </button>

                    </form>
                </div>
            </div>

            {{-- Help Text --}}
            <div class="card shadow border-left-info">
                <div class="card-body">
                    <h6 class="fw-bold text-info"><i class="fas fa-info-circle"></i> How to get keys?</h6>
                    <ol class="small text-muted ps-3 mb-0">
                        <li>Login to <a href="https://dashboard.razorpay.com/" target="_blank">Razorpay Dashboard</a>.</li>
                        <li>Go to <strong>Settings</strong> > <strong>API Keys</strong>.</li>
                        <li>Click on <strong>Generate Test Key</strong> (for local testing).</li>
                        <li>Copy Key ID and Key Secret and paste above.</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
