<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Suyagya</title>

    {{-- 🔗 Bootstrap 5 CSS --}}
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        /* 🎨 CUSTOM CSS FOR LOGIN PAGE */
        body {
            background-color: #ffffff;
            font-family: 'Open Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 950px;
            background: #fff;
            border: 1px solid #f1f1f1;
            box-shadow: 0 0 30px rgba(0,0,0,0.02);
            border-radius: 12px;
            overflow: hidden;
        }

        /* Left Side (Illustration) */
        .login-left-side {
            background-color: #F3F5F9; /* Light purple/gray tint */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-left-side img {
            max-width: 100%;
            height: auto;
            /* If you have a specific vector image like the screenshot, put it here */
        }

        /* Right Side (Form) */
        .login-right-side {
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-logo {
            height: 50px;
            margin-bottom: 20px;
        }

        .login-title {
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 1.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        /* Input Fields Styling (Like Screenshot) */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #333;
        }

        .form-control {
            background-color: #E8F0FE; /* Light blue background for inputs */
            border: none;
            border-radius: 4px;
            height: 45px;
            padding-left: 15px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            background-color: #E8F0FE;
            box-shadow: none;
            border: 1px solid #ccc;
        }

        /* Button Styling */
        .btn-black {
            background-color: #000;
            color: #fff;
            font-weight: 600;
            width: 100%;
            height: 45px;
            border-radius: 4px;
            text-transform: uppercase;
            font-size: 0.9rem;
            transition: background 0.3s;
        }
        .btn-black:hover {
            background-color: #333;
            color: #fff;
        }

        /* Back Link */
        .back-link {
            margin-top: 30px;
            text-align: right;
            display: block;
            font-size: 0.85rem;
            color: #000;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        .input-group-text {
            background-color: #E8F0FE;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="row g-0">

            {{-- 🖼️ LEFT SIDE: Illustration --}}
            <div class="col-md-6 login-left-side d-none d-md-flex">
                {{-- You can replace this URL with your specific vector image if you have one --}}
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/login-3305943-2757111.png" alt="Login Illustration">
            </div>

            {{-- 📝 RIGHT SIDE: Login Form --}}
            <div class="col-md-6 login-right-side">

                {{-- Logo --}}
                <div class="text-start">
                    <img src="https://suyagya.com/public/uploads/all/ackLS169wFEfhj8jfhnb0SGblGIHug1XfDCg7WIs.webp" alt="Suyagya" class="brand-logo">
                </div>

                <h3 class="login-title">Welcome to Suyagya</h3>
                <p class="login-subtitle">Login to your account.</p>

                {{-- Form --}}
                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required autofocus>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="las la-eye" id="eye-icon"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                            <label class="form-check-label text-muted small" for="remember_me">
                                Remember Me
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="text-muted small text-decoration-underline" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-black">
                        Login
                    </button>

                </form>

            </div>
        </div>
    </div>
</body>
</html>
