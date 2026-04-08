<style>
    .active-btn {
        background-color: #000;
        accent-color: #fff;
    }

    .active-btn span {
        color: #fff;
    }

    .radioLogin {
        display: flex;
        border-radius: 8px;
        padding: 10px;
        accent-color: #000;
        gap: 5px;
    }

    .toggle-password {
        position: absolute;
        top: 42px;
        right: 12px;
        cursor: pointer;
        color: #666;
        font-size: 16px;
    }

    .toggle-password:hover {
        color: #000;
    }

    .contactDetail {
        padding: 0;
        margin-left: 0;
}

    .contactDetail li {
        display: flex;
        align-items: center;   /* vertical alignment */
        gap: 10px;             /* space between icon and text */
        margin-bottom: 10px;
        list-style: none;
    }

    .contactDetail li img {
        width: 18px;
        height: 18px;
        object-fit: contain;
    }
</style>

<!-- Login Modal -->
<div class="modal fade loginModel" id="loginPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content loginModel">
            <div class="modal-header" style="border: none;">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h3 class="text-center">Login</h3>

            <form class="loginForm" id="loginFormAjax" action="{{ url('/student-login') }}" method="GET">
                @csrf
                <div class="form-group">
                    <div id="loginAlert" style="display:none;" class="alert"></div>
                    @if (Session::has('success'))
                        <div class="alert alert-success">{{ Session::get('success') }}</div>
                        <input type="hidden" id="showloginpopup" name="showloginpopup" value="0">
                    @endif
                    @if (Session::has('fail'))
                        <input type="hidden" id="showloginpopup" name="showloginpopup" value="1">
                        <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                    @endif
                   <label for="number">Mobile Number</label>
                    <div style="display:flex; gap:10px; margin-bottom: 15px;">
                        <select class="form-control" id="country_code" name="country_code" style="max-width:100px;">
                            @foreach(config('phone') as $country)
                                <option value="{{ $country['code'] }}" 
                                    {{ old('country_code', '+44') == $country['code'] ? 'selected' : '' }}>
                                    {{ $country['code'] }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" class="form-control" id="mobile" name="mobile" 
                            placeholder="Your Number" required value="{{ old('mobile') }}" oninput="this.value=this.value.slice(0,20)">
                    </div>
                </div>
                <span class="text-danger  login-errorMessage" id="mobileError">
                    @error('mobile')
                        {{ $message }}
                    @enderror
                </span>
                <div class="form-group position-relative">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" aria-describedby=""
                        placeholder="Password" required>
                    <i class="fa fa-eye-slash toggle-password mt-2" data-target="password"></i>

                </div>
                <span class="text-danger login-errorMessage" id="passwordError">
                    @error('password')
                        {{ $message }}
                    @enderror
                </span>
                <p class="mt-3">Login as</p>

                <div class="radioBtn">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin studentPopup  active-btn">
                                <input type="radio" value="student" name="loginAs" id="studentPopup" checked>
                                <span>Student</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin tutorPopup">
                                <input type="radio" value="tutor" name="loginAs" id="tutorPopup">
                                <span>Tutor</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin parentsPopup">
                                <input type="radio" value="parent" name="loginAs" id="parentsPopup">
                                <span>Parents</span>
                            </div>
                        </div>
                    </div>

                    <span class="text-danger login-errorMessage" id="loginAsError">
                        @error('loginAs')
                            {{ $message }}
                        @enderror
                    </span>
                </div>


                <hr>
                <button type="submit" id="loginBtnAjax" class="btn brand-bg-Color popuplogin mb-3">Login</button>

                <br>
                {{-- <a href="#">
                      <div class="googleLogin">
  
                          <img src="{{ url('frontendnew/img/icons/google-logo.png') }}" alt=""><span>Sign in with
                              Google</span>
  
                      </div>
  
                  </a> --}}

                <div class="forgotPwd mt-3">
                    <p> Don't have an account? <a href="{{ '/student/register' }}" class="register">Register</a></p>
                    <button type="button" class="btn btn-sm" data-toggle="modal" data-target="#forgetPasswordPopup">Forgot
                        password?</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Forget Password Modal -->
<div class="modal fade loginModel" id="forgetPasswordPopup" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content loginModel">
            <div class="modal-header" style="border: none;">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h3 class="text-center">Forget Password</h3>

            <form class="loginForm" id="forgetPasswordFormAjax" action="{{ url('/forget-password') }}" method="POST">
                @csrf
                <div class="form-group">
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
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" aria-describedby=""
                        placeholder="Your Email" required>
                </div>
                <span class="text-danger login-errorMessage">
                    @error('email')
                        {{ $message }}
                    @enderror
                </span>
                <p class="mt-3">Change Password for</p>
                <div class="radioBtn">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin studentPopup  active-btn">
                                <input type="radio" value="student" name="requestAs" id="studentPopup" checked>
                                <span>Student</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin tutorPopup">
                                <input type="radio" value="tutor" name="requestAs" id="tutorPopup">
                                <span>Tutor</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin parentsPopup">
                                <input type="radio" value="parent" name="requestAs" id="parentsPopup">
                                <span>Parents</span>
                            </div>
                        </div>
                    </div>

                    <span class="text-danger login-errorMessage">
                        @error('requestAs')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <hr>
                <button type="submit" id="forgetPasswordBtnAjax" class="btn brand-bg-Color popuplogin mb-3 ml-auto">Send Code</button>

                <br>
                {{-- <a href="#">
                      <div class="googleLogin">
  
                          <img src="{{ url('frontendnew/img/icons/google-logo.png') }}" alt=""><span>Sign in with
                              Google</span>
  
                      </div>
  
                  </a> --}}
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const studentRadio = document.getElementById('student');
        const tutorRadio = document.getElementById('tutor');
        const parentsRadio = document.getElementById('parents');
        const studentDiv = document.querySelector('.student');
        const tutorDiv = document.querySelector('.tutor');
        const parentsDiv = document.querySelector('.parents');



        function switchActiveClass() {
            studentDiv.classList.remove('active-btn');
            tutorDiv.classList.remove('active-btn');
            parentsDiv.classList.remove('active-btn');


            if (studentRadio.checked) {
                studentDiv.classList.add('active-btn');
            } else if (tutorRadio.checked) {
                tutorDiv.classList.add('active-btn');
            } else if (parentsRadio.checked) {
                parentsDiv.classList.add('active-btn');
            }

        }

        studentRadio.addEventListener('change', switchActiveClass);
        tutorRadio.addEventListener('change', switchActiveClass);
        parentsRadio.addEventListener('change', switchActiveClass);


    });

    document.addEventListener('DOMContentLoaded', () => {

        const studentRadioPopup = document.getElementById('studentPopup');
        const tutorRadioPopup = document.getElementById('tutorPopup');
        const parentsRadioPopup = document.getElementById('parentsPopup');
        const studentDivPopup = document.querySelector('.studentPopup');
        const tutorDivPopup = document.querySelector('.tutorPopup');
        const parentsDivPopup = document.querySelector('.parentsPopup');


        function switchActiveClassNew() {
            studentDivPopup.classList.remove('active-btn');
            tutorDivPopup.classList.remove('active-btn');
            parentsDivPopup.classList.remove('active-btn');

            if (studentRadioPopup.checked) {
                studentDivPopup.classList.add('active-btn');
            } else if (tutorRadioPopup.checked) {
                tutorDivPopup.classList.add('active-btn');
            } else if (parentsRadioPopup.checked) {
                parentsDivPopup.classList.add('active-btn');
            }

        }
        studentRadioPopup.addEventListener('change', switchActiveClassNew);
        tutorRadioPopup.addEventListener('change', switchActiveClassNew);
        parentsRadioPopup.addEventListener('change', switchActiveClassNew);

    });
