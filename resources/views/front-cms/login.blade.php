@extends('front-cms.layouts.main')
@section('main-section')
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,600,800');

        .lms-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 75vh;
            margin-top: 120px;
            padding: 40px 20px 60px;
            background: #fcfaff;
        }

        .lms-container {
            display: flex;
            width: 960px;
            max-width: 100%;
            min-height: 580px;
            overflow: hidden;
            border-radius: 16px;
            background: #fff;
            border: 1px solid rgba(142, 68, 173, 0.1);
            box-shadow: 0 15px 35px rgba(106, 48, 125, 0.1),
                0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .lms-image-side,
        .lms-form-side {
            flex: 1;
        }

        .lms-image-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
            color: #fff;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
        }

        .lms-image-side img {
            width: 85%;
            max-width: 280px;
            margin-bottom: 25px;
            border-radius: 8px;
        }

        .lms-image-side h2 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .lms-image-side p {
            font-size: 14px;
            color: #e2d2e8;
            line-height: 1.5;
        }

        .lms-form-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px 45px;
            background: #fff;
        }

        .lms-form-side h3 {
            font-size: 32px;
            font-weight: 800;
            color: #333;
            margin-bottom: 5px;
        }

        .lms-wrapper .subtitle {
            font-size: 14px;
            color: #777;
            margin-bottom: 30px;
        }

        .lms-input-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        .lms-wrapper .form-control-lms,
        #forgetPasswordPopup #email {
            width: 100%;
            height: 48px;
            padding: 8px 12px;
            font-size: 14px;
            color: #333;
            border: 2px solid transparent;
            border-radius: 8px;
            background: #f4f5f7;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .lms-wrapper .form-control-lms:focus,
        #forgetPasswordPopup #email:focus {
            outline: none;
            border-color: #8e44ad;
            background: #fff;
            box-shadow: 0 4px 12px rgba(142, 68, 173, 0.08);
        }

        .lms-phone-row,
        .lms-radio-row {
            display: flex;
            gap: 12px;
            width: 100%;
        }

        .lms-phone-row {
            margin-bottom: 4px;
        }

        .lms-radio-row {
            margin-bottom: 5px;
        }

        .lms-form-side select.form-control-lms {
            width: 75px;
            flex-shrink: 0;
        }

        .lms-radio-col {
            flex: 1;
        }

        .lms-wrapper .radioLogin,
        #forgetPasswordPopup .radioLogin {
            width: 100%;
            padding: 12px 5px;
            text-align: center;
            border: 2px solid #eef0f4;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            transition: 0.25s;
        }

        .lms-wrapper .radioLogin input,
        #forgetPasswordPopup .radioLogin input {
            display: none;
        }

        .lms-wrapper .radioLogin span,
        #forgetPasswordPopup .radioLogin span {
            font-size: 14px;
            font-weight: 600;
            color: #666;
        }

        .lms-wrapper .radioLogin.active-btn,
        #forgetPasswordPopup .radioLogin.active-btn {
            border-color: #8e44ad;
            background: #fbf6ff;
        }

        .lms-wrapper .radioLogin.active-btn span,
        #forgetPasswordPopup .radioLogin.active-btn span {
            color: #8e44ad;
        }

        .lms-password-wrapper {
            position: relative;
            width: 100%;

        }

        .lms-wrapper .toggle-password {
            position: absolute;
            right: 16px;
            top: 16px;
            cursor: pointer;
            color: #777;
        }

        .lms-password-wrapper .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
            font-size: 16px;
        }

        .btn-lms-submit {
            width: 100%;
            margin-top: 15px;
            padding: 14px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(135deg, #6a307d, #8e44ad);
            box-shadow: 0 5px 15px rgba(142, 68, 173, 0.3);
            transition: 0.3s;
        }

        .btn-lms-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 20px rgba(142, 68, 173, 0.45);
        }

        .lms-footer-links {
            margin-top: 25px;
            text-align: center;
        }

        .lms-footer-links p {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .lms-wrapper .register,
        .lms-wrapper .btn-forgot-link {
            color: #8e44ad;
            font-weight: 600;
        }

        .lms-wrapper .btn-forgot-link {
            background: none;
            border: none;
            padding: 0;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }

        .lms-wrapper .lms-error-msg,
        #forgetPasswordPopup .lms-error-msg {
            display: block;
            margin: 4px 0 15px;
            font-size: 12px;
            color: #e74c3c;
        }

        /* Keep shared footer layout consistent (Popular Subjects uses form/button) */
        .footerArea ul form {
            margin: 0;
            padding: 0;
        }

        .footerArea ul form button {
            margin: 0;
            padding: 0;
            border: none;
            background: none;
            box-shadow: none;
            text-transform: none;
            letter-spacing: normal;
            font-weight: inherit;
            line-height: inherit;
        }

        @media (max-width: 820px) {
            .lms-container {
                flex-direction: column;
                max-width: 460px;
                min-height: auto;
            }

            .lms-image-side {
                padding: 35px 20px;
            }

            .lms-image-side img {
                max-width: 160px;
                margin-bottom: 15px;
            }

            .lms-form-side {
                padding: 40px 25px;
            }
        }

        @media (max-width: 425px) {

            .lms-wrapper {
                padding: 20px 12px 40px;
                margin-top: 90px;
            }

            .lms-form-side {
                padding: 30px 18px;
            }

            /* Mobile number field fix */
            .lms-phone-row {
                gap: 8px;
            }

            .lms-form-side select.form-control-lms {
                width: 65px;
                min-width: 65px;
            }

            .lms-phone-row input.form-control-lms {
                flex: 1;
                min-width: 0;
            }

            /* Login buttons fix */
            .lms-radio-row {
                gap: 8px;
            }

            .lms-radio-col {
                flex: 1;
            }

            .lms-wrapper .radioLogin {
                padding: 10px 4px;
            }

            .lms-wrapper .radioLogin span {
                font-size: 13px;
            }

            /* Button spacing */
            .btn-lms-submit {
                padding: 12px;
                font-size: 13px;
            }

            .lms-footer-links p,
            .lms-wrapper .btn-forgot-link {
                font-size: 13px;
            }
        }
    </style>

    <div class="lms-wrapper">
        <div class="lms-container">

            <div class="lms-image-side">
                <img src="{{ asset('images/admin/admin.jpg') }}" alt="Student Portal Illustration">
                <h2>Welcome Back!</h2>
                <p>Access your personalized lessons and track your growth path.</p>
            </div>

            <div class="lms-form-side">
                <h3>Login</h3>
                <p class="subtitle">Sign in to manage your system</p>

                <div class="lms-form-container-box">
                    <form class="loginForm" action="{{ route('userLogin') }}" method="POST">
                        @csrf
                        <input type="hidden" name="timezone" id="timezone">

                        <span class="lms-input-label">Mobile Number</span>
                        <div class="lms-phone-row">
                            <select class="form-control-lms" id="country_code" name="country_code">
                                @foreach (config('phone') as $country)
                                    <option value="{{ $country['code'] }}"
                                        {{ old('country_code', '+44') == $country['code'] ? 'selected' : '' }}>
                                        {{ $country['code'] }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="text" class="form-control-lms" id="mobile" name="mobile"
                                placeholder="Your Number" required value="{{ old('mobile') }}"
                                oninput="this.value=this.value.slice(0,20)">
                        </div>
                        <span class="lms-error-msg">
                            @error('mobile')
                                {{ $message }}
                            @enderror
                        </span>

                        <span class="lms-input-label">Password</span>
                        <div class="lms-password-wrapper">
                            <input type="password" class="form-control-lms" id="password" name="password"
                                placeholder="Password" required maxlength="30">
                            <i class="fa fa-eye-slash toggle-password" data-target="password"></i>
                        </div>
                        <span class="lms-error-msg">
                            @error('password')
                                {{ $message }}
                            @enderror
                        </span>

                        <span class="lms-input-label" style="margin-bottom: 12px !important;">Login as</span>
                        <div class="lms-radio-row">
                            <div class="lms-radio-col">
                                <div class="radioLogin student active-btn">
                                    <input type="radio" value="student" name="loginAs" id="student" checked>
                                    <span>Student</span>
                                </div>
                            </div>
                            <div class="lms-radio-col">
                                <div class="radioLogin tutor">
                                    <input type="radio" value="tutor" name="loginAs" id="tutor">
                                    <span>Tutor</span>
                                </div>
                            </div>
                            <div class="lms-radio-col">
                                <div class="radioLogin parents">
                                    <input type="radio" value="parent" name="loginAs" id="parents">
                                    <span>Parents</span>
                                </div>
                            </div>
                        </div>
                        <span class="lms-error-msg">
                            @error('loginAs')
                                {{ $message }}
                            @enderror
                        </span>

                        <button type="submit" class="btn-lms-submit">Login</button>

                        <div class="lms-footer-links">
                            <p>Don't have an account? <a href="{{ route('std_tutor_registration') }}"
                                    class="register">Register</a></p>
                            <button type="button" class="btn-forgot-link" data-toggle="modal"
                                data-target="#forgetPasswordPopup">
                                Forgot password?
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="forgetPasswordPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; padding: 15px; border:none;">
                <div class="modal-header" style="border: none; padding-bottom: 0;">
                    <h3 class="modal-title w-100 text-center"
                        style="font-weight:800; font-size:24px; color:#333; margin-top:10px;">Forget Password</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="position: absolute; right: 20px; top: 20px; background: none; border: none; font-size: 24px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form class="loginForm" id="forgetPasswordFormAjax" action="{{ url('/forget-password') }}" method="POST"
                    style="padding: 10px 20px;">
                    @csrf
                    <div class="form-group" style="margin-bottom: 15px;">
                        <div id="forgetPasswordAlert" style="display:none;" class="alert"></div>
                        @if (Session::has('success'))
                            <div class="alert alert-success">{{ Session::get('success') }}</div>
                            <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                                value="0">
                        @endif
                        @if (Session::has('fail'))
                            <input type="hidden" id="showForgetPasswordPopup" name="showForgetPasswordPopup"
                                value="1">
                            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                        @endif
                        <label for="email"
                            style="font-weight:600; font-size:14px; margin-bottom:8px; display:block; color:#444;">Email
                            Address</label>
                        <input type="text" class="form-control" id="email" name="email"
                            placeholder="Your Email" required
                            style="background: #f4f5f7; border:2px solid transparent; padding:12px; border-radius:8px; width:100%; box-sizing:border-box;">
                    </div>
                    <span class="lms-error-msg">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </span>

                    <p class="mt-3 mb-2" style="font-weight:600; font-size:14px; color:#444;">Change Password for</p>
                    <div class="lms-radio-row" style="margin-bottom: 20px !important;">
                        <div class="lms-radio-col">
                            <div class="radioLogin studentPopup active-btn">
                                <input type="radio" value="student" name="requestAs" id="studentPopup" checked>
                                <span>Student</span>
                            </div>
                        </div>
                        <div class="lms-radio-col">
                            <div class="radioLogin tutorPopup">
                                <input type="radio" value="tutor" name="requestAs" id="tutorPopup">
                                <span>Tutor</span>
                            </div>
                        </div>
                        <div class="lms-radio-col">
                            <div class="radioLogin parentsPopup">
                                <input type="radio" value="parent" name="requestAs" id="parentsPopup">
                                <span>Parents</span>
                            </div>
                        </div>
                    </div>
                    <span class="lms-error-msg">
                        @error('requestAs')
                            {{ $message }}
                        @enderror
                    </span>

                    <button type="submit" id="forgetPasswordBtnAjax" class="btn-lms-submit"
                        style="margin-top:0px !important;">Send Code</button>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('js/timezone/timezone.js') }}"></script>
<script>
    $(document).ready(function() {
        // Clear eye visibility click logic
        $(document).on('click', '.toggle-password', function() {
            let inputId = $(this).attr('data-target');
            let input = $('#' + inputId);

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Contextual choice logic toggle
        $(document).on('click', '.radioLogin', function() {
            let parentContainer = $(this).closest('.lms-radio-row');
            parentContainer.find('.radioLogin').removeClass('active-btn');

            $(this).addClass('active-btn');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    });
</script>
