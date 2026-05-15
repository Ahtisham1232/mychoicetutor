
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
                        @php
                            $isUserLoggedIn = session()->has('userid') || Auth::check();
                            $userName = '';
                            $dashboardRoute = url('/');
                            
                            if ($isUserLoggedIn) {
                                if (session()->has('userid')) {
                                    $userObj = session('userid');
                                    $userName = $userObj->name ?? 'User';
                                    $userType = session('usertype');
                                    
                                    if ($userType == 'Parent') {
                                        $dashboardRoute = route('parent.dashboard');
                                    } elseif ($userType == 'Student' || (isset($userObj->role_id) && $userObj->role_id == 3)) {
                                        $dashboardRoute = route('student.dashboard');
                                    } elseif ($userType == 'Tutor' || (isset($userObj->role_id) && $userObj->role_id == 2)) {
                                        $dashboardRoute = route('tutor.dashboard');
                                    } elseif (isset($userObj->role_id) && $userObj->role_id == 1) {
                                        $dashboardRoute = route('admin.dashboard');
                                    }
                                } elseif (Auth::check()) {
                                    $userObj = Auth::user();
                                    $userName = $userObj->name;
                                    
                                    if ($userObj->role_id == 1) {
                                        $dashboardRoute = route('admin.dashboard');
                                    } elseif ($userObj->role_id == 2) {
                                        $dashboardRoute = route('tutor.dashboard');
                                    } elseif ($userObj->role_id == 3) {
                                        $dashboardRoute = route('student.dashboard');
                                    } elseif ($userObj->role_id == 4) {
                                        $dashboardRoute = route('parent.dashboard');
                                    }
                                }
                            }
                        @endphp
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item cta-btn mobBtn">
                                @if(!$isUserLoggedIn)
                                    <div class="mobLogin">
                                        <a href="{{ route('studentlogin')}}" class="btn btn-sm" style="color: black">Login</a>

                                    </div>
                                    <div >
                                        <a href="{{ url('/student/register') }}" class="btn btn-sm ">Get Started</a>
                                    </div>
                                @else
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm dropdown-toggle" type="button" id="profileMenuMob" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #666; background: transparent; border: 1px solid #ccc;">
                                            <i class="fa fa-user-circle"></i> {{ $userName }}
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenuMob">
                                            <a class="dropdown-item" href="{{ $dashboardRoute }}">
                                                <i class="fa fa-dashboard mr-2"></i> Dashboard
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                                <i class="fa fa-sign-out mr-2"></i> Logout
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </li>
                        </ul>

                        <ul class="navbar-nav ml-auto pl-0 topLine">
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('findatutor') ? 'active' : '' }}" href="{{ url('/findatutor') }}" style="padding-left: 6px; padding-right: 6px;">Find a tutor</a>
                            </li>
                            <li class="" style="margin-right: 10px;">
                                <a class="nav-link {{ Request::is('aboutus') ? 'active' : '' }}" href="{{ url('/aboutus') }}" style="padding-left: 6px; padding-right: 6px;">About Us</a>
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
                                    @if(!$isUserLoggedIn)
                                        <a href="{{ route('studentlogin')}}" class="btn btn-sm" style="color: white">Login</a>
                                        <a href="{{ url('/student/register') }}" class="btn btn-sm">Get Started</a>
                                    @else
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-sm dropdown-toggle" type="button" id="profileMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-user-circle"></i> {{ $userName }}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                                                <a class="dropdown-item" href="{{ $dashboardRoute }}">
                                                    <i class="fa fa-dashboard mr-2"></i> Dashboard
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                                    <i class="fa fa-sign-out mr-2"></i> Logout
                                                </a>
                                            </div>
                                        </div>
                                    @endif
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
