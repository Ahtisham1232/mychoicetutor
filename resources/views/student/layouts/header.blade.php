@php
use App\Models\studentprofile;
$studentprofile = studentprofile::where('student_id', session('userid')->id)->first();

@endphp
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Online Tuition App | student Dashboard & Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose student & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- Favicon -->
    <link rel="icon" href="{{ url('frontendnew/img/icons/mct-favicon.png') }}" type="image/x-icon">

    <!-- plugin css -->
    <link href="{{ url('new-styles/assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ url('new-styles/assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ url('new-styles/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ url('new-styles/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ url('new-styles/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ url('new-styles/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('css/custom.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>


<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="/student/dashboard" class="logo logo-dark">
                                <span class="logo-sm">
                                    <!-- <h3>LOGO</h3> -->
                                    <img src="{{ url('/images/MCT Logo.png') }}" alt="" height="22">

                                </span>
                                <span class="logo-lg">
                                    <!-- <h3>LOGO</h3> -->
                                    <img src="{{ url('/images/MCT Logo.png') }}" alt="" height="17">
                                </span>
                            </a>

                            <a href="/student/dashboard" class="logo logo-light">
                                <span class="logo-sm">
                                    <!-- <h3>LOGO</h3> -->
                                    <img src="{{ url('images/MCT Logo.png') }}" alt="" height="22">

                                </span>
                                <span class="logo-lg">
                                    <!-- <h3>LOGO</h3> -->
                                    <img src="{{ url('images/MCT Logo.png') }}" alt="" height="17">
                                </span>
                            </a>
                        </div>

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>




                    </div>

                    <!-- <div class="studentTopBtn d-flex">
                        <a href="/student/classes" style="padding-right: 20px"> <button
                                class="btn btn-primary">Scheduled
                                Classes</button></a>
                        <a href="/student/searchtutor" style="padding-right: 20px"> <button
                                class="btn btn-primary">Explore Tutors</button></a>
                        <a href="/" target="_blank"> <button class="btn btn-primary">Visit Website</button></a>
                    </div> -->
