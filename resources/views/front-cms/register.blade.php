@extends('front-cms.layouts.main')
@section('main-section')
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container">


            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                    <div class="registop">
                        <h1 class="">
                            Become a tutor <br> share your passion!
                        </h1>
                        <img src="{{url('frontendnew/img/registrationImg.png')}}" width="100%" alt="">

                        <p class="charcol">"Become a tutor with us and share your knowledge to inspire and educate students worldwide. Enjoy the flexibility of setting your own schedule, competitive earnings, and the satisfaction of making a difference in learners' lives." <br><br>Join our community of passionate educators today!</p>

                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                    <div class="regidform">
                        <h3 class="text-center mt-4">To Register with us fill out the following</h3>
                        @if (Session::has('success'))
                                <div class="alert alert-success">{{ Session::get('success') }}</div>
                            @endif
                            @if (Session::has('fail'))
                                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                            @endif
                        <form action="{{url('/student/register')}}" method="POST" class="">
                            @csrf
                                <input type="hidden" name="timezone" id="timezone">

                            <div class="form-group">
                                <label for="name">Full Name:<span class="reqrd">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" aria-describedby=""
                                    placeholder="Your name" value="{{old('name')}}">
                                    <span class="text-danger error-message">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div>


                            <div class="form-group">
                                <label for="email">Email:<span class="reqrd">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby=""
                                    placeholder="Your email address"value="{{old('email')}}">
                                    <span class="text-danger error-message">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div>

                            <div class="form-group">
                                <label for="number">Mobile:<span class="reqrd">*</span></label>

                                <div style="display:flex; gap:10px;">

                                    <!-- Country Code Dropdown -->
                                    <select class="form-control" id="country_code" name="country_code" style="max-width:120px;">
                                        @foreach(config('phone') as $key => $country)
                                            <option value="{{ $country['code'] }}"
                                                {{ old('country_code', '+44') == $country['code'] ? 'selected' : '' }}>
                                                {{ $country['code'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Mobile Input -->
                                    <input type="text"
                                        class="form-control"
                                        id="mobile"
                                        name="mobile"
                                        placeholder="Mobile (no country code)"
                                        value="{{ old('mobile') }}"
                                        oninput="formatMobile(this)">
                                </div>

                                <span class="text-danger error-message">
                                    @error('mobile')
                                        {{ $message }}
                                    @enderror
                                </span>

                            </div>

                            {{-- <div class="form-group">
                                <label for="password">Create password:<span class="reqrd">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" aria-describedby=""
                                    placeholder="&#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226;">
                                    <span class="text-danger error-message">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div>

                            <div class="form-group">
                                <label for="password">Retype password:<span class="reqrd">*</span></label>
                                <input type="password" class="form-control" id="confpassword" name="confpassword" aria-describedby=""
                                    placeholder="&#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226;">
                                    <span class="text-danger error-message">
                                        @error('confpassword')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div> --}}


                            <div class="form-group position-relative">
                                    <label for="password">Create password:<span class="reqrd">*</span></label>

                                    <input type="password" class="form-control pr-5" id="password" name="password"
                                        placeholder="••••••••">

                                    <span class="toggle-password" toggle="#password" style="position:absolute; right:15px; top:40px; cursor:pointer;">
                                        👁️
                                    </span>

                                    <span class="text-danger error-message">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </span>
                            </div>

                            <div class="form-group position-relative">
                                <label for="confpassword">Retype password:<span class="reqrd">*</span></label>

                                <input type="password" class="form-control pr-5" id="confpassword" name="confpassword"
                                    placeholder="••••••••">

                                <span class="toggle-password" toggle="#confpassword" style="position:absolute; right:15px; top:40px; cursor:pointer;">
                                    👁️
                                </span>

                                <span class="text-danger error-message">
                                    @error('confpassword')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>


                            <p>Register as<span class="reqrd">*</span></p>
                            <div class="radioBtn">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="radioLogin student active-btn">
                                            <input type="radio" class=""    value="student" name="registerAs"
                                                id="student" checked> <span>Student</span>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="radioLogin tutor">
                                            <input type="radio" value="tutor" name="registerAs" id="tutor">
                                            <span>Tutor</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="term-Condition">
                                <input type="checkbox" id="expcheck" name="expcheck" {{ old('expcheck') ? 'checked' : '' }}> <span for="termCondition">I have read and agree to all <a
                                        href="{{route('termsandconditions')}}">Terms & conditions</a></span>
                            </div>
                            <span class="text-danger error-message">
                                @error('expcheck')
                                    {{ $message }}
                                @enderror
                            </span>


                            <div class="row mt-4">
                                <div class="col-12 ">
                                    <div class="regSub">
                                        <button type="submit" class="btn btn-lg">Create</button>
                                    </div>
                                </div>
                            </div>


                            {{-- <div class="row mt-4">
                                <div class="col-12 ">
                                    <div class="fb-btn">
                                        <img src="{{url('frontendnew/img/icons/facebook-icon.png')}}" alt="">
                                        <span>Sign up with Facebook</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12 ">
                                    <div class="google-btn">
                                        <img src="{{url('frontendnew/img/icons/google-logo.png')}}" alt="">
                                        <span>Sign up with Google</span>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="haveAnAccount">
                                <p>Already have an account <a href="#" data-toggle="modal" data-target="#loginPopup"><u>Login now</u></a></p>
                            </div>


                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const studentRadio = document.getElementById('student');
            const tutorRadio = document.getElementById('tutor');
            const studentDiv = document.querySelector('.student');
            const tutorDiv = document.querySelector('.tutor');

            function switchActiveClass() {
                if (studentRadio.checked) {
                    studentDiv.classList.add('active-btn');
                    tutorDiv.classList.remove('active-btn');
                } else if (tutorRadio.checked) {
                    tutorDiv.classList.add('active-btn');
                    studentDiv.classList.remove('active-btn');
                }
            }

            studentRadio.addEventListener('change', switchActiveClass);
            tutorRadio.addEventListener('change', switchActiveClass);
        });
    </script>
    <script>
            function formatMobile(input)
            {
                // Allow digits only
                let number = input.value.replace(/\D/g, '');

                // limit length (max 15 digits international standard)
                if (number.length > 15) {
                    number = number.slice(0, 15);
                }

                input.value = number;
            }
    </script>

    {{-- Pick the timezone of the user and log  --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            
            console.log("User timezone:", timezone);

            document.getElementById("timezone").value = timezone;
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            document.querySelectorAll(".toggle-password").forEach(function (element) {
                element.addEventListener("click", function () {

                    let input = document.querySelector(this.getAttribute("toggle"));

                    if (input.type === "password") {
                        input.type = "text";
                        this.innerHTML = "🙈"; // hide icon
                    } else {
                        input.type = "password";
                        this.innerHTML = "👁️"; // show icon
                    }
                });
            });

        });
</script>


@endsection
