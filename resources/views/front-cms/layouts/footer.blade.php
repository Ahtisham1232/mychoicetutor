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

            <form class="loginForm" action="{{ url('/student-login') }}" method="GET">
                @csrf
                <div class="form-group">
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
                                    {{ old('country_code', '+92') == $country['code'] ? 'selected' : '' }}>
                                    {{ $country['code'] }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" class="form-control" id="mobile" name="mobile" 
                            placeholder="Your Number" required value="{{ old('mobile') }}" oninput="this.value=this.value.slice(0,20)">
                    </div>
                </div>
                <span class="text-danger  login-errorMessage">
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
                <span class="text-danger login-errorMessage">
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

                    <span class="text-danger login-errorMessage">
                        @error('loginAs')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <hr>
                <button type="submit" class="btn brand-bg-Color popuplogin mb-3">Login</button>

                <br>
                {{-- <a href="#">
                      <div class="googleLogin">
  
                          <img src="{{ url('frontendnew/img/icons/google-logo.png') }}" alt=""><span>Sign in with
                              Google</span>
  
                      </div>
  
                  </a> --}}

                <div class="forgotPwd mt-3">
                    <p> Don't have an account? <a href="{{ '/student/register' }}" class="register">Register</a></p>
                    <button class="btn btn-sm" data-toggle="modal" data-target="#forgetPasswordPopup">Forgot
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

            <form class="loginForm" action="{{ url('/forget-password') }}" method="POST">
                @csrf
                <div class="form-group">
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
                    @error('password')
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
                        @error('loginAs')
                            {{ $message }}
                        @enderror
                    </span>
                </div>

                <hr>
                <button type="submit" class="btn brand-bg-Color popuplogin mb-3 ml-auto">Send Code</button>

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

                <ul>
                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="1">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Maths</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="2">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>English</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="3">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Chemistry</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="4">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Physics</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="5">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Biology</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="6">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Science</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="7">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Spanish</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="8">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>French</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="9">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>German</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="10">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>History</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="11">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Music</li>
                        </button>
                    </form>

                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <input type="hidden" id="subject" name="subject" value="12">
                        <button type="submit" style="background:none;border:none;padding:0;">

                            <li>Psychology</li>
                        </button>
                    </form>
                </ul>
            </div>


            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <h5 class="mb-4">Follow us</h5>
                <ul class="contactDetail">
                    <li><img src="{{ url('frontendnew/img/icons/Group.png') }}" alt="">07761 975326</li>
                    <li><img src="{{ url('frontendnew/img/icons/Vector.png') }}" alt="">07761 975326</li>
                    <li><img src="{{ url('frontendnew/img/icons/email.png') }}"
                            alt="">mychoicetutor@gmail.com
                    </li>

                </ul>

                <div class="social">
                    <a href="https://www.facebook.com/share/1BtDAN2Fmy/" target="_blank">
                        <img src="{{ url('frontendnew/img/icons/facebook.png') }}" alt="Facebook">
                    </a>
                    <a href="https://youtube.com/@choicetutoracademy?si=s1Vn-NvQmCGENz4-" target="_blank">
                        <img src="{{ url('frontendnew/img/icons/OUTLINE_copy_2.png') }}" alt="Youtube">
                    </a>
                    <a href="https://www.instagram.com/my_choice_tutor?igsh=MWI0enFqYjdhb2NwdA==" target="_blank">
                        <img src="{{ url('frontendnew/img/icons/Group 797.png') }}" alt="Instagram">
                    </a>
                    <a href="https://www.tiktok.com/@my.choice.tutor?_t=ZS-8z2WgZKxz6O&_r=1" target="_blank">
                        <img src="{{ url('frontendnew/img/icons/tiktok.png') }}" alt="Tik Tok" height="33"
                            width="33">
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                {{-- <h5 class="mt-4">Help</h5>
                  <ul>
                      <li>Help Center</li>
                      <li>Contact Us</li>
                  </ul> --}}

                <div class="social my-5">
                    <img src="{{ url('frontendnew/img/footer-logo.png') }}" width="160px" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Copyright © 2024 MyChoiceTutor. All rights reserved. &nbsp; | &nbsp; Proudly powered by <a
                href="https://thenexteck.com/" target="_blank" style="color: white">Nexteck</p>
    </div>
</footer>

<script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ url('frontendnew/js/popper.min.js') }}"></script>
<script src="{{ url('frontendnew/js/bootstrap.min.js') }}"></script>
<script src="{{ url('frontendnew/js/jquery.sticky.js') }}"></script>
<script src="{{ url('frontendnew/js/main.js') }}"></script>
</body>

</html>
