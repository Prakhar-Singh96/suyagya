@extends('frontend.layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- 1. Search Box --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 text-center">
                    <h3 class="fw-bold mb-3">Track Your Order</h3>
                    <p class="text-muted mb-4">Enter your AWB / Tracking ID to see live status.</p>

                    <form action="{{ route('track.order.submit') }}" method="POST" class="d-flex gap-2 justify-content-center">
                        @csrf
                        <input type="text" name="awb_number" class="form-control w-50" placeholder="Enter Tracking ID (e.g. 953064324...)" required value="{{ $awb ?? '' }}">
                        <button type="submit" class="btn btn-primary px-4">Track</button>
                    </form>

                    @if(session('error'))
                        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
                    @endif
                </div>
            </div>

            {{-- 2. Tracking Result Section --}}
            @if(isset($trackingData))
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tracking: <span class="text-primary">{{ $awb }}</span></h5>

                        {{-- Current Status Header --}}
                        @if(isset($trackingData['order_detail']['current_tracking_status']))
                            <span class="badge bg-success">
                                {{ $trackingData['order_detail']['current_tracking_status'] }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body">

                        {{-- Top Info Box --}}
                        <div class="row mb-4 bg-light p-3 rounded mx-1">
                            <div class="col-6">
                                <small class="text-muted">Courier Name</small>
                                <h6 class="fw-bold">{{ $trackingData['order_detail']['courier_name'] ?? 'N/A' }}</h6>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted">Manifest Date</small>
                                <h6 class="fw-bold">{{ $trackingData['order_detail']['order_manifest_datetime'] ?? 'N/A' }}</h6>
                            </div>
                        </div>

                        {{-- Timeline --}}
                        <div class="tracking-timeline px-2">
                            @if(isset($trackingData['scan_histories']) && count($trackingData['scan_histories']) > 0)

                                {{-- Loop through history --}}
                                @foreach($trackingData['scan_histories'] as $scan)
                                    <div class="d-flex mb-4 position-relative">

                                        {{-- Date & Time --}}
                                        <div class="me-4 text-end" style="min-width: 100px;">
                                            <div class="fw-bold text-dark" style="font-size: 14px;">
                                                {{ date('d M Y', strtotime($scan['scan_datetime'])) }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ date('H:i A', strtotime($scan['scan_datetime'])) }}
                                            </div>
                                        </div>

                                        {{-- Timeline Line & Dot --}}
                                        <div class="position-relative">
                                            <div class="timeline-dot bg-primary border border-white border-2 rounded-circle"
                                                 style="width: 16px; height: 16px; position: relative; z-index: 2;"></div>

                                            {{-- Vertical Line (Connects dots) --}}
                                            @if(!$loop->last)
                                                <div class="timeline-line bg-secondary opacity-25"
                                                     style="width: 2px; position: absolute; top: 16px; left: 7px; height: 100%; z-index: 1;"></div>
                                            @endif
                                        </div>

                                        {{-- Details --}}
                                        <div class="ms-4 pb-2">
                                            <h6 class="fw-bold mb-1 text-uppercase text-primary">{{ $scan['scan_status'] }}</h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="las la-map-marker"></i> {{ $scan['scan_location'] }}
                                            </p>
                                            @if(!empty($scan['scan_remarks']))
                                                <p class="mb-0 text-muted x-small fst-italic">
                                                    "{{ $scan['scan_remarks'] }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="las la-truck fs-1 mb-2"></i>
                                    <p>Tracking information received, but no scans yet.</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
