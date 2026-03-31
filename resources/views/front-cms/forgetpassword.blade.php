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
                        <h3 class="text-center mt-4">Change Your Password</h3>
                        <div id="ajax-alert"></div>
                        @if (Session::has('success'))
                            <div class="alert alert-success">{{ Session::get('success') }}</div>
                        @endif
                        @if (Session::has('fail'))
                            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                        @endif
                        <form action="{{ route('reset.password') }}" method="POST" class="" id="resetPasswordForm">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label for="password">Create password:<span class="reqrd">*</span></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    aria-describedby=""
                                    placeholder="&#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226;" required>
                                <span class="text-danger error-message" id="password-error">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="form-group">
                                <label for="password">Retype password:<span class="reqrd">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" aria-describedby=""
                                    placeholder="&#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226; &#8226;" required>
                                <span class="text-danger error-message" id="password_confirmation-error">
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
    <script>
        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();

            let password = $('#password').val();
            let confirmPassword = $('#password_confirmation').val();

            // Clear old errors
            $('.error-message').html('');
            $('#ajax-alert').html('');

            //  Check password match BEFORE AJAX
            if (password !== confirmPassword) {
                $('#password_confirmation-error').html('Passwords do not match');
                return; 
            }

            // Continue AJAX if valid
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var originalText = $btn.html();

            $btn.html('Updating...').prop('disabled', true);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#ajax-alert').html('<div class="alert alert-success">' + response.message +
                            '</div>');
                        setTimeout(function() {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            }
                        }, 2000);
                    } else {
                        $btn.html(originalText).prop('disabled', false);
                    }
                },
                error: function() {
                    $btn.html(originalText).prop('disabled', false);
                    $('#ajax-alert').html('<div class="alert alert-danger">An error occurred.</div>');
                }
            });
        });
    </script>
@endsection
