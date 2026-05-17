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
    
     @media (max-width: 576px) {
        .footer-bottom{
            padding: 20px;
        }
    }
</style>






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
                    <a href="{{route('faqs')}}">
                        <li>FAQS</li>
                    </a>

                </ul>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <h5 class="mb-4">Popular Subjects</h5>

                @php
                    $footerSubjects = App\Helpers\CommonHelper::getPopularSubjects(8);
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
               
                <ul class="contactDetail" style="margin-left:2px; list-style: none; padding: 0;">
                    <li class="contact-item">
                        <a href="https://wa.me/447761975326" target="_blank" style="display: flex; align-items: center; gap: 10px; color: inherit; text-decoration: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="18" height="18"><path fill="#25D366" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                            <span>07761 975326</span>
                        </a>
                    </li>

                    <li class="contact-item">
                        <a href="tel:07761975326" style="display: flex; align-items: center; gap: 10px; color: inherit; text-decoration: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18"><path fill="#007bff" d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>
                            <span>07761 975326</span>
                        </a>
                    </li>

                    <li class="contact-item">
                        <a href="mailto:{{config('mail.support_address')}}" style="display: flex; align-items: center; gap: 10px; color: inherit; text-decoration: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18"><path fill="#EA4335" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                            <span>{{config('mail.support_address')}}</span>
                        </a>
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
        <p>Copyright © 2026 MyChoiceTutor. All rights reserved.</p>
    </div>
</footer>

<script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ url('frontendnew/js/popper.min.js') }}"></script>
<script src="{{ url('frontendnew/js/bootstrap.min.js') }}"></script>
<script src="{{ url('frontendnew/js/jquery.sticky.js') }}"></script>
<script src="{{ url('frontendnew/js/main.js') }}"></script>
</body>

</html>
