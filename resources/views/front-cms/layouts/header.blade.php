
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://fonts.googleapis.com/css?family=Quicksand:400,600,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="{{url('frontendnew/fonts/icomoon/style.css')}}">
        <link rel="stylesheet" href="{{url('frontendnew/css/owl.carousel.min.css')}}">
        <script src="{{ url('frontendnew/js/jquery-3.3.1.min.js') }}"></script>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{url('frontendnew/css/bootstrap.min.css')}}">
        <!-- Style -->
        <link rel="stylesheet" href="{{url('frontendnew/css/style.css')}}">
        <link rel="stylesheet" href="{{url('frontendnew/css/responsive.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <!-- Favicon -->
        <link rel="icon" href="{{url('frontendnew/img/icons/mct-favicon.png')}}" type="image/x-icon">
        <title>My Choice Tutor</title>
    </head>
    <body>
        <header role="banner" style="background-color: #fff; border-bottom: 1px solid #dcdcdc">
            <nav class="navbar navbar-expand-xl  navbar-dark bg-dark">
                <div class="container-fluid">
                    <div class="navFlx">
                    <button
                        class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#navbarsExample05"
                        aria-controls="navbarsExample05"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="navbar-toggler-icon">
                            <i class="fa fa-bars" aria-hidden="true" style="color:black"></i>
                            <!-- <img src="{{url('frontendnew/img/icons/book-03.png')}}" alt=""> -->
                        </span>
                    </button>
                    <div  class="logoMobNum">
                       <div class="logo">
                            <a href="{{('/')}}">
                                <img src="{{url('frontendnew/img/logo-mtc.png')}}" width="116px" alt="">
                            </a>
                       </div>
                        <div class="logo">
                            <a class="mob" href="tel:07761 975326">
                                <i class="fa fa-phone" ></i>
                                <span>07761 975326</span>
                            </a>
                        </div>
                    </div>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarsExample05">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item cta-btn mobBtn">
                                <div class="mobLogin">
                                    <button class="btn btn-sm" data-toggle="modal" data-target="#loginPopup">Login</button>
                                </div>
                                <div >
                                <a href="{{('/student/register')}}" class="btn btn-sm ">Get Started</a>
                                </div>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto pl-0 topLine">
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('findatutor') ? 'active' : '' }}" href="{{ url('/findatutor') }}" style="padding-left: 6px; padding-right: 6px;">Find a tutor</a>
                            </li>
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('aboutus') ? 'active' : '' }}" href="{{ url('/aboutus') }}" style="padding-left: 6px; padding-right: 6px;">Why choose us?</a>
                            </li>
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('blogs') ? 'active' : '' }}" href="{{ url('/blogs') }}" style="padding-left: 6px; padding-right: 6px;">Blogs</a>
                            </li>
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('howitworks') ? 'active' : '' }}" href="{{ url('/howitworks') }}" style="padding-left: 6px; padding-right: 6px;">How it works</a>
                            </li>
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}" style="padding-left: 6px; padding-right: 6px;">Contact Us</a>
                            </li>
                        </ul>


                        <ul class="navbar-nav ml-auto mr-4 deskBtn">
                            <li class="nav-item cta-btn">
                                <div class="btnSec">
                                    <button class="btn btn-sm " data-toggle="modal" data-target="#loginPopup">Login</button>
                                    <a href="{{('/student/register')}}" class="btn btn-sm ">Get Started</a>
                                    <span style="display:none;" >
                                        En
                                        <i class="fa fa-angle-down "></i>
                                    </span>
                                </div>
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto mobLang mt-2" style="display:none;" >
                            <li class="nav-item cta-btn">
                                <div >
                                    <span>
                                        En <i class="fa fa-angle-down "></i>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
