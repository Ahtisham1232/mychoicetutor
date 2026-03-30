@extends('front-cms.layouts.main')
@section('main-section')
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container">

            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                    <div class="registop">
                        <img src="{{url('frontendnew/img/registrationImg.png')}}" width="100%" alt="">
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 pt-5 mt-5">
                    <div class="regidform mt-5">
                        <h3 class="text-center mt-4">Change Your Password</h3>
                        @if (Session::has('success'))
                                <div class="alert alert-success">{{ Session::get('success') }}</div>
                            @endif
                            @if (Session::has('fail'))
                                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                            @endif
                        <form action="{{route('reset.password')}}" method="POST" class="">
                            @csrf
                             <input type="hidden" name="token" value="{{ $token }}">

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
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" aria-describedby=""
                                    placeholder="&#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226;">
                                <span class="text-danger error-message">
                                    @error('password_confirmation')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <span class="text-danger error-message">
                                @error('expcheck')
                                    {{ $message }}
                                @enderror
                            </span>
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-end">
                                    <div class="regSub">
                                        <button type="submit" class="btn btn-lg">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
