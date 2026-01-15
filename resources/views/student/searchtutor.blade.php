@extends('student.layouts.main')
@section('main-section')
    <link rel="stylesheet" href="{{ url('frontend/css/profile.css') }}">

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            .dropdown-submenu {
                position: relative;
            }

            .dropdown-submenu>.dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -6px;
                margin-left: -1px;
                -webkit-border-radius: 0 6px 6px 6px;
                -moz-border-radius: 0 6px 6px;
                border-radius: 0 6px 6px 6px;
            }

            .dropdown-submenu:hover>.dropdown-menu {
                display: block;
            }

            .dropdown-submenu>a:after {
                display: block;
                content: " ";
                float: right;
                width: 0;
                height: 0;
                border-color: transparent;
                border-style: solid;
                border-width: 5px 0 5px 5px;
                border-left-color: #ccc;
                margin-top: 5px;
                margin-right: -10px;
            }

            .dropdown-submenu:hover>a:after {
                border-left-color: #fff;
            }

            .dropdown-submenu.pull-left {
                float: none;
            }

            .dropdown-submenu.pull-left>.dropdown-menu {
                left: -100%;
                margin-left: 10px;
                -webkit-border-radius: 6px 0 6px 6px;
                -moz-border-radius: 6px 0 6px 6px;
                border-radius: 6px 0 6px 6px;
            }

            .btns {
                display: flex;
            }

            .btns button {
                margin: 3px;
            }

            .slotbtn {
                width: 38%;
            }

            .card .card-title {
                margin-bottom: 0;
            }

            /* #enrollnow {

                margin-top: 4px;
            } */
        </style>
        <style>
            .slot {
                width: 50px;
                /* Adjust width as needed */
                /* height: 50px; */
                /* Adjust height as needed */
                border: .5px solid #000;
                /* Border color */
                border-radius: 10%;
                margin: 5px;
                cursor: pointer;
                display: inline-block;
                text-align: center;
                font-size: 11px;
                /* box-shadow: 2px 1px 1px gray; */
            }

            .available {
                background-color: #198754;
                /* Available slot color */
                color: white;
            }

            .booked {
                background-color: #dcdcdc;
                /* Booked slot color */
                /* color: white; */
            }

            .icon-heart {
                fill: red !important;
            }
        </style>

        <div class="page-content">
            <div class="container-fluid">


                {{-- <h3 class="text-center mb-5">Choose your Tutor</h3> --}}
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif
                <div class="mb-5 listHeader">
                    <div class="tu-sort">
                        <!-- <h3>{{ count($tutorlist) }} Search result for tutors</h3> -->
                         <h3>Find Your Perfect Tutor</h3>
                        {{-- <div class="tu-sort-right-area">
                            <div class="tu-sortby">
                                <span>Sort by: </span>
                                <div class="tu-select">
                                    <select class="form-control tu-selectv">
                                        <option>Price low to high </option>
                                        <option>Price high to low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="tu-filter-btn">
                                <a class="tu-listbtn active" href="{{url('search-listing.html')}}"><i class="icon icon-list"></i></a>
                                <a class="tu-listbtn" href="{{url('search-listing-two.html')}}"><i class="icon icon-grid"></i></a>
                            </div>
                        </div> --}}
                    </div>
                    <div class="btns">

                        <!-- <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        Sort By
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">Price</a>
                                        <a class="dropdown-item" href="#">Class</a>
                                        <a class="dropdown-item" href="#">Rating</a>
                                        <a class="dropdown-item" href="#">Experience</a>

                                    </div>
                                </div> -->
                        <div class="dropdown">
                            {{-- <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Sort By
                            </button> --}}
                            <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" tabindex="-1" href="#">Price</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item"><a tabindex="-1"
                                                href="{{ url('student/sorttutor/pricing/asc') }}">Ascending</a></li>
                                        <li class="dropdown-item"><a
                                                href="{{ url('student/sorttutor/pricing/desc') }}">Descending</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" tabindex="-1" href="#">Class</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item"><a tabindex="-1"
                                                href="{{ url('student/sorttutor/class/asc') }}">Ascending</a></li>
                                        <li class="dropdown-item"><a
                                                href="{{ url('student/sorttutor/class/desc') }}">Descending</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" tabindex="-1" href="#">Rating</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item"><a tabindex="-1"
                                                href="{{ url('student/sorttutor/rating/asc') }}">Ascending</a></li>
                                        <li class="dropdown-item"><a
                                                href="{{ url('student/sorttutor/rating/desc') }}">Descending</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" tabindex="-1" href="#">Experience</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item"><a tabindex="-1"
                                                href="{{ url('student/sorttutor/experience/asc') }}">Ascending</a></li>
                                        <li class="dropdown-item"><a
                                                href="{{ url('student/sorttutor/experience/desc') }}">Descending</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>


                        {{-- <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#advSearchModal"><i
                                class="fa fa-search"></i> Advance Search
                        </button> --}}
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-8 col-xxl-9 col-lg-8 col-md-12 col-sm-12 col-xs-12 searchOrder2">
                        <div class="tu-listinginfo-holder">
                            @if (isset($tutorlist))
                                @foreach ($tutorlist as $tutorlist)
                                    <div class="tu-listinginfo">
                                        <span class="tu-cardtag"></span>
                                        <div class="tu-listinginfo_wrapper">
                                            <div class="tu-listinginfo_title">
                                                <div class="tu-listinginfo-img">
                                                    <figure>
                                                        {{-- <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorlist->profile_pic ?? url('images/avatar/default_avatar_img.jpg') }}"
                                                            width="60px" alt="imge"> --}}
                                                            <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorlist->profile_pic }}"
                                    width="60px" alt=""
                                    onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                                    </figure>
                                                    <div class="tu-listing-heading">
                                                        <h5><a
                                                                href="/student/tutorprofile/{{ $tutorlist->tutor_id }}">{{ $tutorlist->name }}</a>
                                                            <i class="icon icon-check-circle tu-greenclr"
                                                                data-tippy-trigger="mouseenter"
                                                                data-tippy-html="#tu-verifed" data-tippy-interactive="true"
                                                                data-tippy-placement="top"></i>
                                                        </h5>
                                                        <div class="tu-listing-location">
                                                            <span>{{ $tutorlist->starrating }} <i
                                                                    class="fa-solid fa-star"></i><em></em></span>
                                                            <address><i class="icon icon-map-pin"></i> <span>Exp:</span>
                                                                {{ $tutorlist->experience }}</address>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tu-listinginfo_price">
                                                    <span>Starting from:</span>
                                                    <h4>£{{ $tutorlist->rateperhour }}/hr</h4>
                                                </div>
                                            </div>
                                            <div class="tu-listinginfo_description">
                                                <p>{{ $tutorlist->headline }}</p>
                                            </div>
                                            <div class="tu-listinginfo_service">
                                                <h6>You can get teaching service direct at</h6>
                                                <ul class="tu-service-list">
                                                    <li>
                                                        <span>
                                                            <i class="icon icon-home tu-greenclr"></i>
                                                            {{ $tutorlist->subject }}
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span>
                                                            <i class="icon icon-map-pin tu-blueclr"></i>
                                                            {{ $tutorlist->total_classes_done }}+ Lessons Completed
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span>
                                                            <i class="icon icon-video tu-orangeclr"></i>
                                                            {{ $tutorlist->total_topics }}+ Topics
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tu-listinginfo_btn">
                                            {{-- @if ($tutorlist->myfav)
                                                <div class="tu-iconheart">
                                                    <a href="addfav/{{ $tutorlist->tutor_id }}"><img
                                                            src="{{ asset('images/red-heart.png') }}" width="18px"
                                                            alt=""><span>&nbsp; Remove Favourite</span></a>
                                                </div>
                                            @else
                                                <div class="tu-iconheart">
                                                    <a href="addfav/{{ $tutorlist->tutor_id }}"><img
                                                            src="{{ asset('images/grey-heart.png') }}" width="18px"
                                                            alt=""><span>&nbsp; Add Favourite</span></a>
                                                </div>
                                            @endif --}}

                                            <div class="tu-btnarea">
                                                @if (session('usertype') == 'Parent')
                                                @else
                                                    <div>
                                                    <a href="#booktrial"> <button data-toggle="modal"
                                                            data-target="#openDemoModal" class="btn btn-sm btn-primary"
                                                            onclick="openDemoModal('{{ $tutorlist->tutor_id }}','{{ $tutorlist->name }}','{{ $tutorlist->subjectid }}','{{ $tutorlist->subject }}')">Trial
                                                            Class</button></a>
                                                    </div>

                                                    <div>
                                                        @if(!empty($tutorlist->subject))
                                                            <a href="enrollnow/{{ $tutorlist->tutor_id }}">
                                                                <button class="btn btn-sm btn-success">
                                                                    Enroll Now
                                                                </button>
                                                            </a>
                                                        @else
                                                            <span 
                                                                data-toggle="tooltip"
                                                                title="Tutor has not added any subject yet"
                                                                style="display:inline-block; cursor:not-allowed;"
                                                            >
                                                                <button 
                                                                    class="btn btn-sm btn-secondary"
                                                                    disabled
                                                                    style="pointer-events:none;"
                                                                >
                                                                    Enroll Now
                                                                </button>
                                                            </span>
                                                        @endif 
                                                    </div>



                                                @endif

                                                <!-- <a href="/student/tutorprofile/{{ $tutorlist->tutor_id }}"
                                                    class="tu-primbtn">View full profile</a> -->

                                                    <!-- <a  href="/student/tutorprofile/{{ $tutorlist->tutor_id }}"> <button
                                                            class="btn btn-sm btn-primary" id="enrollnow">View Full Profile</button></a> -->
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        {{-- <nav class="tu-pagination">
                            <ul>
                                <li class="tu-pagination-prev"><a href="#"><i class="icon icon-chevron-left"></i></a> </li>
                                <li><a href="#">1</a> </li>
                                <li><a href="#">2</a> </li>
                                <li><a href="#">3</a> </li>
                                <li class="active"><a href="#">4</a> </li>
                                <li><a href="#">...</a> </li>
                                <li><a href="#">60</a> </li>
                                <li class="tu-pagination-next"><a href="#"><i class="icon icon-chevron-right"></i></a> </li>
                            </ul>
                        </nav> --}}
                    </div>
                    <br>
                    {{-- Search/Filter --}}
                    <div class="col-xl-4 col-xxl-3 col-lg-4 col-md-12 col-sm-12 col-xs-12 searchOrder1">
                        <aside class="tu-asidewrapperq">
                            <a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-chevron-left"></i></a>
                            <form action="{{ route('student.tutoradvs') }}" method="POST">
                                @csrf
                                <div class="tu-aside-menu">
                                    <div class="tu-aside-holder">
                                        <div class="tu-asidetitle" data-bs-toggle="collapse" aria-expanded="true">
                                            <h6>Search Tutor</h6>
                                            <div class="input-group">
                                                <input type="text" class="form-control tu-input-field"
                                                    placeholder="Type Tutor Name..." id="ttsearch" name="ttsearch"
                                                    style="width: 70%;">
                                                <button class="btn btn-sm btn-success" style="width: 30%;"><i
                                                        class="fa fa-search"></i> Search</button>
                                            </div>
                                        </div>

                                        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side2"
                                            role="button" aria-expanded="true">
                                            <h6>Education level</h6>
                                        </div>
                                        <div id="side2" class="collapse show">
                                            <div class="tu-aside-content">
                                                <div class="tu-filterselect">
                                                    <div class="tu-select">
                                                        <select id="gradelistid" name="gradelistid"
                                                            data-placeholder="Select education level"
                                                            data-placeholderinput="Select education level"
                                                            class="form-control tu-input-field">
                                                            <option label="Select Grade"></option>
                                                            @foreach ($classes as $class)
                                                                <option value="{{ $class->id }}">{{ $class->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="tu-filterselect">
                                                    <h6>Choose subjects</h6>
                                                    <ul class="tu-categoriesfilter" style="max-height: 200px; overflow-y: auto; padding-right: 5px;">
                                                        @foreach ($subjectlist as $subject)
                                                            <li>
                                                                <div class="tu-check tu-checksm">
                                                                    <input type="checkbox"
                                                                        id="subject{{ $subject->id }}"
                                                                        name="subjectlistid[]"
                                                                        value="{{ $subject->id }}">
                                                                    <label
                                                                        for="subject{{ $subject->id }}">{{ $subject->name }}</label>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    {{-- <div class="show-more">
                                                        <a href="javascript:void(0);"
                                                            class="tu-readmorebtn tu-show_more">Show all</a>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tu-aside-holder">
                                        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side3"
                                            role="button" aria-expanded="true">
                                            <h5>Price range</h5>
                                        </div>
                                        <div id="side3" class="collapse show">
                                            <div class="tu-aside-content">
                                                <div class="tu-rangevalue" data-bs-target="#tu-rangecollapse"
                                                    role="list" aria-expanded="false">
                                                    <div class="tu-areasizebox">
                                                        <input type="number" class="form-control tu-input-field"
                                                            step="1" placeholder="Min price" id="tminprice"
                                                            name="tminprice" />
                                                        <input type="number" class="form-control tu-input-field"
                                                            step="1" placeholder="Max price" id="tmaxprice"
                                                            name="tmaxprice" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tu-distanceholder">
                                                <div id="tu-rangecollapse" class="collapse">
                                                    <div class="tu-distance">
                                                        <div id="tu-rangeslider" class="tu-tooltiparrow tu-rangeslider">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tu-aside-holder" hidden>
                                        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side1a"
                                            role="button" aria-expanded="true">
                                            <h5>Rating</h5>
                                        </div>
                                        <div id="side1a" class="collapse show">
                                            <div class="tu-aside-content">
                                                <ul class="tu-categoriesfilter">
                                                    <li>
                                                        <div class="tu-check tu-checksm">
                                                            <input type="checkbox" id="rate" name="rate">
                                                            <label for="rate">
                                                                <span class="tu-stars">
                                                                    <span></span>
                                                                </span>
                                                                <em class="tu-totalreview">
                                                                    <span>5.0/<em>5.0</em></span>
                                                                </em>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="tu-check tu-checksm">
                                                            <input type="checkbox" id="rate4" name="rate4">
                                                            <label for="rate4">
                                                                <span class="tu-stars tu-fourstar">
                                                                    <span></span>
                                                                </span>
                                                                <em class="tu-totalreview">
                                                                    <span>4.0/<em>5.0</em></span>
                                                                </em>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="tu-check tu-checksm">
                                                            <input type="checkbox" id="rate3" name="rate2">
                                                            <label for="rate3">
                                                                <span class="tu-stars tu-threestar">
                                                                    <span></span>
                                                                </span>
                                                                <em class="tu-totalreview">
                                                                    <span>3.0/<em>5.0</em></span>
                                                                </em>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="tu-check tu-checksm">
                                                            <input type="checkbox" id="rate2a" name="rate2a">
                                                            <label for="rate2a">
                                                                <span class="tu-stars tu-twostar">
                                                                    <span></span>
                                                                </span>
                                                                <em class="tu-totalreview">
                                                                    <span>2.0/<em>5.0</em></span>
                                                                </em>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="tu-check tu-checksm">
                                                            <input type="checkbox" id="rate1a" name="rate1a">
                                                            <label for="rate1a">
                                                                <span class="tu-stars tu-onestar">
                                                                    <span></span>
                                                                </span>
                                                                <em class="tu-totalreview">
                                                                    <span>1.0/<em>5.0</em></span>
                                                                </em>
                                                            </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tu-aside-holder" hidden>
                                        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side1ab"
                                            role="button" aria-expanded="true">
                                            <h5>Location</h5>
                                        </div>
                                        <div id="side1ab" class="collapse show">
                                            <div class="tu-aside-content">
                                                <ul class="tu-categoriesfilter">
                                                    @foreach ($countrylist as $country)
                                                        <li>
                                                            <div class="tu-check tu-checksm">
                                                                <input type="checkbox" id="name{{ $country->id }}"
                                                                    name="expcheck">
                                                                <label
                                                                    for="name{{ $country->id }}">{{ $country->name }}</label>
                                                            </div>
                                                        </li>
                                                    @endforeach


                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tu-filterbtns">

                                        <button type="submit" class="btn btn-primary mb-2">Apply filters</button>
                                        <button type="button" onclick="clearfilter()"> <a
                                                href="{{ url('student/searchtutor') }}" class="btn btn-info ">Clear all
                                                filters</a></button>
                                    </div>
                                </div>
                            </form>
                        </aside>
                    </div>
                </div>

                {{-- Modified search modal as per customer requirements --}}
                <div class="modal fade" id="advSearchModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <style>
                                .pb-5,
                                .py-5 {
                                    padding-bottom: 0 !important;
                                }

                                select.form-control {
                                    color: black !important;
                                }
                            </style>

                            <div class="modal-body">


                                <header>
                                    <h3 class="text-center mb-4">Search Tutor</h3>
                                </header>
                                <form action="{{ route('student.tutoradvs') }}" method="POST"
                                    class="multi-range-field my-5 pb-5">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="name">Class</label>
                                            <select type="text" class="form-control" id="class"
                                                onchange="fetchSubjects()" name="class_name">
                                                <option value="">--Select--</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                @endforeach
                                            </select>


                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Subject</label>
                                                <select type="text" class="form-control" id="subject"
                                                    name="subject">
                                                    <option value="">--Select--</option>
                                                    @foreach ($subjectlist as $subjectlist)
                                                        <option value="{{ $subjectlist->id }}">{{ $subjectlist->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nationality</label>
                                                <select type="text" class="form-control" id="country"
                                                    name="country">
                                                    <option value="">--Select--</option>
                                                    @foreach ($countrylist as $countrylist)
                                                        <option value="{{ $countrylist->id }}">{{ $countrylist->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="name">Keyword</label>
                                            <input type="text" class="form-control" id="class" name="keywords">
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <label for="name">Charges</label>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <input type="number" placeholder="Min" class="form-control" name="minrate"
                                                id="minrate">
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <input type="number" placeholder="Max" class="form-control" name="maxrate"
                                                id="maxrate"></select>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4 col-12">
                                            <label for="name">Experience</label>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <input type="text" class="form-control" placeholder="Min" id="minexp"
                                                name="minexp">
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <input type="text" class="form-control" placeholder="Max" id="maxexp"
                                                name="maxexp">


                                        </div>
                                    </div>
                                    <button type="submit" id=""
                                        class="btn btn-sm btn-primary moveRight mt-2"><i class="fa fa-search"></i>
                                        Search</button>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enroll modal -->
                <div class="modal fade" id="openEnrollModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <header>
                                    <h3 class="text-center mb-4">Enroll Now</h3>
                                </header>
                                <form action="{{ route('student.purchaseclass') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Tutor</label>
                                            <input type="hidden" id="tutorenrollid" name="tutorenrollid">
                                            <input type="text" class="form-control" name="tutorenroll"
                                                id="tutorenroll" disabled>
                                            <span class="text-danger">
                                                @error('tutorenrollid')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Subject</label>
                                            <input type="hidden" id="subjectenrollid" name="subjectenrollid">
                                            <input type="text" class="form-control" name="subjectenroll"
                                                id="subjectenroll" disabled>
                                            <span class="text-danger">
                                                @error('subjectenrollid')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Available Class</label>
                                            <input type="text" class="form-control" name="availableclassenroll"
                                                id="availableclassenroll" readonly>
                                            <span class="text-danger">
                                                @error('availableclassenroll')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Required Class<i style="color: red">*</i></label>
                                            <input type="number" class="form-control" name="requiredclassenroll"
                                                id="requiredclassenroll" onkeyup="calculate();valuechanged()" required>
                                            <span class="text-danger">
                                                @error('requiredclassenroll')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Rate/Hr</label>
                                            <input type="text" class="form-control" name="rateperhourenroll"
                                                id="rateperhourenroll" readonly>
                                            <span class="text-danger">
                                                @error('rateperhourenroll')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 col-sm-6 mb-2">
                                            <label for="">Total Amount</label>
                                            <input type="text" class="form-control" name="totalamountenroll"
                                                id="totalamountenroll" readonly>
                                            <span class="text-danger">
                                                @error('totalamountenroll')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="">Date<span style="color:red">*</span></label>
                                            <input type="date" class="form-control" id="classdate" name="classdate"
                                                min="{{ date('Y-m-d', strtotime('+0 day')) }}" onchange="searchSlots()">
                                            <span class="text-danger">
                                                @error('classdate')
                                                    {{ 'Date is required' }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 mb-3">
                                            {{-- <label for="">Selected Slot</label> --}}
                                            <input type="hidden" class="form-control" id="selectedSlot"
                                                name="selectedSlot" readonly>
                                            <span class="text-danger">
                                                @error('selectedSlot')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <div class="col-12 mb-3">
                                            {{-- <label for="">Selected Slot</label> --}}
                                            <input type="hidden" class="form-control" id="selectedSlotIds"
                                                name="selectedSlotIds" readonly>
                                            <span class="text-danger">
                                                @error('selectedSlot')
                                                    {{ $message }}
                                                @enderror
                                            </span>
                                        </div>
                                        <!-- Available Slots Section -->
                                        <div id="availableSlots" class="mt-4">
                                            <!-- Slots will be dynamically added here based on search results -->
                                            <!-- Example Slot: <div class="slot" onclick="toggleSlot(this)"></div> -->
                                        </div>
                                        <p id="selectedSlotConfirmation"></p>
                                        <div class="col-12 col-md-12 col-sm-6 mb-2">
                                            <input class="" type="checkbox" id="contactadmin"
                                                name="contactadmin"><label for="contactadmin">&nbsp;Contact admin</label>
                                        </div>
                                    </div>
                                    <div class="row" style="float:right">
                                        <div class="col-12 col-md-12 col-sm-12 mb-2" id="formbuttons">
                                            <button class="btn btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                                            {{-- <button type="submit" class="btn btn-sm btn-success">Pay Now</button> --}}
                                            <button type="button" onclick="confirmformdata()"
                                                class="btn btn-sm btn-success">Confirm</button>
                                            <button type="button" onclick="confirmformdata()"
                                                class="btn btn-sm btn-success">Confirm</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!--Demo modal -->
                <div class="modal fade" id="openDemoModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">


                            <div class="modal-body">


                                <header>
                                    <h3 class="text-center ">Free Trial Class</h3>
                                    <p style="color: red">*Please provide your preferred time slots for the trial class.
                                        Once confirmed by the tutor, be punctual and join at the specified time.</p>
                                </header>

                                <form action="{{ route('student.bookdemo') }}" method="POST">
                                    @csrf
                                    <div class=" row mb-2">

                                        <div class="form-group col-md-6">
                                            {{-- <label for="">Candidate</label> --}}
                                            <input type="hidden" class="form-control" id="demostudentname"
                                                value="{{ session('userid')->name }}" disabled>

                                        </div>
                                        <div class="form-group col-md-6">
                                            {{-- <label for="">Tutor</label> --}}
                                            <input type="hidden" class="" id="demotutorid" name="demotutorid">
                                            <input type="hidden" class="form-control" id="demotutorname"
                                                name="demotutorname" disabled>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <label for="name">Subject</label>
                                            {{-- <input type="text" id="demosubjectid" name="demosubjectid"> --}}
                                            {{-- <input type="text" class="form-control" id="demosubjectname"
                                                name="demosubjectname" disabled> --}}
                                            <select class="form-control" id="demosubjectid" name="demosubjectid"
                                                required></select>
                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <label for="">Prefer Slot 1<i style="color: red;">*</i></label>
                                            <input type="datetime-local" class="form-control" id="demoslotfirst"
                                                name="demoslotfirst" required>

                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <label for="">Prefer Slot 2</label>
                                            <input type="datetime-local" class="form-control" id="demoslotsecond"
                                                name="demoslotsecond">

                                        </div>
                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                            <label for="">Prefer Slot 3</label>
                                            <input type="datetime-local" class="form-control" id="demoslotthird"
                                                name="demoslotthird">

                                        </div>

                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <label for="">Message(<span><i>Optional</i></span>)</label>
                                            <textarea class="form-control" id="message" name="message" style="height: 110px !important"></textarea>

                                        </div>

                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" id="" class="btn btn-sm btn-success"
                                            style="float:right ">Book Trial</button>
                                    </div>

                            </div>





                        </div>






                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- Schedule modal -->
        <div class="modal fade" id="scheduleclassmodal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">

                    <div class="modal-body">

                        <header>
                            <h3 class="text-center mb-4">Available Slots</h3>
                        </header>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <input type="hidden" id="tutorslotid" name="tutorslotid">
                                <label>Date<span style="color:red">*</span></label>
                                <input type="date" class="form-control" id="classdate" name="classdate"
                                    min="{{ date('Y-m-d', strtotime('+0 day')) }}" onchange="searchSlots()">
                                <span class="text-danger">
                                    @error('classdate')
                                        {{ 'Date is required' }}
                                    @enderror
                                </span>
                            </div>
                            {{-- <div class="col-12 mb-3">
                           <button type="button" class="btn btn-sm btn-primary" onclick="searchSlots();">Search</button>
                       </div> --}}
                        </div>

                        <!-- Available Slots Section -->
                        {{-- <div id="availableSlots" class="mt-4"> --}}
                        <!-- Slots will be dynamically added here based on search results -->
                        <!-- Example Slot: <div class="slot" onclick="toggleSlot(this)"></div> -->
                    </div>


                </div>
            </div>
        </div>
    </div>
    <script>
        function openDemoModal(tid, tname, sid, sname) {
            getTutorSubjects(tid);
            $('#demotutorid').val(tid)
            $('#demotutorname').val(tname)
            // $('#demosubjectid').val(sid)
            // $('#demosubjectname').val(sname)
            $('#openDemoModal').modal('show')

        }

        $(document).ready(function() {
            // Function to validate slot timings
            function validateSlotTimings() {
                var now = new Date();
                var minTime = new Date(now.getTime() + 60 * 60 * 1000); // Current time + 1 hour

                // Validate Prefer Slot 1
                setMinDate('#demoslotfirst', minTime);

                // Validate Prefer Slot 2
                setMinDate('#demoslotsecond', minTime);

                // Validate Prefer Slot 3
                setMinDate('#demoslotthird', minTime);

                return true;
            }

            // Set min date for datetime-local input
            function setMinDate(selector, minDate) {
                // Get the current date and time in the required format
                var minDateString = minDate.toISOString().slice(0, 16);

                // Set the min attribute for the specified datetime-local input
                $(selector).attr('min', minDateString);
            }

            // Call the validation function on page load
            validateSlotTimings();
        });


        function openEnrollModal(tid, tname, sid, sname, ttopics, ratehr) {
            clearselectedslots()
            $('#tutorenrollid').val(tid)
            $('#tutorenroll').val(tname)
            $('#subjectenrollid').val(sid)
            $('#subjectenroll').val(sname)
            $('#availableclassenroll').val(ttopics)
            $('#rateperhourenroll').val(ratehr)
            // $('#totalamountenroll').val(ratehr * )
            $('#openEnrollModal').modal('show')
        }

        function calculate() {
            $('#totalamountenroll').val($('#rateperhourenroll').val() * $('#requiredclassenroll').val())
        }
    </script>
    <script>
        // range slid

        let inputLeft = document.getElementById('input-left');
        let inputRight = document.getElementById('input-right');
        let range = document.querySelector('.slider > .range');
        let priceFrom = document.querySelector('.price-from');
        let priceTo = document.querySelector('.price-to');

        function setLeftValue() {
            let _this = inputLeft,
                min = parseInt(_this.min),
                max = parseInt(_this.max);
            _this.value = Math.min(
                parseInt(_this.value),
                parseInt(inputRight.value) - 1
            );
            priceFrom.textContent = `Min: £${_this.value}`;
            let percent = ((_this.value - min) / (max - min)) * 100;
            range.style.left = `${percent}%`;
        }
        setLeftValue();

        function setRightValue() {
            let _this = inputRight,
                min = parseInt(_this.min),
                max = parseInt(_this.max);

            _this.value = Math.max(
                parseInt(_this.value),
                parseInt(inputLeft.value) + 1
            );
            priceTo.textContent = `Max: £${_this.value}`;
            let percent = ((_this.value - min) / (max - min)) * 100;
            range.style.right = `${100 - percent}%`;
        }
        setRightValue();
        inputLeft.addEventListener('input', setLeftValue);
        inputRight.addEventListener('input', setRightValue);

        inputLeft.addEventListener('mousemove', (e) => {
            inputLeft.classList.add('hover');
        });
        inputLeft.addEventListener('mouseout', (e) => {
            inputLeft.classList.remove('hover');
        });
        inputLeft.addEventListener('mousedown', (e) => {
            inputLeft.classList.add('active');
        });
        inputLeft.addEventListener('mouseup', (e) => {
            inputLeft.classList.remove('active');
        });
        inputLeft.addEventListener('touchstart', (e) => {
            inputLeft.classList.add('active');
        });
        inputLeft.addEventListener('touchend', (e) => {
            inputLeft.classList.remove('active');
        });
        inputRight.addEventListener('mouseover', (e) => {
            inputRight.classList.add('hover');
        });
        inputRight.addEventListener('mouseout', (e) => {
            inputRight.classList.remove('hover');
        });
        inputRight.addEventListener('mousedown', (e) => {
            inputRight.classList.add('active');
        });
        inputRight.addEventListener('mouseup', (e) => {
            inputRight.classList.remove('active');
        });
        inputRight.addEventListener('touchstart', (e) => {
            inputRight.classList.add('active');
        });
        inputRight.addEventListener('touchend', (e) => {
            inputRight.classList.remove('active');
        });

        //   experience range
        let minExp = document.getElementById('minexp');
        let maxExp = document.getElementById('maxexp');
        let rangeExp = document.querySelector('.slider1 > .range1');
        let expFrom = document.querySelector('.price-from1');
        let expTo = document.querySelector('.price-to1');

        function setMinValue() {
            let _this1 = minExp,
                min = parseInt(_this1.min),
                max = parseInt(_this1.max);
            _this1.value = Math.min(
                parseInt(_this1.value),
                parseInt(maxExp.value) - 1
            );
            expFrom.textContent = `Year: ${_this1.value}`;
            let percent1 = ((_this1.value - min) / (max - min)) * 100;
            rangeExp.style.left = `${percent1}%`;
        }
        setMinValue();

        function setMaxValue() {
            let _this1 = maxExp,
                min = parseInt(_this1.min),
                max = parseInt(_this1.max);

            _this1.value = Math.max(
                parseInt(_this1.value),
                parseInt(minExp.value) + 1
            );
            expTo.textContent = `Year: ${_this1.value}`;
            let percent1 = ((_this1.value - min) / (max - min)) * 100;
            rangeExp.style.right = `${100 - percent1}%`;
        }
        setMaxValue();
        minExp.addEventListener('input', setMinValue);
        maxExp.addEventListener('input', setMaxValue);

        minExp.addEventListener('mousemove', (e) => {
            minExp.classList.add('hover');
        });
        minExp.addEventListener('mouseout', (e) => {
            minExp.classList.remove('hover');
        });
        minExp.addEventListener('mousedown', (e) => {
            minExp.classList.add('active');
        });
        minExp.addEventListener('mouseup', (e) => {
            minExp.classList.remove('active');
        });
        minExp.addEventListener('touchstart', (e) => {
            minExp.classList.add('active');
        });
        minExp.addEventListener('touchend', (e) => {
            minExp.classList.remove('active');
        });
        maxExp.addEventListener('mouseover', (e) => {
            maxExp.classList.add('hover');
        });
        maxExp.addEventListener('mouseout', (e) => {
            maxExp.classList.remove('hover');
        });
        maxExp.addEventListener('mousedown', (e) => {
            maxExp.classList.add('active');
        });
        maxExp.addEventListener('mouseup', (e) => {
            maxExp.classList.remove('active');
        });
        maxExp.addEventListener('touchstart', (e) => {
            maxExp.classList.add('active');
        });
        maxExp.addEventListener('touchend', (e) => {
            maxExp.classList.remove('active');
        });



        // --star rating--

        let minrating = document.getElementById('minrating');
        let maxRating = document.getElementById('maxrating');


        let rangeRating = document.querySelector('.slider2 > .range2');
        let ratingFrom = document.querySelector('.price-from2');
        let ratingTo = document.querySelector('.price-to2');

        function setMinRating() {
            let _this2 = minrating,
                min = parseInt(_this2.min),
                max = parseInt(_this2.max);
            _this2.value = Math.min(
                parseInt(_this2.value),
                parseInt(maxRating.value) - 0
            );
            ratingFrom.textContent = `Star: ${_this2.value}`;
            let percent1 = ((_this2.value - min) / (max - min)) * 100;
            rangeRating.style.left = `${percent1}%`;
        }
        setMinRating();

        function setMaxRating() {
            let _this2 = maxRating,
                min = parseInt(_this2.min),
                max = parseInt(_this2.max);

            _this2.value = Math.max(
                parseInt(_this2.value),
                parseInt(minrating.value) + 0
            );
            ratingTo.textContent = `Star: ${_this2.value}`;
            let percent1 = ((_this2.value - min) / (max - min)) * 100;
            rangeRating.style.right = `${100 - percent1}%`;
        }
        setMaxRating();
        minrating.addEventListener('input', setMinRating);
        maxRating.addEventListener('input', setMaxRating);


        minrating.addEventListener('mousemove', (e) => {
            minrating.classList.add('hover');
        });
        minrating.addEventListener('mouseout', (e) => {
            minrating.classList.remove('hover');
        });
        minrating.addEventListener('mousedown', (e) => {
            minrating.classList.add('active');
        });
        minrating.addEventListener('mouseup', (e) => {
            minrating.classList.remove('active');
        });
        minrating.addEventListener('touchstart', (e) => {
            minrating.classList.add('active');
        });
        minrating.addEventListener('touchend', (e) => {
            minrating.classList.remove('active');
        });
        maxRating.addEventListener('mouseover', (e) => {
            maxRating.classList.add('hover');
        });
        maxRating.addEventListener('mouseout', (e) => {
            maxRating.classList.remove('hover');
        });
        maxRating.addEventListener('mousedown', (e) => {
            maxRating.classList.add('active');
        });
        maxRating.addEventListener('mouseup', (e) => {
            maxRating.classList.remove('active');
        });
        maxRating.addEventListener('touchstart', (e) => {
            maxRating.classList.add('active');
        });
        maxRating.addEventListener('touchend', (e) => {
            maxRating.classList.remove('active');
        });
    </script>
    <script>
        function fetchSubjects() {

            var classId = $('#class option:selected').val();
            $("#subject").html('');
            $("#topic").html('');
            $.ajax({
                url: "{{ url('fetchsubjects') }}",
                type: "POST",
                data: {
                    class_id: classId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#subject').html('<option value="">-- Select Subject --</option>');
                    $.each(result.subjects, function(key, value) {
                        $("#subject").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });

                }

            });

        };
    </script>
    <script>
        function getTutorSubjects(id) {


            $.ajax({
                url: "{{ url('fetchtutorsubjects') }}",
                type: "POST",
                data: {
                    tutor_id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // $('#demosubjectid').html('<option value="">-- Select Subject --</option>');
                    $('#demosubjectid').html('<option value="">-- Select Subject --</option>');
                    $.each(result.subjects, function(key, value) {
                        $("#demosubjectid").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });

                }

            });

        };
    </script>

    {{-- Slot Availablity Starts here --}}
    <script>
        // At the beginning of your script
        let selectedSlotIds = [];

        // Load selectedSlotIds from local storage if available
        const storedSelectedSlotIds = sessionStorage.getItem('selectedSlotIds');
        if (storedSelectedSlotIds) {
            selectedSlotIds = JSON.parse(storedSelectedSlotIds);
        }

        // Function to convert 24-hour time to AM/PM format
        function convertToAmPm(timeString) {
            const convertedTime = new Date(`2000-01-01 ${timeString}`);
            return convertedTime.toLocaleString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });
        }

        function searchSlots() {
            const tutorid = document.getElementById('tutorenrollid').value;
            const selectedDate = document.getElementById('classdate').value;
            const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Get CSRF token from meta tag

            // Make an AJAX request to fetch available slots based on the selected date
            $.ajax({
                url: "{{ route('index.slots.search') }}",
                type: 'GET',
                data: {
                    date: selectedDate,
                    tutorid: tutorid
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken, // Include CSRF token in headers
                },
                success: function(response) {
                    // Assuming 'response' is an array of slot data
                    displayAvailableSlots(response);
                },
                error: function(error) {
                    console.error('Error fetching slot data:', error);
                }
            });
        }

        function displayAvailableSlots(slots) {
            const availableSlotsContainer = document.getElementById('availableSlots');
            availableSlotsContainer.innerHTML = '';

            if (slots.length === 0) {
                // Display an error message when no slots are found
                const errorMessage = document.createElement('div');
                errorMessage.className = 'error-message';
                errorMessage.innerHTML = 'No slots found';
                availableSlotsContainer.appendChild(errorMessage);
                return;
            }

            for (let i = 0; i < slots.length; i++) {
                const slot = document.createElement('div');
                slot.className = 'slot';
                slot.setAttribute('data-slot-id', slots[i].id); // Set slot ID as a data attribute
                slot.onclick = function() {
                    toggleSlot(this);
                };

                // Check if the slot is selected and stored in local storage
                if ((selectedSlotIds && selectedSlotIds.includes(slots[i].id.toString())) || (storedSelectedSlotIds &&
                        storedSelectedSlotIds.includes(slots[i].id.toString()))) {
                    slot.classList.add('selected');
                    slot.style.backgroundColor = 'blue'; // Set the background color to blue
                }


                // Customize the slot appearance based on the slot data
                if (slots[i].status === 0) {
                    // Available slot styling
                    slot.classList.add('available');
                } else {
                    // Booked slot styling
                    slot.classList.add('booked');
                }

                // Add slot information to the slot element
                const slotInfo = document.createElement('div');
                slotInfo.innerHTML = `${convertToAmPm(slots[i].slot)}`;
                slot.appendChild(slotInfo);

                availableSlotsContainer.appendChild(slot);
            }
        }

        function toggleSlot(slot) {
            // Check if the slot is already selected or disabled
            const slotId = slot.getAttribute('data-slot-id');

            // Check if the slot is not available (you can modify this condition based on your data)
            if (slot.classList.contains('booked') || slot.classList.contains('unavailable')) {
                // Slot is not available, return early
                return;
            }

            if (selectedSlotIds.includes(slotId)) {
                // Slot is already selected, deselect it
                const index = selectedSlotIds.indexOf(slotId);
                selectedSlotIds.splice(index, 1);
                slot.classList.remove('selected');
                slot.style.backgroundColor = ''; // Remove the background color
            } else if (!slot.hasAttribute('disabled')) {
                // Slot is not selected, select it
                selectedSlotIds.push(slotId);
                slot.classList.add('selected');
                slot.style.backgroundColor = 'blue'; // Set the background color to blue
            }
            valuechanged();
            // Retrieve selected slot IDs from local storage
            const storedSelectedSlotIds = JSON.parse(sessionStorage.getItem('selectedSlotIds')) || [];

            // Save selectedSlotIds to local storage
            sessionStorage.setItem('selectedSlotIds', JSON.stringify(selectedSlotIds));

            // Update the 'Selected Slot' input field
            const selectedSlotInput = document.getElementById('selectedSlot');
            selectedSlotInput.value = selectedSlotIds.map(id => convertToAmPm(id)).join(', ');

            // Attach an event listener to the form submission
            document.querySelector('form').addEventListener('submit', function(event) {
                // Before the form is submitted, update the hidden input with the latest selected slots from local storage
                const latestStoredSelectedSlotIds = JSON.parse(sessionStorage.getItem('selectedSlotIds')) || [];
                document.getElementById('selectedSlot').value = latestStoredSelectedSlotIds.join(', ');
                document.getElementById('selectedSlot').value = latestStoredSelectedSlotIds.join(', ');

                // Optionally, you can remove or clear the local storage after using the data
                // sessionStorage.removeItem('selectedSlotIds');
            });

            // // Update the 'Selected Slot' input field
            // const selectedSlotInput = document.getElementById('selectedSlot');
            // selectedSlotInput.value = selectedSlotIds.map(id => convertToAmPm(id)).join(', ');

            // // Save selectedSlotIds to local storage
            // sessionStorage.setItem('selectedSlotIds', JSON.stringify(selectedSlotIds));
        }

        // Function to handle the submission of selected slots to the API
        function submitSelectedSlots() {
            // Add your logic to send selectedSlotIds to the API
            console.log('Selected Slot IDs:', selectedSlotIds);

            // Clear selectedSlotIds after submission (optional)
            // sessionStorage.removeItem('selectedSlotIds');
        }

        function clearselectedslots() {
            // Clear selectedSlotIds in memory
            selectedSlotIds = [];

            // Clear selectedSlotIds in local storage
            sessionStorage.removeItem('selectedSlotIds');

            // Clear the 'Selected Slot' input field
            const selectedSlotInput = document.getElementById('selectedSlot');
            selectedSlotInput.value = '';

            // Optionally, you can also clear the slots in the UI
            const slots = document.querySelectorAll('.slot.selected');
            slots.forEach(slot => {
                slot.classList.remove('selected');
                slot.style.backgroundColor = ''; // Remove the background color
            });

            // Optionally, you can also update the UI or perform other actions related to clearing
            // ...

            console.log('Selected Slot IDs cleared');
        }
    </script>


    {{-- Slots availability ends here --}}

    <script>
        function checkslots(id) {
            document.getElementById('tutorslotid').value = id;
            $('#scheduleclassmodal').modal('show');
            searchSlots();
        }
    </script>



    <script>
        function checkslots(id) {

            document.getElementById('tutorslotid').value = id;

            $('#scheduleclassmodal').modal('show')
            searchSlots();
        }

        function confirmformdata() {

            var totalclasstaken = document.getElementById('requiredclassenroll').value;
            var totalclassavl = document.getElementById('availableclassenroll').value;
            var selectedSlotIds = JSON.parse(sessionStorage.getItem('selectedSlotIds')) || [];
            if (totalclasstaken == "") {
                alert('Required class should not be empty');
                return false;
            }
            if (totalclasstaken < 1) {
                alert('Required class should not be less than 1');
                return false;
            }
            if (totalclasstaken > totalclassavl) {
                alert('Required class should not be more than available class');
                return false;
            }



            document.getElementById('selectedSlotConfirmation').innerHTML = '';
            document.getElementById('selectedSlotConfirmation').innerHTML =
                `You have selected total ${selectedSlotIds.length} slots out of ${totalclasstaken}.`;
            if (selectedSlotIds.length > totalclasstaken) {
                alert('Selected slots should not more than purchase class');
                return false;
            }
            // Update the 'Selected Slot' input field
            document.getElementById('selectedSlotIds').value = selectedSlotIds.join(',');

            document.getElementById('formbuttons').innerHTML = '';
            document.getElementById('formbuttons').innerHTML =
                `<button class="btn btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-sm btn-success">Pay Now</button>`;
        }

        function valuechanged() {
            document.getElementById('formbuttons').innerHTML = '';
            document.getElementById('formbuttons').innerHTML =
                `<button class="btn btn-sm btn-danger" data-dismiss="modal">Cancel</button>
                                                                <button type="button" onclick="confirmformdata()" class="btn btn-sm btn-success">Confirm</button>`;
            document.getElementById('selectedSlotConfirmation').innerHTML = '';
        }
    </script>

@endsection
