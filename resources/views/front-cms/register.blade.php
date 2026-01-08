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
                               <input type="text" class="form-control" id="mobile" name="mobile"
       placeholder="Your mobile number"
       value="{{ old('mobile') }}" maxlength="11"
       oninput="formatMobile(this)">

                                <span class="text-danger error-message" id="mobile-error">
                                    @error('mobile')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="form-group">
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
function formatMobile(input) {
    let number = input.value.replace(/\D/g, ''); // remove all non-digits

    // Replace 92 only if it's at the very beginning
    if (number.startsWith('92')) {
        number = '0' + number.slice(2);
    }

    // Limit to max 11 digits
    if (number.length > 11) {
        number = number.slice(0, 11);
    }

    input.value = number; // update field value
}
</script>

@endsection
