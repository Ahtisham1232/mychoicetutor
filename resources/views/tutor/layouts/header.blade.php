<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Online Tuition App | student Dashboard & Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose student & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="/images/MCTfavicon.png">

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
    <link rel="stylesheet"
        href="{{ url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

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
                            <a href="/tutor/dashboard" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                                </span>
                                <span class="logo-lg">

                                    <img src="/images/MCT Logo.png" alt="logo" height="17">
                                </span>
                            </a>

                            <a href="/tutor/dashboard" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                                </span>
                                <span class="logo-lg">
                                    <img src="/images/MCT Logo.png" width="120px" alt="logo">
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

                        <div class="topBtn">
                            {{-- <button type="button" style=""
                                class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                                id="topnav-hamburger-icon">
                                <a href="/tutor/tutorslots">
                                    <div class="btn btn-primary">Slots Management</div>
                                </a>
                            </button>
                            <button type="button"
                                class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                                id="topnav-hamburger-icon">
                                <a href="/tutor/getclasslist">
                                    <div class="btn btn-primary">Scheduled Classes</div>
                                </a>
                            </button> --}}

                        </div>

                    </div>

                    <div class="d-flex align-items-center">
                        <button type="button"
                        class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                        id="topnav-hamburger-icon">
                            <a href="{{route('tutor.messages')}}"> <button class="btn btn-sm fw-bold" style="color: white; background-color:#5353be; margin-right:10px">Open Chat</button></a>
                            <a href="{{route('home')}}" target="_blank"> <button class="btn btn-sm" style="color: black; background-color:#F3F3F9">Visit Website</button></a>
                    </button>
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="all-notification-check01">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check01">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check02">
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
                                                                invoice #12501.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 10 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check03">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check04">
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

                                <div class="dropdown-head bg-primary bg-pattern rounded-top" style="margin: 0px 10px">
                                    <div class="" style="padding-left: 10px">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-0 fs-16 fw-semibold text-white"> Notifications </h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-2 pt-2">
                                        <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom"
                                            data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab"
                                                    role="tab" aria-selected="true">All</a>
                                            </li>

                                             <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" href="{{ route('tutor.notifications') }}">
                                                    Previous Notifications
                                                </a>
                                            </li>
                                
                                            {{--<li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab" aria-selected="false">Messages</a>
                                            </li>
                                            <li class="nav-item waves-effect waves-light">
                                                <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab" aria-selected="false">Alerts</a>
                                            </li> --}}
                                        </ul>
                                    </div>

                                </div>

                                <div class="tab-content position-relative" id="notificationItemsTabContent1">
                                    <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab"
                                        role="tabpanel">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check01">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check02">
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
                                                                invoice #12501.
                                                            </p>
                                                        </div>
                                                        <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                            <span><i class="mdi mdi-clock-outline"></i> 10 hrs
                                                                ago</span>
                                                        </p>
                                                    </div>
                                                    <div class="px-2 fs-15">
                                                        <div class="form-check notification-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check03">
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
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="messages-notification-check04">
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
                            @php
                                $tutorProfile = \App\Models\tutorprofile::where(
                                    'tutor_id',
                                    session('userid')->id,
                                )->first();
                            @endphp

                            <button type="button" class="btn" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ asset('/images/tutors/profilepics') }}/{{ $tutorProfile ? $tutorProfile->profile_pic : '1703078631.png' }}"
                                        alt="Header Avatar" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ session('userid')->name }}</span>
                                        {{-- <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Founder</span> --}}
                                    </span>
                                </span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ session('userid')->name }}</h6>
                                <a class="dropdown-item" href="{{ url('tutor/profile') }}"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Profile</span></a>
                                <a class="dropdown-item" href="{{ route('tutor.messages') }}"><i
                                        class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                                    <span class="align-middle">Messages</span></a>
                                {{-- <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a> --}}
                                {{-- <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a> --}}
                                <div class="dropdown-divider"></div>
                                {{-- <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a> --}}
                                {{-- <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a> --}}
                                {{-- <a class="dropdown-item" href="#"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a> --}}
                                <a class="dropdown-item" href="{{ route('tutor.notifications') }}"><i
                                    class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i>
                                <span class="align-middle">Notifications</span></a>
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
                                colors="primary:#f7b84b,secondary:#f06548"
                                style="width:100px;height:100px"></lord-icon>
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
                                colors="primary:#f7b84b,secondary:#f06548"
                                style="width:100px;height:100px"></lord-icon>
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
                <a href="/tutor/dashboard" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/MCT Logo.png" alt="logo" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="/tutor/dashboard" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/MCTsmallLogo.png" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/MCT Logo.png" width="120px" alt="logo">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>



            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('tutor/dashboard') ? 'active' : '' }}"
                                href="/tutor/dashboard" role="button" aria-expanded="false"
                                aria-controls="sidebarDashboards">
                                <img src="/images/Tutor-Dashboard-menu-icons/dashboard.svg" alt="">&nbsp;
                                <span data-key="t-dashboards"> Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item" hidden>
                            <a class="nav-link menu-link" href="#sidebarApps" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="sidebarApps">
                                <i class="ri-apps-2-line"></i> <span data-key="t-apps">Apps</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarApps">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" data-key="t-calendar"> Calendar </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.messages') }}" class="nav-link" data-key="t-chat">
                                            Chat </a>
                                    </li>

                                </ul>

                            </div>
                        </li>


                        <!-- My Classes Dropdown -->
                        <li class="nav-item">

                            <a href="{{ route('tutor.tutorslots') }}"
                                class="nav-link {{ Request::is('tutor/tutorslots') ? 'active' : '' }}"
                                data-key="t-horizontal">
                                <img src="/images/Tutor-Dashboard-menu-icons/Trial Classes.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">&nbsp; Slots Management</span>
                            </a>
                            <a href="{{ route('tutor.demolist') }}"
                                class="nav-link {{ Request::is('tutor/demolist') ? 'active' : '' }}"
                                data-key="t-horizontal">
                                <img src="/images/Tutor-Dashboard-menu-icons/Trial Classes.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">&nbsp; Trial Classes</span>
                            </a>

                            <a href="{{ route('tutor.liveclass.classlist') }}"
                                class="nav-link {{ Request::is('tutor/getclasslist') ? 'active' : '' }}"
                                data-key="t-horizontal">
                                <img src="/images/Tutor-Dashboard-menu-icons/My Scheduled Classes.svg"
                                    alt="">&nbsp; <span data-key="t-dashboards">&nbsp; Scheduled Classes</span>
                            </a>

                            <a href="{{ route('tutor.classes') }}"
                                class="nav-link {{ Request::is('tutor/classes') ? 'active' : '' }}"
                                data-key="t-horizontal">
                                <img src="/images/Tutor-Dashboard-menu-icons/My Complete Classes.svg"
                                    alt="">&nbsp; <span data-key="t-dashboards">&nbsp; Completed Classes</span>
                            </a>

                            <a href="{{ route('tutor.studentslist') }}"
                                class="nav-link {{ Request::is('tutor/students') ? 'active' : '' }}"
                                data-key="t-horizontal">
                                <img src="/images/Tutor-Dashboard-menu-icons/My Batches.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">&nbsp; My Students</span>
                            </a>

                        </li> <!-- end Dashboard Menu -->
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('tutor/assignments') || Request::is('tutor/learningcontents') ? 'active' : '' }}"
                                href="#sidebarLanding" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ Request::is('tutor/assignments') || Request::is('tutor/learningcontents') ? 'true' : 'false' }}"
                                aria-controls="sidebarLanding">
                                <img src="/images/Tutor-Dashboard-menu-icons/Study Materials.svg"
                                    alt="">&nbsp; <span data-key="t-dashboards">Study Materials</span>
                            </a>
                            <div class="collapse menu-dropdown {{ Request::is('tutor/assignments') || Request::is('tutor/learningcontents') ? 'show' : '' }}"
                                id="sidebarLanding">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.assignments') }}"
                                            class="nav-link {{ Request::is('tutor/assignments') ? 'active' : '' }}"
                                            data-key="t-one-page">Assignments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.learningcontents') }}"
                                            class="nav-link {{ Request::is('tutor/learningcontents') ? 'active' : '' }}"
                                            data-key="t-one-page">Learning Contents</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('tutor/questionbank') || Request::is('tutor/onlinetestlist') || Request::is('tutor/onlinetestresponseslist') ? 'active' : '' }}"
                                href="#sidebarFormsTest" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ Request::is('tutor/questionbank') || Request::is('tutor/onlinetestlist') || Request::is('tutor/onlinetestresponseslist') ? 'true' : 'false' }}"
                                aria-controls="sidebarFormsTest">
                                <img src="/images/Tutor-Dashboard-menu-icons/Online Tests.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">Online Tests</span>
                            </a>
                            <div class="collapse menu-dropdown {{ Request::is('tutor/questionbank') || Request::is('tutor/onlinetestlist') || Request::is('tutor/onlinetestresponseslist') ? 'show' : '' }}"
                                id="sidebarFormsTest">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ url('tutor/questionbank') }}"
                                            class="nav-link {{ Request::is('tutor/questionbank') ? 'active' : '' }}"
                                            data-key="t-form-select">Questions Bank</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('tutor/onlinetestlist') }}"
                                            class="nav-link {{ Request::is('tutor/onlinetestlist') ? 'active' : '' }}"
                                            data-key="t-form-select">Online Test</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ url('tutor/onlinetestresponseslist') }}"
                                            class="nav-link {{ Request::is('tutor/onlinetestresponseslist') ? 'active' : '' }}"
                                            data-key="t-form-select">Subjective Responses</a>
                                    </li>
                                </ul>
                            </div>
                        </li>



                        <!-- <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('tutor/payouts') || Request::is('tutor/payments') ? 'active' : '' }}"
                                href="#sidebarForms" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ Request::is('tutor/payouts') || Request::is('tutor/payments') ? 'true' : 'false' }}"
                                aria-controls="sidebarForms">
                                <img src="/images/Tutor-Dashboard-menu-icons/Payments.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">Payments</span>
                            </a>
                            <div class="collapse menu-dropdown {{ Request::is('tutor/payouts') || Request::is('tutor/payments') ? 'show' : '' }}"
                                id="sidebarForms">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.payouts') }}"
                                            class="nav-link {{ Request::is('tutor/payouts') ? 'active' : '' }}"
                                            data-key="t-form-select">Payouts</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.payments') }}"
                                            class="nav-link {{ Request::is('tutor/payments') ? 'active' : '' }}"
                                            data-key="t-form-select">Payment History</a>
                                    </li>
                                </ul>
                            </div>
                        </li> -->

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ Request::is('tutor/attendance') ? 'active' : '' }}"
                                href="#sidebarCharts" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ Request::is('tutor/attendance') ? 'true' : 'false' }}"
                                aria-controls="sidebarCharts">
                                <img src="/images/Tutor-Dashboard-menu-icons/Reports.svg" alt="">&nbsp;
                                <span data-key="t-dashboards">Reports</span>
                            </a>
                            <div class="collapse menu-dropdown {{ Request::is('tutor/attendance') ? 'show' : '' }}"
                                id="sidebarCharts">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('tutor.attendance') }}"
                                            class="nav-link {{ Request::is('tutor/attendance') ? 'active' : '' }}"
                                            data-key="t-chartjs">Attendance</a>
                                    </li>
                                </ul>
                            </div>
                        </li>



                        <li class="nav-item">
                            <a href="{{ route('tutor.messages') }}"
                                class="nav-link menu-link {{ Request::is('tutor/messages') ? 'active' : '' }}"> <img
                                    src="/images/Student-dashboard-menu-icon/Chat.svg" alt="">&nbsp; <span
                                    data-key="t-dashboards"> Chat</span> </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('tutor.feedback.list') }}"
                                class="nav-link menu-link {{ Request::is('tutor/feedback') ? 'active' : '' }}"
                                data-key="t-starter"> <img src="/images/Student-dashboard-menu-icon/Feedback.svg"
                                    alt="">&nbsp; <span data-key="t-dashboards"> Feedback</span> </a>
                        </li> --}}






                    </ul>
                    </nav>


                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>
