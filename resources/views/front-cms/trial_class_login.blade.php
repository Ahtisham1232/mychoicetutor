@extends('front-cms.layouts.main')
@section('main-section')
<!-- Modal -->
<div class="modal-dialog modal-dialog-centered" style="margin-top: 100px" role="document">
        <div class="modal-content loginModel">
            <div class="modal-header" style="border: none;">

            </div>
            <h3 class="text-center">Login</h3>
            <form class="loginForm" action="{{ url('/free-trial-class-student-login') }}" method="POST">
                @csrf
              <input type="hidden" id="tutorid" name="tutorid" value="{{$tutorid}}">
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
                    <div style="display:flex; gap:10px;">
                        <select class="form-control" id="country_code" name="country_code" style="max-width:100px;">
                            @foreach (config('phone') as $country)
                                <option value="{{ $country['code'] }}"
                                    {{ old('country_code', '+92') == $country['code'] ? 'selected' : '' }}>
                                    {{ $country['code'] }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control" id="mobile" name="mobile" aria-describedby="" 
                            placeholder="Your mobile number" value="{{ old('mobile') }}" maxlength="20"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,20)">
                    </div>
                </div>
                <span class="text-danger  login-errorMessage">
                    @error('mobile')
                        {{ $message }}
                    @enderror
                </span>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" aria-describedby=""
                        placeholder="Password">
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
                        {{-- <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin tutor">
                                <input type="radio" value="tutor" name="loginAs" id="tutor"> 
                                <span>Tutor</span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="radioLogin parents">
                                <input type="radio" value="parent" name="loginAs" id="parent"> 
                                <span>Parent</span>
                            </div>
                        </div> --}}
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
            {{--    <a href="#">
                    <div class="googleLogin">

                        <img src="{{ url('frontendnew/img/icons/google-logo.png') }}" alt=""><span>Sign in with
                            Google</span>

                    </div>

                </a> --}}

                <div class="forgotPwd mt-3">
                    <p> Don't have an account? <a href="{{ '/student/register' }}" class="register">Register</a></p>
                    <a href="#">Forgot password?</a>
                </div>







            </form>
        </div>

</div>
@endsection