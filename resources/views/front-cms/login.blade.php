@extends('front-cms.layouts.main')
@section('main-section')
    <!-- Modal -->
    <div class="modal-dialog modal-dialog-centered" style="margin-top: 150px" role="document">
        <div class="modal-content loginModel">
            <div class="modal-header" style="border: none;">

            </div>
            <h3 class="text-center">Login</h3>
            <form class="loginForm" action="{{ route('userLogin') }}" method="POST">
                @csrf
                {{-- <div class="form-group">
                    <label for="number">Mobile Number</label>
                    <input type="number" class="form-control" id="username" name="username" aria-describedby=""
                        placeholder="Your Number">
                </div> --}}

                <div style="display:flex; gap:10px; margin-bottom: 15px;">
                    <select class="form-control" id="country_code" name="country_code" style="max-width:100px;">
                        @foreach (config('phone') as $country)
                            <option value="{{ $country['code'] }}"
                                {{ old('country_code', '+44') == $country['code'] ? 'selected' : '' }}>
                                {{ $country['code'] }}
                            </option>
                        @endforeach
                    </select>

                    <input type="number" class="form-control" id="mobile" name="mobile" placeholder="Your Number"
                        required value="{{ old('mobile') }}" oninput="this.value=this.value.slice(0,20)">
                </div>
                <span class="text-danger  login-errorMessage">
                    @error('username')
                        {{ $message }}
                    @enderror
                </span>

                <div class="form-group position-relative">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" aria-describedby=""
                        placeholder="Password" required maxlength="30">
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
                            <div class="radioLogin student active-btn">
                                <input type="radio" value="student" name="loginAs" id="student" checked>
                                <span>Student</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin tutor">
                                <input type="radio" value="tutor" name="loginAs" id="tutor">
                                <span>Tutor</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin parents">
                                <input type="radio" value="parent" name="loginAs" id="parents">
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
                <button type="submit" class="btn brand-bg-Color mb-3">Login</button>

                <br>
                {{-- <a href="#">
                    <div class="googleLogin">

                        <img src="{{ url('frontendnew/img/icons/google-logo.png') }}" alt=""><span>Sign in with
                            Google</span>

                    </div>

                </a> --}}

                <div class="forgotPwd mt-3">
                    <p> Don't have an account? <a href="{{ '/student/register' }}" class="register">Register</a></p>
                    <button type="button" class="btn btn-sm" data-dismiss="modal" data-toggle="modal"
                        data-target="#forgetPasswordPopup">
                        Forgot password?
                    </button>
                </div>

            </form>
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
                    <button type="submit" id="forgetPasswordBtnAjax"
                        class="btn brand-bg-Color popuplogin mb-3 ml-auto">Send Code</button>

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
@endsection
<script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
<script>
    $(document).ready(function() {
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
    });
</script>
<script>
    $(document).on('click', '.radioLogin', function() {
        // remove active class from all
        $('.radioLogin').removeClass('active-btn');

        // add to clicked one
        $(this).addClass('active-btn');

        // check the radio inside it
        $(this).find('input[type="radio"]').prop('checked', true);
    });
</script>