<!--
                    <div class="topBtn" style="margin-top:15px">
                        <a href="/student/classes" style="padding-right: 10px"> <button class="btn btn-primary">Upcoming
                                Classes</button></a>
                        <a href="/student/searchtutor" style="padding-right: 10px"> <button
                                class="btn btn-primary">Explore Tutors</button></a>
                        <a href="/" target="_blank"> <button class="btn btn-primary">Visit Website</button></a>
                    </div> -->

                    <div class="d-flex">
                        <div class="topBtn" style="margin-top:15px">

                            <a href="{{route('student.messages')}}"> <button class="btn btn-sm fw-bold" style="color: white; background-color:#5353be;margin-right:10px">Open Chat</button></a>
                            <a href="{{route('home')}}" target="_blank"> <button class="btn btn-sm" style="color: black; background-color:#F3F3F9">Visit Website</button></a>
                        </div>
                        <div class="dropdown d-md-none topbar-head-dropdown header-item" hidden>
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..."
                                                aria-label="Recipient's username">
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>





                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button"
                                class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">

                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="tab-content position-relative" id="notificationItemsTabContent">
                                    <div class="tab-pane noti-sec fade show active py-2 ps-2" id="all-noti-tab"
                                        role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2"
                                            id="allnotificationsmessages">
                                            <div
                                                class="text-reset notification-item d-block dropdown-item position-relative">
                                                <div class="d-flex">
                                                    <div class="avatar-xs me-3 flex-shrink-0">
                                                        <span
                                                            class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                            <i class="bx bx-badge-check"></i>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-2 lh-base">Your <b>Elite</b> author
                                                                Geqwewqewqeqweqweqwraphic
                                                                Optimization <span class="text-secondary">reward</span>
                                                                is
                                                                ready!
                                                            </h6>
                                                        </a>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> Just 30 sec
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="all-notification-check01">
                                                            <label class="form-check-label"
                                                                for="all-notification-check01"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="my-3 text-center view-all">
                                                <button type="button"
                                                    class="btn btn-soft-success waves-effect waves-light">View
                                                    All Notifications <i
                                                        class="ri-arrow-right-line align-middle"></i></button>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel"
                                        aria-labelledby="messages-tab">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">
                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-3.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">We talked about a project on linkedin.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 30 min
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check01">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check01"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-2.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">Answered to your comment on the cash flow
                                                                forecast's
                                                                graph 🔔.</p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 2 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check02">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check02"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-6.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Kenneth Brown</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">Mentionned you in his comment on 📃
                                                                invoice
                                                                #12501.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 10 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check03">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check03"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-8.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">We talked about a project on linkedin.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 3 days
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check04">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check04"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-3 text-center view-all">
                                                <button type="button"
                                                    class="btn btn-soft-success waves-effect waves-light">View
                                                    All Messages <i
                                                        class="ri-arrow-right-line align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"
                                        aria-labelledby="alerts-tab"></div>

                                    <div class="notification-actions" id="notification-actions">
                                        <div class="d-flex text-muted justify-content-center">
                                            Select <div id="select-content" class="text-body fw-semibold px-1">0</div>
                                            Result <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#removeNotificationModal">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- /////////////////////// --}}

                        <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">

                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                                id="page-header-notifications-dropdown" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                                <i class='bx bx-bell fs-22'></i>
                                <span id="unreadNotificationCount"
                                    class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">0<span
                                        class="visually-hidden">unread messages</span></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">

                                <div class="dropdown-head bg-primary bg-pattern rounded-top" style="margin:0 10px">
                                    <div class="" style="padding-left: 10px">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-2 pt-2">
                                        <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true"
                                            id="notificationItemsTab" role="tablist">
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab"
                                                    role="tab" aria-selected="true">All</a>
                                            </li>
                                            {{--
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab" aria-selected="false">Messages</a>
                                            </li>
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab" aria-selected="false">Alerts</a>
                                            </li> --}}
                                        </ul>
                                    </div>

                                </div>

                                <div class="tab-content position-relative" id="notificationItemsTabContent1">
                                    <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">


                                        </div>

                                    </div>

                                    <div class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel"
                                        aria-labelledby="messages-tab">
                                        <div data-simplebar style="max-height: 300px;" class="pe-2">
                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-3.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">We talked about a project on linkedin.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 30 min
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check01">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check01"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-2.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">Answered to your comment on the cash flow
                                                                forecast's
                                                                graph 🔔.</p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 2 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check02">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check02"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-6.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Kenneth Brown</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">Mentionned you in his comment on 📃
                                                                invoice
                                                                #12501.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 10 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check03">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check03"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-reset notification-item d-block dropdown-item">
                                                <div class="d-flex">
                                                    <img src="{{ url('new-styles/assets/images/users/avatar-8.jpg') }}"
                                                        class="me-3 rounded-circle avatar-xs" alt="user-pic">
                                                    <div class="flex-grow-1">
                                                        <a href="#!" class="stretched-link">
                                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                        </a>
                                                        <div class="fs-13 text-muted">
                                                            <p class="mb-1">We talked about a project on linkedin.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 3 days
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox" value=""
                                                                id="messages-notification-check04">
                                                            <label class="form-check-label"
                                                                for="messages-notification-check04"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="my-3 text-center view-all">
                                                <button type="button"
                                                    class="btn btn-soft-success waves-effect waves-light">View
                                                    All Messages <i
                                                        class="ri-arrow-right-line align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"
                                        aria-labelledby="alerts-tab"></div>

                                    <div class="notification-actions" id="notification-actions">
                                        <div class="d-flex text-muted justify-content-center">
                                            Select <div id="select-content" class="text-body fw-semibold px-1">0</div>
                                            Result <button type="button" class="btn btn-link link-danger p-0 ms-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#removeNotificationModal">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ url('images/students/profilepics/') }}/{{ $studentprofile->profile_pic}}"
                                    alt="" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ session('userid')->name }}</span>
                                        @if (session('usertype') == 'Parent')
                                        <p style="color: grey; font-size:10px">Logged In As Parent</p>
                                        @endif
                                        {{-- <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Founder</span> --}}
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">

                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ session('userid')->name }}</h6>

                                <a class="dropdown-item" href="{{ url('student/profile') }}"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Profile</span></a>
                                <a class="dropdown-item" href="{{ route('student.messages') }}"><i
                                        class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Messages</span></a>
                                {{-- <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a> --}}
                                {{-- <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a> --}}
                                <div class="dropdown-divider"></div>
                                {{-- <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a> --}}
                                {{-- <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a> --}}
                                <a class="dropdown-item" href="{{ route('student.notifications') }}"><i
                                        class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle" data-key="t-logout">Notifications</span></a>

                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout">Logout</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- logoutConfirmModal -->
        <div id="logoutConfirmModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to logout?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn btn-light text-dark w-sm" data-bs-dismiss="modal">Cancel</button>
                            <a href="{{ route('logout') }}" class="btn w-sm btn-danger">Yes, Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="/student/dashboard" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/MCT Logo.png" alt="logo" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="/student/dashboard" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <!-- <h1 style="color: white">Logo</h1> -->
                        <img src="/images/MCT Logo.png" width="120px" alt="logo">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid sidemenu">

                    <div id="two-column-menu">
                    </div>

                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('student/dashboard') ? 'active' : '' }}"
                                href="/student/dashboard" role="button" aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <img src="/images/Student-dashboard-menu-icon/Dashboard.svg" alt="">&nbsp;
                                <span data-key="t-dashboards"> Dashboard</span>
                            </a>
                        </li> <!-- end Dashboard Menu -->

                        {{-- <li class="nav-item">
                            <a href="{{ route('student.yourtutor') }}"
                                class="nav-link menu-link {{ Request::is('student/yourtutor') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/My Tutors.svg" alt="">&nbsp;
                                <span data-key="t-starter">My Tutor </span> </a>
                        </li> --}}

                        {{-- <li class="nav-item" hidden>
                        <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarApps">
                            <i class="ri-apps-2-line"></i> <span data-key="t-apps">Apps</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarApps">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="#" class="nav-link" data-key="t-calendar"> Calendar </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('student.messages')}}" class="nav-link" data-key="t-chat"> Chat
                                    </a>
                                </li>

                            </ul>
                        </div>
                        </li> --}}


                        <li class="nav-item">
                            <a href="{{ route('student.yourtutor') }}"
                                class="nav-link menu-link {{ Request::is('student/yourtutor') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/My Tutors.svg" alt="">&nbsp;
                                <span data-key="t-starter">My Tutors </span> </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('student.searchtutor') }}"
                                class="nav-link menu-link {{ Request::is('student/searchtutor') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/Explore tutors.svg" alt="">&nbsp; <span
                                    data-key="t-dashboards"> All Tutors</span> </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('student.demolist') }}"
                                class="nav-link menu-link {{ Request::is('student/demolist') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/My Scheduled Classes.svg" alt="">&nbsp;
                                <span data-key="t-starter">Trial Classes</span> </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('student.classes') }}"
                                class="nav-link menu-link {{ Request::is('student/classes') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/My Scheduled Classes.svg" alt="">&nbsp;
                                <span data-key="t-starter">Scheduled Classes</span> </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.completed-classes') }}"
                                class="nav-link menu-link {{ Request::is('student/completed-classes') ? 'active' : '' }}">
                                <img src="/images/Student-dashboard-menu-icon/My Scheduled Classes.svg" alt="">&nbsp;
                                <span data-key="t-starter">Completed Classes</span> </a>
                        </li>





                <!-- <li class="nav-item">
                    @php
                        $isClassesActive =
                            Request::is('student/demolist') ||
                            Request::is('student/classes') ||
                            Request::is('student/completed-classes');
                    @endphp
                    <a class="nav-link menu-link" href="#sidebarLayouts" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isClassesActive ? 'true' : 'false' }}" aria-controls="sidebarLayouts">
                        <img src="/images/Student-dashboard-menu-icon/My Classes.svg" alt="">&nbsp;<span
                            data-key="t-layouts"> My Classes</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isClassesActive ? 'show' : '' }}" id="sidebarLayouts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item submneu">
                                <img src="/images/Student-dashboard-menu-icon/My Scheduled Classes.svg" alt="">
                                <a href="{{ route('student.demolist') }}"
                                    class="nav-link {{ Request::is('student/demolist') ? 'active' : '' }}"
                                    data-key="">My Demo Classes</a>
                            </li>
                            <li class="nav-item submneu">
                                <img src="/images/Student-dashboard-menu-icon/My Scheduled Classes.svg" alt="">
                                <a href="{{ route('student.classes') }}"
                                    class="nav-link {{ Request::is('student/classes') ? 'active' : '' }}"
                                    data-key="">Upcoming Classes</a>
                            </li>
                            <li class="nav-item submneu">
                                <img src="/images/Student-dashboard-menu-icon/My recordings.svg" alt="">&nbsp;
                                <a href="{{ route('student.completed-classes') }}"
                                    class="nav-link {{ Request::is('student/completed-classes') ? 'active' : '' }}"
                                    data-key="t-horizontal">My Recordings</a>
                            </li>
                        </ul>
                    </div>
                </li> -->


                

                {{-- <li class="nav-item">
                            <a href="{{route('student.subjects')}}" class="nav-link menu-link"> <img
                    src="/images/Student-dashboard-menu-icon/My Subjects.svg" alt="">&nbsp; <span
                    data-key="t-dashboards"> My Subjects</span> </a>
                </li> --}}


                {{--
                        <li class="nav-item">
                            <a href="{{route('student.subjectlist')}}" class="nav-link menu-link"> <img
                    src="/images/Student-dashboard-menu-icon/Explore Subjects.svg" alt="">&nbsp; <span
                    data-key="t-dashboards"> Explore Subjects </span></a>
                </li> --}}




                <!-- <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Purchase</span></li> -->

                <li class="nav-item" hidden>
                    <a class="nav-link menu-link" href="#sidebarPages" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarPages">
                        <i class="ri-pages-line"></i> <span data-key="t-pages">My Purchases</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                            <li class="nav-item">
                                <a href="{{ route('student.searchtutor') }}" class="nav-link"
                                    data-key="t-starter">Purchase New Tutor </a>
                            </li>
                            <a href="{{ route('student.yourtutor') }}" class="nav-link" data-key="t-starter">
                                Tutor </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('student.subjects') }}" class="nav-link" data-key="t-starter">
                        Subjects </a>
                </li>

                </ul>
            </div>
            </li>

            <li class="nav-item">
                <a href="{{ route('student.mylearnings') }}"
                                class="nav-link menu-link {{ Request::is('student/mylearnings') ? 'active' : '' }}"><img src="/images/Student-dashboard-menu-icon/Study Materials.svg" alt="">&nbsp; <span
                                data-key="t-dashboards">Learning Contents</a>

            </li>
            <li class="nav-item">
                <a href="{{ route('student.assignments.list') }}"
                                class="nav-link menu-link {{ Request::is('student/assignments') ? 'active' : '' }}"><img src="/images/Student-dashboard-menu-icon/Study Materials.svg" alt="">&nbsp; <span
                                data-key="t-dashboards">Assignments</a>

            </li>





            <li class="nav-item">
                @php
                $isOnlineTestsActive = Request::is('student/exams');
                @endphp
                <a class="nav-link menu-link" href="#sidebarAdvanceUI" data-bs-toggle="collapse" role="button"
                    aria-expanded="{{ $isOnlineTestsActive ? 'true' : 'false' }}" aria-controls="sidebarAdvanceUI">
                    <img src="/images/Student-dashboard-menu-icon/Online Tests.svg" alt="">&nbsp; <span
                        data-key="t-advance-ui">Online Tests</span>
                </a>
                <div class="collapse menu-dropdown {{ $isOnlineTestsActive ? 'show' : '' }}" id="sidebarAdvanceUI">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.exams') }}"
                                class="nav-link {{ Request::is('student/exams') ? 'active' : '' }}"
                                data-key="t-alerts">Online Tests</a>
                        </li>
                    </ul>
                </div>
            </li>



            <li class="nav-item">
                @php
                $isPaymentsActive = Request::is('student/studentpayments');
                @endphp
                <a class="nav-link menu-link" href="#sidebarForms" data-bs-toggle="collapse" role="button"
                    aria-expanded="{{ $isPaymentsActive ? 'true' : 'false' }}" aria-controls="sidebarForms">
                    <img src="/images/Student-dashboard-menu-icon/Payments.svg" alt="">&nbsp; <span
                        data-key="t-forms">Payments</span>
                </a>
                <div class="collapse menu-dropdown {{ $isPaymentsActive ? 'show' : '' }}" id="sidebarForms">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.studentpayments') }}"
                                class="nav-link {{ Request::is('student/studentpayments') ? 'active' : '' }}"
                                data-key="t-form-select">
                                Payment History
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                @php
                $isReportsActive =
                Request::is('student/class-reports') || Request::is('student/attendance-reports');
                @endphp
                <a class="nav-link menu-link" href="#sidebarCharts" data-bs-toggle="collapse" role="button"
                    aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}" aria-controls="sidebarCharts">
                    <img src="/images/Student-dashboard-menu-icon/Reports.svg" alt="">&nbsp; <span
                        data-key="t-charts">Reports</span>
                </a>
                <div class="collapse menu-dropdown {{ $isReportsActive ? 'show' : '' }}" id="sidebarCharts">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.class.report') }}"
                                class="nav-link {{ Request::is('student/class-reports') ? 'active' : '' }}"
                                data-key="t-chartjs">
                                Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.attendance.report') }}"
                                class="nav-link {{ Request::is('student/attendance-reports') ? 'active' : '' }}"
                                data-key="t-chartjs">
                                Attendance
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="nav-item">
                <a href="{{ route('student.messages') }}"
                    class="nav-link menu-link {{ Request::is('student/messages') ? 'active' : '' }}"> <img
                        src="/images/Student-dashboard-menu-icon/Chat.svg" alt="">&nbsp; <span data-key="t-dashboards">
                        Chat</span> </a>
            </li>
            {{-- <li class="nav-item">
                <a href="{{ route('student.myfeedback') }}"
                    class="nav-link menu-link {{ Request::is('student/myfeedback') ? 'active' : '' }}"
                    data-key="t-starter"> <img src="/images/Student-dashboard-menu-icon/Feedback.svg" alt="">&nbsp;
                    <span data-key="t-dashboards"> Feedback</span> </a>
            </li> --}}


            <li class="nav-item" hidden>
                <a class="nav-link menu-link" href="#sidebarFormsChat" data-bs-toggle="collapse" role="button"
                    aria-expanded="false" aria-controls="sidebarForms">
                    <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Chat & Feedbacks</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarFormsChat">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item">
                            <a href="{{ route('student.messages') }}" class="nav-link" data-key="t-form-select">
                                Chat/Messages </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student.myfeedback') }}" class="nav-link" data-key="t-form-select">
                                Feedbacks </a>
                        </li>


                    </ul>
                </div>
            </li>


            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
