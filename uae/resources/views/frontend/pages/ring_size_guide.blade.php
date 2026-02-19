@extends('frontend.layouts.app')

@section('title', 'Ring Size Calculator | Suyagya')

@section('styles')
    <style>
        /* ✨ Premium Font */
        .font-serif {
            font-family: 'Merriweather', serif;
        }

        /* 🖼️ Images */
        .split-image {
            width: 100%;
            height: 100%;
            min-height: 500px;
            object-fit: cover;
        }

        /* 💎 Canvas Tool */
        #ring-size-canvas {
            background-color: transparent;
            cursor: ew-resize;
        }

        /* 🎚️ Custom Range Slider (Fixed Size) */
        .custom-range {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: #e0e0e0;
            border-radius: 5px;
            outline: none;
            cursor: pointer;
        }

        /* Chrome/Safari Thumb */
        .custom-range::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            /* 🔥 SIZE KAM KIYA (Pehle 25px tha) */
            height: 18px;
            border-radius: 50%;
            background: #000;
            border: 2px solid #d4af37;
            margin-top: -7px;
            /* Center align */
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            /* Halo effect */
            transition: transform 0.1s;
        }

        .custom-range::-webkit-slider-thumb:active {
            transform: scale(1.2);
        }

        /* Firefox Thumb */
        .custom-range::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            background: #000;
            cursor: pointer;
        }

        /* Icons */
        .icon-circle {
            width: 80px;
            height: 80px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-size: 35px;
            color: #d4af37;
        }

        .step-text h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .step-text p {
            color: #666;
            font-size: 1rem;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Table */
        .size-table th {
            background: #000;
            color: #fff;
        }

        .size-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* मोबाइल के लिए कैनवास और ग्रिड बैकग्राउंड का साइज एडजस्ट करें */
        @media (max-width: 767px) {
            #ring-size-canvas,
            .position-relative.d-inline-block.mb-4 div[style*="width: 300px"] {
                width: 320px !important;
                height: 320px !important;
            }
            .fs-1 { font-size: 3rem !important; } /* मोबाइल पर साइज नंबर बड़ा दिखे */
        }
    </style>
@endsection

@section('content')

    {{-- 💎 METHOD 1: VIRTUAL SIZER --}}
    <section class="w-100 overflow-hidden">
        {{-- Header Section (Centered & Clean) --}}
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <span class="text-uppercase text-warning fw-bold ls-2 small">Method 1</span>
                    <h1 class="font-serif display-5 fw-bold mt-2 mb-3">Ring size Guide</h1>
                    <p class="text-muted lead mx-auto" style="max-width: 600px;">
                        Use our virtual ring sizer to measure your existing ring instantly.
                    </p>
                </div>
            </div>
        </div>
        <div class="row g-0">

            {{-- LEFT: IMAGE --}}
            <div class="col-lg-6 order-2 order-lg-1">
                <img src="{{ asset('assets/img/1.png') }}" alt="Ring Sizer Model" class="split-image">
            </div>

            {{-- RIGHT: TOOL --}}
            <div class="col-lg-6 order-1 order-lg-2 d-flex align-items-center justify-content-center p-5">
                <div class="text-center w-100" style="max-width: 550px;">

                    <h5 class="text-uppercase text-muted small ls-2 mb-2">Method 1</h5>
                    <h2 class="font-serif fw-bold display-5 mb-5">Find Your Ring Size</h2>

                    {{-- Canvas Area --}}
                    <div class="position-relative d-inline-block mb-4">
                        {{-- Grid Background --}}
                        <div
                            style="background-image: radial-gradient(#ddd 1px, transparent 1px); background-size: 20px 20px; width: 300px; height: 300px; position: absolute; left:0; top:0; z-index:0; opacity: 0.4; border-radius: 10px;">
                        </div>

                        <canvas id="ring-size-canvas" width="300" height="300"
                            style="position: relative; z-index: 1;"></canvas>

                        {{-- Size Text Overlay (Perfectly Centered) --}}
                        <div class="position-absolute top-50 start-50 translate-middle pointer-events-none text-center"
                            style="z-index: 0;">
                            <span class="text-secondary fw-bold"
                                style="font-size: 12px; letter-spacing: 1px;">SIZE</span><br>
                            <span class="fs-1 fw-bold text-dark" id="displaySize" style="line-height: 1;">10</span>
                        </div>
                    </div>

                    {{-- Slider --}}
                    <div class="mb-5 px-4">
                        <input type="range" class="custom-range" id="ringSlider" min="1" max="35"
                            step="1" value="10">
                        <div class="d-flex justify-content-between text-muted small mt-2 fw-bold text-uppercase">
                            <span>Smaller</span>
                            <span>Larger</span>
                        </div>
                    </div>

                    <div class="step-text mb-4">
                        <h3>How to use?</h3>
                        <p>Place your ring on the screen. Move the slider until the <strong>inner circle</strong> of your
                            ring matches the yellow outline perfectly.</p>
                    </div>

                    {{-- Steps --}}
                    <div class="d-flex flex-column gap-4 text-center">
                        <div class="step-text">
                            <h3>Step 1</h3>
                            <p>Place your existing ring on the circle outline above.</p>
                        </div>
                        <div class="step-text">
                            <h3>Step 2</h3>
                            <p>Use the slider to adjust the size until the <strong>yellow circle</strong> fits perfectly
                                inside your ring.</p>
                        </div>
                        <div class="step-text">
                            <h3>Step 3</h3>
                            <p>The number shown in the center is your Indian Ring Size.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- 📏 METHOD 2: MANUAL MEASURE --}}
    <section class="w-100 overflow-hidden">
        <div class="container py-5">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <span class="text-uppercase text-warning fw-bold ls-2 small">Method 2</span>
                    <h1 class="font-serif display-5 fw-bold mt-2 mb-3">Measure Your Finger</h1>
                    <p class="text-muted lead mx-auto" style="max-width: 600px;">
                        Use our virtual ring sizer to measure your existing ring instantly.
                    </p>
                </div>
            </div>
        </div>
        <div class="row g-0">
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5">
                <div class="text-center w-100" style="max-width: 550px;">
                    <h5 class="text-uppercase text-muted small ls-2 mb-2">Method 2</h5>
                    <h2 class="font-serif fw-bold display-5 mb-5">Measure Your Finger</h2>
                    <div class="icon-circle"><i class="las la-tape"></i></div>
                    <div class="step-text mb-4">
                        <p>Wrap a string around your finger, mark the overlap, measure the length in <strong>mm</strong> and
                            enter below.</p>
                    </div>

                    <div class="d-flex flex-column gap-4 text-center mb-5">
                        <div class="step-text">
                            <h3>Step 1</h3>
                            <p>Wrap a thin strip of paper or non-stretchy string around the base of the finger you want to
                                measure.</p>
                        </div>
                        <div class="step-text">
                            <h3>Step 2</h3>
                            <p>Mark the point where the paper/string overlaps with a pen.</p>
                        </div>
                        <div class="step-text">
                            <h3>Step 3</h3>
                            <p>Measure the length in Millimeters (mm) and enter it below.</p>
                        </div>
                    </div>

                    {{-- Calculator --}}
                    <div class="bg-white p-4 shadow-sm rounded border text-center mx-auto" style="max-width: 400px;">
                        <label class="fw-bold mb-2">Enter Circumference (mm)</label>
                        <div class="input-group mb-3">
                            <input type="number" id="manualInput" class="form-control text-center fs-5" placeholder="52"
                                oninput="calculateFromInput()">
                            <span class="input-group-text">mm</span>
                        </div>
                        <div id="manualResult" class="fw-bold text-success" style="display: none;">
                            Your Size: <span class="fs-2 text-dark" id="calcSizeResult">12</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/img/2.png') }}" alt="Measure Finger" class="split-image">
            </div>
        </div>
    </section>

    {{-- 📊 CHART --}}
    <section class="py-5">
        <div class="container text-center">
            <h3 class="font-serif fw-bold mb-4">Ring Size Chart</h3>
            <div class="table-responsive d-inline-block shadow-sm" style="max-width: 800px;">
                <table class="table table-hover text-center size-table border mb-0">
                    <thead>
                        <tr>
                            <th class="py-3">Size</th>
                            <th class="py-3">Diameter (mm)</th>
                            <th class="py-3">Circumference (mm)</th>
                        </tr>
                    </thead>
                    <tbody id="chartTableBody"></tbody>
                </table>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script>
        const ringData = {
            1: {
                mm: 13.0,
                circ: 41
            },
            2: {
                mm: 13.4,
                circ: 42
            },
            3: {
                mm: 13.7,
                circ: 43
            },
            4: {
                mm: 14.0,
                circ: 44
            },
            5: {
                mm: 14.3,
                circ: 45
            },
            6: {
                mm: 14.6,
                circ: 46
            },
            7: {
                mm: 15.0,
                circ: 47
            },
            8: {
                mm: 15.3,
                circ: 48
            },
            9: {
                mm: 15.6,
                circ: 49
            },
            10: {
                mm: 15.9,
                circ: 50
            },
            11: {
                mm: 16.2,
                circ: 51
            },
            12: {
                mm: 16.5,
                circ: 52
            },
            13: {
                mm: 16.8,
                circ: 53
            },
            14: {
                mm: 17.2,
                circ: 54
            },
            15: {
                mm: 17.5,
                circ: 55
            },
            16: {
                mm: 17.8,
                circ: 56
            },
            17: {
                mm: 18.1,
                circ: 57
            },
            18: {
                mm: 18.4,
                circ: 58
            },
            19: {
                mm: 18.8,
                circ: 59
            },
            20: {
                mm: 19.1,
                circ: 60
            },
            21: {
                mm: 19.4,
                circ: 61
            },
            22: {
                mm: 19.7,
                circ: 62
            },
            23: {
                mm: 20.1,
                circ: 63
            },
            24: {
                mm: 20.3,
                circ: 64
            },
            25: {
                mm: 20.6,
                circ: 65
            },
            26: {
                mm: 21.0,
                circ: 66
            },
            27: {
                mm: 21.3,
                circ: 67
            },
            28: {
                mm: 21.6,
                circ: 68
            },
            29: {
                mm: 22.0,
                circ: 69
            },
            30: {
                mm: 22.3,
                circ: 70
            },
            31: {
                mm: 22.6,
                circ: 71
            },
            32: {
                mm: 22.9,
                circ: 72
            },
            33: {
                mm: 23.2,
                circ: 73
            },
            34: {
                mm: 23.5,
                circ: 74
            },
            35: {
                mm: 23.9,
                circ: 75
            }
        };

        const canvas = document.getElementById('ring-size-canvas');
        const ctx = canvas.getContext('2d');
        const slider = document.getElementById('ringSlider');
        const displaySize = document.getElementById('displaySize');

        // 🔥 SCALE BADHA DIYA (4.5) taki ring screen par badi dikhe aur text uske andar aa jaye
        // 🔥 SCALE FIX: Mobile के लिए scale बढ़ा दिया गया है
        let PIXELS_PER_MM = window.innerWidth < 768 ? 7.5 : 4.5;

        // अगर स्क्रीन साइज बदले तो ऑटो-एडजस्ट करने के लिए
        window.addEventListener('resize', () => {
            PIXELS_PER_MM = window.innerWidth < 768 ? 7.5 : 4.5;
            drawRing(slider.value);
        });

        function drawRing(size) {
            const data = ringData[size];
            if (!data) return;

            const cx = canvas.width / 2;
            const cy = canvas.height / 2;
            const diameterMm = data.mm;
            const radiusPx = (diameterMm / 2) * PIXELS_PER_MM;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // 1. Draw Ring (Thicker & Gold)
            ctx.beginPath();
            ctx.arc(cx, cy, radiusPx, 0, 2 * Math.PI);
            ctx.strokeStyle = '#d4af37';
            ctx.lineWidth = 5; // Thoda mota kiya
            ctx.stroke();

            // 2. Draw Diamond (Visual only, Thoda upar kiya)
            const dH = 15;
            const dW = 20;
            // 🔥 GAP Added: 'radiusPx + 4' taki wo ring se chipke nahi
            let topY = cy - radiusPx - 2;

            ctx.beginPath();
            ctx.fillStyle = '#d4af37';
            ctx.moveTo(cx, topY);
            ctx.lineTo(cx + dW / 2, topY - dH / 2);
            ctx.lineTo(cx + dW / 2, topY - dH);
            ctx.lineTo(cx - dW / 2, topY - dH);
            ctx.lineTo(cx - dW / 2, topY - dH / 2);
            ctx.closePath();
            ctx.fill();

            displaySize.innerText = size;
            highlightTableRow(size);
        }

        slider.addEventListener('input', function() {
            drawRing(this.value);
        });

        // 🚀 Default Size 10 set kiya taki text overlap na ho
        document.addEventListener("DOMContentLoaded", function() {
            slider.value = 10;
            drawRing(10);
        });

        function calculateFromInput() {
            const inputVal = parseFloat(document.getElementById('manualInput').value);
            const resultBox = document.getElementById('manualResult');
            const sizeSpan = document.getElementById('calcSizeResult');

            if (!inputVal || inputVal < 40 || inputVal > 80) {
                resultBox.style.display = 'none';
                return;
            }

            let closestSize = 1;
            let minDiff = 100;
            for (const [size, data] of Object.entries(ringData)) {
                let diff = Math.abs(data.circ - inputVal);
                if (diff < minDiff) {
                    minDiff = diff;
                    closestSize = size;
                }
            }
            sizeSpan.innerText = closestSize;
            resultBox.style.display = 'block';
            slider.value = closestSize;
            drawRing(closestSize);
        }

        const tableBody = document.getElementById('chartTableBody');

        function highlightTableRow(size) {
            document.querySelectorAll('.size-table tr').forEach(tr => {
                tr.classList.remove('table-warning', 'fw-bold');
                tr.style.backgroundColor = '';
            });
            const row = document.getElementById('row-' + size);
            if (row) {
                row.style.backgroundColor = '#fff3cd';
                row.classList.add('fw-bold');
            }
        }

        for (const [size, data] of Object.entries(ringData)) {
            const row = document.createElement('tr');
            row.id = 'row-' + size;
            row.innerHTML = `<td>${size}</td><td>${data.mm}</td><td>${data.circ}</td>`;
            tableBody.appendChild(row);
        }
    </script>
@endsection