</script>

<script>
    $('#myModal').on('shown.bs.modal', function() {
        $('#myInput').trigger('focus')
    })
</script>
<script>
    $(document).ready(function() {
        // When Forget Password modal opens, hide Login modal
        $('#forgetPasswordPopup').on('show.bs.modal', function() {
            $('#loginPopup').modal('hide');
        });

        // Optional: When Login modal opens, hide Forget Password modal
        $('#loginPopup').on('show.bs.modal', function() {
            $('#forgetPasswordPopup').modal('hide');
        });

        // Re-open modals on validation or session errors
        @if(Session::has('fail'))
            var failMsg = @json(Session::get('fail'));
            if (failMsg === 'Email not found!' || failMsg === 'No User Found!') {
                $('#forgetPasswordPopup').modal('show');
            } else if (failMsg === 'Password does not match' || failMsg === 'Mobile No. Not Registered') {
                $('#loginPopup').modal('show');
            }
        @endif

        @if(Session::has('success'))
            var successMsg = @json(Session::get('success'));
            if (successMsg === 'Token send successfully!') {
                $('#forgetPasswordPopup').modal('show');
            }
        @endif

        @if($errors->has('email') || $errors->has('requestAs'))
            $('#forgetPasswordPopup').modal('show');
        @endif

        @if($errors->has('mobile') || $errors->has('password') || $errors->has('loginAs'))
            $('#loginPopup').modal('show');
        @endif
    });
