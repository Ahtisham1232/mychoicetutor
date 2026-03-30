@extends('front-cms.layouts.main')
@section('main-section')
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container">

            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                    <div class="registop">
                        <img src="{{ url('frontendnew/img/registrationImg.png') }}" width="100%" alt="">
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 pt-5 mt-5">
                    <div class="regidform mt-5">
                        <div class="success-icon mb-3">
                            <i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>
                        </div>

                        <h2 style="font-weight: 700; color: #1a1a1a;">Password Updated</h2>

                        <p class="mt-3" style="font-size: 16px; color: #555; line-height: 1.6;">
                            Your new password has been saved successfully.
                            <br>
                            <strong>Please use the "Login" button in the header above to access your account.</strong>
                        </p>

                        <hr class="mt-4 mb-4">

                        <small class="text-muted">
                            If you didn't request this change, please contact our support team immediately.
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
