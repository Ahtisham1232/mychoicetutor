<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Online Tutor Admin Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,600,800');

        * {
            box-sizing: border-box;
        }

        body {
            background: #f4ecf7;
            /* Soft purple tint background to match your brand */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
        }

        h1 {
            font-weight: 800;
            margin: 0 0 10px 0;
            color: #333;
        }

        p {
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            letter-spacing: 0.5px;
            margin: 0 0 20px 0;
            color: #666;
        }

        .text-danger {
            font-size: 12px;
            color: #e74c3c;
            align-self: flex-start;
            margin-bottom: 5px;
            display: block;
        }

        a {
            color: #8e44ad;
            font-size: 14px;
            text-decoration: none;
            margin: 15px 0;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #6a307d;
        }

        .button {
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            color: #FFFFFF;
            font-size: 13px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 80ms ease-in, box-shadow 0.2s ease;
            box-shadow: 0 4px 10px rgba(142, 68, 173, 0.3);
        }

        .button:hover {
            box-shadow: 0 6px 15px rgba(142, 68, 173, 0.4);
        }

        .button:active {
            transform: scale(0.95);
        }

        /* Two-Column Split Container */
        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.1), 0 10px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            width: 850px;
            max-width: 95%;
            min-height: 500px;
            overflow: hidden;
        }

        /* left column (image) -> Equivalent to col-md-6 */
        .image-container {
            flex: 1;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: #ffffff;
            text-align: center;
        }

        .image-container img {
            width: 90%;
            max-width: 320px;
            height: auto;
            margin-bottom: 20px;
        }

        .image-container h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .image-container p {
            color: #e2d2e8;
            font-size: 13px;
            margin: 0;
        }

        /* Right column (form) -> Equivalent to col-md-6 */
        .form-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            background-color: #FFFFFF;
        }

        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        input {
            background-color: #f4f5f7;
            border: 2px solid transparent;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #8e44ad;
            background-color: #fff;
        }

        /* Responsive handling for smaller screens */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 400px;
            }

            .image-container {
                display: none;
                /* Hides image side on mobile for space optimization */
            }

            .form-container {
                padding: 40px 30px;
            }
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8e44ad;
            font-size: 16px;
        }

        .btn-forgot-link {
            background: none;
            border: none;
            color: #8e44ad;
            font-size: 14px;
            text-decoration: underline;
            cursor: pointer;
            margin: 15px 0;
        }
    </style>
</head>

<body>

    <div class="container" id="container">

        <!-- Left Section: Interactive Image side (col-md-6) -->
        <div class="image-container">
            <!-- Dynamic Vector Image representing an LMS / Admin Dashboard -->
            <img src="{{ asset('images/admin/admin.jpg') }}" alt="Admin Portal Illustration">
            <h2>MyChoiceTutor</h2>
            <p>Management & Analytics Control Panel</p>
        </div>

        <!-- Right Section: Login Form side (col-md-6) -->
        <div class="form-container">
            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 0;">
                    <div id="forgetPasswordAlert" style="display:none;" class="alert"></div>
                    @if (Session::has('login_success'))
                        <div class="alert alert-success py-2 text-center" style="font-size: 14px;">
                            {{ Session::get('login_success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                        <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                            value="0">
                    @endif
                    @if (Session::has('login_fail'))
                        <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                            value="1">
                        <div class="alert alert-danger py-2 text-center"
                            style="font-size: 14px; background-color: #fce4e4; border-color: #f9c7c7; color: #cc2a2a;">
                            {{ Session::get('login_fail') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>

                        </div>
                    @endif
                </div>

                <h1>Admin Login</h1>
                <p>Sign in to manage your system</p>

                <input type="email" id="username" name="username" placeholder="Email Address" required />
                <span class="text-danger">
                    @error('username')
                        {{ $message }}
                    @enderror
                </span>

                <div class="password-wrapper">
                    <input type="password" id="loginpassword" name="loginpassword" placeholder="Password" required />

                    <i class="fa fa-eye-slash toggle-password" id="eyeIcon" onclick="togglePassword()"></i>
                </div>

                <span class="text-danger">
                    @error('loginpassword')
                        {{ $message }}
                    @enderror
                </span>

                {{-- <a href="#">Forgot your password?</a> --}}
                <button type="button" class="btn-forgot-link" data-bs-toggle="modal"
                    data-bs-target="#forgetPasswordPopup">
                    Forgot password?
                </button>
                <button type="submit" class="button">Log In</button>
            </form>
        </div>

    </div>

    <div class="modal fade" id="forgetPasswordPopup" tabindex="-1" aria-labelledby="forgetPasswordTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; padding: 20px; border: none; background: #ffffff;">

                <div class="modal-header" style="border: none; padding: 0; position: relative;">
                    <h3 class="modal-title w-100 text-center" id="forgetPasswordTitle"
                        style="font-weight: 800; font-size: 24px; color: #333; margin-top: 10px;">
                        Forgot Password
                    </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="position: absolute; right: 5px; top: 5px; font-size: 16px; opacity: 0.7; shadow: none; outline: none;">
                    </button>
                </div>

                <form class="loginForm" id="forgetPasswordFormAjax" action="{{ route('admin.forget-password') }}"
                    method="POST"
                    style="padding: 10px 5px; display: flex; flex-direction: column; align-items: stretch; width: 100%;">
                    @csrf

                    <div class="form-group" style="margin-bottom: 0;">
                        <div id="forgetPasswordAlert" style="display:none;" class="alert"></div>
                        @if (Session::has('success'))
                            <div class="alert alert-success py-2 text-center" style="font-size: 14px;">
                                {{ Session::get('success') }}</div>
                            <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                                value="0">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        @endif
                        @if (Session::has('fail'))
                            <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                                value="1">
                            <div class="alert alert-danger py-2 text-center"
                                style="font-size: 14px; background-color: #fce4e4; border-color: #f9c7c7; color: #cc2a2a;">
                                {{ Session::get('fail') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>

                            </div>
                        @endif
                    </div>

                    <div class="form-group text-start" style="margin-bottom: 20px; width: 100%;">
                        <label for="email"
                            style="font-weight: 600; font-size: 14px; margin-bottom: 8px; display: block; color: #444; text-align: left;">
                            Email Address
                        </label>
                        <input type="text" class="form-control" id="email" name="email"
                            placeholder="Your Email" required
                            style="background: #f4f5f7; border: 2px solid transparent; padding: 12px; border-radius: 8px; width: 100%; transition: all 0.3s ease;">

                        <span class="lms-error-msg text-danger mt-1">
                            @error('email')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>

                    <span class="lms-error-msg text-danger">
                        @error('requestAs')
                            {{ $message }}
                        @enderror
                    </span>

                    <div class="text-center w-100 mt-2">
                        <button type="submit" id="forgetPasswordBtnAjax" class="button"
                            style="width: auto; min-width: 180px; padding: 12px 30px;">
                            Send Code
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</body>

</html>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("loginpassword");
        const eyeIcon = document.getElementById("eyeIcon");

        if (passwordInput.type === "password") {

            // Show password
            passwordInput.type = "text";

            // Eye open
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");

        } else {

            // Hide password
            passwordInput.type = "password";

            // Eye slash
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        }
    }
</script>