</script>

<script>
    $(document).ready(function() {
        $('#loginFormAjax').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $btn = $('#loginBtnAjax');
            var $alert = $('#loginAlert');
            
            // Clear previous errors
            $('#loginFormAjax .login-errorMessage').text('');
            $alert.hide().removeClass('alert-success alert-danger').text('');
            $btn.prop('disabled', true).text('Logging in...');
            
            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method') || 'GET',
                data: $form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $alert.addClass('alert-success').text('Login successful! Redirecting...').show();
                        window.location.href = response.redirectUrl;
                    } else if (response.status === 'error') {
                        $alert.addClass('alert-danger').text(response.message).show();
                        $btn.prop('disabled', false).text('Login');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.mobile) $('#mobileError').text(errors.mobile[0]);
                        if (errors.password) $('#passwordError').text(errors.password[0]);
                        if (errors.loginAs) $('#loginAsError').text(errors.loginAs[0]);
                    } else {
                        $alert.addClass('alert-danger').text('An error occurred. Please try again.').show();
                    }
                    $btn.prop('disabled', false).text('Login');
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#forgetPasswordFormAjax').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $btn = $('#forgetPasswordBtnAjax');
            var $alert = $('#forgetPasswordAlert');
            
            $btn.prop('disabled', true).text('Sending...');
            $alert.hide().removeClass('alert-success alert-danger').text('');
            
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        $alert.addClass('alert-success').text(response.message).show();
                        $form[0].reset();
                    } else {
                        $alert.addClass('alert-danger').text(response.message).show();
                    }
                },
                error: function(xhr) {
                    $alert.addClass('alert-danger').text('An error occurred. Please try again.').show();
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Send Code');
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleIcons = document.querySelectorAll('.toggle-password');

        toggleIcons.forEach(function (icon) {
            icon.addEventListener('click', function () {
                const input = document.getElementById(icon.dataset.target);

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        });
    });
</script>




<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<a href="https://api.whatsapp.com/send?phone=+447761975326&text=Hello." class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
</a>

{{-- <div class="chatboat">
      <img src="{{ url('frontendnew/img/icons/chatboat.png') }}" alt="">
  </div>  --}}

<footer class="footerArea mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <h5 class="mb-4">Quick Links</h5>
                <ul>
                    <a href="/aboutus">
                        <li>About us</li>
                    </a>
                    <a href="/why-choose-us">
                        <li>Why choose us</li>
                    </a>
                    <a href="/findatutor">
                        <li>Find Tutor</li>
                    </a>
                    <a href="/subjects">
                        <li>Subjects</li>
                    </a>
                    <a href="/contact">
                        <li>Contact Us</li>
                    </a>
                    <a href="/privacypolicy">
                        <li>Privacy Policy</li>
                    </a>
                    <a href="/termsandconditions">
                        <li>Terms and Conditions</li>
                    </a>
                    <a href="/refundpolicy">
                        <li>Refund Policy</li>
                    </a>

                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <h5 class="mb-4">Popular Subjects</h5>

                @php
                    $footerSubjects = App\Helpers\CommonHelper::getPopularSubjects(12);
                @endphp

                @if($footerSubjects->isNotEmpty())
                <ul>
                    @foreach ($footerSubjects as $footerSubject)
                        <form action="{{ url('toptutorsearch') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subject" value="{{ $footerSubject->id }}">
                            <button type="submit" style="background:none;border:none;padding:0;">
                                <li>{{ \Illuminate\Support\Str::limit($footerSubject->name, 15, '...') }}</li>
                            </button>
                        </form>
                    @endforeach
                </ul>
                @endif
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <h5 class="mb-4">Follow us</h5>
                <ul class="contactDetail" style="margin-left:2px">
                    <li><img src="{{ url('frontendnew/img/icons/Group.png') }}" alt="">07761 975326</li>
                    <li><img src="{{ url('frontendnew/img/icons/Vector.png') }}" alt="">07761 975326</li>
                    <li><img src="{{ url('frontendnew/img/icons/email.png') }}"
                            alt="">mychoicetutor@gmail.com
                    </li>

                </ul>

                <div class="social mb-3">
                    <a href="https://www.facebook.com/share/1BtDAN2Fmy/" target="_blank">
                        {{-- <img src="{{ url('frontendnew/img/icons/facebook.png') }}" alt="Facebook"> --}}
                         <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                <path fill="#1877F2" 
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="https://youtube.com/@choicetutoracademy?si=s1Vn-NvQmCGENz4-" target="_blank">
                        {{-- <img src="{{ url('frontendnew/img/icons/OUTLINE_copy_2.png') }}" alt="Youtube"> --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                <path fill="#FF0000" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/my_choice_tutor?igsh=MWI0enFqYjdhb2NwdA==" target="_blank">
                           <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                <defs>
                                    <radialGradient id="rg" r="150%" cx="30%" cy="107%">
                                        <stop stop-color="#fdf497" offset="0%" />
                                        <stop stop-color="#fdf497" offset="5%" />
                                        <stop stop-color="#fd5949" offset="45%" />
                                        <stop stop-color="#d6249f" offset="60%" />
                                        <stop stop-color="#285AEB" offset="90%" />
                                    </radialGradient>
                                </defs>
                                <path fill="url(#rg)" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                    </a>
                    <a href="https://www.tiktok.com/@my.choice.tutor?_t=ZS-8z2WgZKxz6O&_r=1" target="_blank">
                        <img src="{{ url('frontendnew/img/icons/tiktok.png') }}" alt="Tik Tok" height="33"
                            width="33">
                    </a>
                </div>
    
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">

                <div class="social my-5">
                    <a href="{{ route('home') }}">
                        <img src="{{ url('frontendnew/img/footer-logo.png') }}" width="160px" alt="Home">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright © 2026 MyChoiceTutor. All rights reserved. &nbsp; | &nbsp; Proudly powered by <a
                href="https://thenexteck.com/" target="_blank" style="color: white">Nexteck</a></p>
    </div>
</footer>

<script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ url('frontendnew/js/popper.min.js') }}"></script>
<script src="{{ url('frontendnew/js/bootstrap.min.js') }}"></script>
<script src="{{ url('frontendnew/js/jquery.sticky.js') }}"></script>
<script src="{{ url('frontendnew/js/main.js') }}"></script>
</body>

</html>
