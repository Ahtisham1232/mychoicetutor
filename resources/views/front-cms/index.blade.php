@extends('front-cms.layouts.main')
@section('main-section')
<!-- END header -->
 <style>
 .subject-list {
    display: flex;
    flex-wrap: nowrap;
    gap: 15px;                 /* smaller gap by default */
    list-style: none;
    padding: 10px;
    margin: 0;
    overflow-x: auto;          /* scroll on small screens */
    scrollbar-width: thin;
}

.subject-list::-webkit-scrollbar {
    height: 6px;               /* slim scrollbar */
}
.subject-list::-webkit-scrollbar-thumb {
    background: #ccc; 
    border-radius: 3px;
}
.subject-list::-webkit-scrollbar-track {
    background: transparent;
}

.subject-list li {
    flex: 0 0 auto;
    text-align: center;
}
.subject-btn {
    background: none;
    border: none;
    padding: 10px;
    cursor: pointer;
    outline: none;
    transition: transform 0.2s ease, color 0.2s ease;
}

.subject-btn:focus,
.subject-btn:active {
    outline: none;
    box-shadow: none;
}

.subject-btn img {
    max-width: 50px;
    margin-bottom: 6px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.subject-btn span {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #333;
}

/* Hover effect for desktop */
.subject-btn:hover span {
    color: #7b3fe4; /* purple accent */
}
.subject-btn:hover img {
    transform: scale(1.1);
}
.subSec ul li {
    margin: 0px;
}
@media (min-width: 1200px) {
    .subject-list {
        justify-content: center; /* center in container */
        overflow-x: visible;     /* disable scroll */
        gap: 0;                  /* remove extra gap */
    }

    .subject-list li {
        margin: 0 8px;           /* small margin instead */
    }

    .containerSubject {
        max-width: 1290px;
    }
}

.input-group-text{
    height: 26px;
}


 </style>
<section class="bannerSec">
    <div class="container-fluid">
        <div class="bannerImg ">
            <div class="bannerBGImg">
                <img class="desktopBanner" src="{{ url('frontendnew/img/bg-mtc.png') }}" alt="" width="100%">
                <img class="mobileBanner" src="{{ url('frontendnew/img/MobBan.png') }}" alt="" width="100%">
                <img class="tabBanner" src="{{ url('frontendnew/img/bannerIpad.png') }}" alt="" width="100%">
                <img class="tabBanner2" src="{{ url('frontendnew/img/ipad2.png') }}" alt="" width="100%">
            </div>
            <div class="overlayMTC">
                <div class="tutorHeader">
                    <h1>
                        Discover the perfect tutor for you
                    </h1>
                    <form action="{{ url('toptutorsearch') }}" method="POST">
                        @csrf
                        <div class="findtutor-btns">

                            <div class="custom-select" style="width:300px;" class="dropdown-menu">
                                <select id="subject"  name="subject" >
                                    <option value="">Select a Subject </option>
                                    @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="custom-select" style="width:300px;">
                                <select id="grade" name="grade">
                                    <option value="">Select Grade</option>
                                    @foreach ($gradelists as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="drpdwnSearch">
                                <button type="submit" class="btn search-tutor">Search</button>
                            </div>
                        </div>
                    </form>



                    <div id="accordion">

                        <div class="accor">
                            <div class="advceAccordian">
                                <div class="" id="headingTwo">
                                    <div class="advance-search">
                                        <a href="javascript:void(0)" class="collapsed advSearTextLeft" data-toggle="collapse"
                                            data-target="#collapseTwo" aria-expanded="false"
                                            aria-controls="collapseTwo">Find the tutor of your choice use advance
                                            search</a>
                                        <span>
                                            <a href="javascript:void(0)" class="collapsed advSearch2" data-toggle="collapse"
                                                data-target="#collapseTwo" aria-expanded="false"
                                                aria-controls="collapseTwo">
                                                <img src="{{ url('frontendnew/img/icons/magnifire.png') }}" alt="">
                                                Advance Search
                                            </a>
                                        </span>
                                    </div>

                                </div>
                                <div id="collapseTwo" class="collapse collapseAdvSearch" aria-labelledby="headingTwo"
                                    data-parent="#accordion">
                                    <form class="advSearchForm" action="{{url('advancesearch')}}" method="POST">
                                        @csrf

                                        <div class="row mb-3">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                                                    <label for="name">Tutor Name</label>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" aria-describedby=""
                                                        placeholder="Search" id="name" name="name" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                                <label for="">Subject</label>
                                                <select class="form-control" id="adv_subject" name="subject">
                                                    <option value="">Select a subject</option>
                                                    @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                                <label for="">Grade</label>
                                                <select class="form-control" id="adv_grade" name="grade">
                                                    <option value="">Select a grade</option>
                                                    @foreach ($gradelists as $grade)
                                                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
     
                                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                                <label for="tminprice">Min Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{config('common.currency.symbol')}}</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="tminprice" name="tminprice" placeholder="0.00">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                                <label for="tmaxprice">Max Price</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">{{config('common.currency.symbol')}}</span>
                                                    </div>
                                                    <input type="text" class="form-control" id="tmaxprice" name="tmaxprice" placeholder="0.00">
                                                </div>
                                            </div>
                                           
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="advSearchBtns">
                                                    <button type="button" class="btn cancelBtn">Cancel</button>
                                                    <button type="submit" class="applyBtn">Apply</button>
                                                </div>
                                            </div>

                                        </div>


                                    </form>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>
              
                    <div class="container subjContainer containerSubject">
                        <div class="subSec">
                            <ul class="subject-list">

                                @php
                                    $subjects = App\Helpers\CommonHelper::getPopularSubjects();
                                @endphp

                                @foreach($subjects as $subject)
                                <li>
                                    <form action="{{ url('toptutorsearch') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="subject" value="{{ $subject->id }}">

                                        <button type="submit" class="subject-btn">
                                            <img src="{{ url('frontendnew/img/icons/physics.png') }}" alt="{{ $subject->name }}">
                                            <span>{{ \Illuminate\Support\Str::limit($subject->name, 15, '...') }}</span>
                                        </button>
                                    </form>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- tutor section -->
<section class=" mar-top-40">
    <div class="container tutor-card padd-80">
        <h4>Explore our evaluated private tutors</h4>
        <br>
        <div class="row">
             @foreach ($tutors->slice(0, 12) as $tutor)
            <a href="tutor-details/{{$tutor->tutor_id}}" style="color: black">
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 col-xs-12 tutorCol">
                    <div class="tutorDetails">
                        <div class="tutorImg">
                            <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutor->profile_pic }}" width="100%" alt="" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                        </div>
                        <div class="star">
                            <span>
                                <i class="fa fa-star"></i>
                                {{ $tutor->avg_rating }} ({{ $tutor->total_reviews }})
                            </span>
                            <span>&#163; {{ $tutor->rateperhour }}/h</span>
                        </div>
                        <a href="tutor-details/{{$tutor->tutor_id}}" style="color: black;line-height: 0px;"> 
                            <span class="name">
                                {{ $tutor->name }}
                              
                            </span>
                        </a> 

                        <p style="line-height: 14px;">{{ $tutor->subject }}</p>
                        <p class="desc-tutor" style="font-weight: 400;line-height: 14px;font-size: 13px;">
                                {{ strlen($tutor->headline) > 100 ? substr($tutor->headline, 0, 100) . '...' : $tutor->headline }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach

        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="expMore">
                    <a href="findatutor" class="btn btn-lg mb-4">View ALL</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- -----------testimonial---------- -->
<section class="testimonial-sec">
    <div class="container topheader">
        <h3 class="">Customer Testimonials</h3>
        <div class="row">
            @foreach ($reviews->slice(0, 4) as $review)
            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-4">
                <div class="testi-card">
                    <span class="nameTo">
                        {{ $review->tutorname }}
                        <p>{{ $review->subjectname }}
                            <br>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                        </p>
                    </span>
                    <p class="mt-4">“{{ $review->name }}”</p>
                    <p class="nameFrom">{{ $review->studentname }}</p>
                </div>
            </div>
            @endforeach

        </div>
        <div class="row mt-4">
            <div class="col-12 ">
                <div class="expMore">
                    <a href="/reviews" class="btn btn-lg">View all</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ----------how it works----- -->
<section class="howitwork-sec">
    <div class="container topheader">
        <h3 class="">How it works</h3>
        <p class="text-center px-5 mb-5">Experience seamless learning with our online tuition app. We've simplified
            education, making
            it easy for students, parents, and tutors to connect and excel. Effortless, effective, and engaging learning
            awaits you.</p>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="how-card card1">
                    <span class="nameTo">Find the best tutor</span>
                    <p class="mt-4 pb-5">MCT offers a selection of in-house trained tutors to elevate your academic
                        career...</p>
                </div>
                <div class="imgNumber">
                    <img class="shaddow" src="{{ url('frontendnew/img/icons/Vector 1.png') }}" alt="">
                    <img class="numbr1" src="{{ url('frontendnew/img/icons/one.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="how-card card2">
                    <span class="nameTo">Find the best tutor</span>
                    <p class="mt-4 pb-5">MCT offers a selection of in-house trained tutors to elevate your academic
                        career...</p>
                </div>
                <div class="imgNumber">
                    <img class="shaddow" src="{{ url('frontendnew/img/icons/Vector 2.png') }}" alt="">
                    <img class="numbr2" src="{{ url('frontendnew/img/icons/two.png') }}" alt="">
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                <div class="how-card card3">
                    <span class="nameTo">Find the best tutor</span>
                    <p class="mt-4 pb-5">MCT offers a selection of in-house trained tutors to elevate your academic
                        career...</p>
                </div>
                <div class="imgNumber">
                    <img class="shaddow" src="{{ url('frontendnew/img/icons/Vector 3.png') }}" alt="">
                    <img class="numbr3" src="{{ url('frontendnew/img/icons/three.png') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- fact sector -->
<section>
    <div class="tu-statsholder mb-5">
        <div class="container">


            <div id="tu-counter" class="tu-stats">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-5">

                        <img src="{{ url('frontendnew/img/icons/img-01.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="50" data-speed="8000" data-refresh-interval="10">50</span>+
                                Courses
                            </h4>
                            <p>Courses available from verified and top tutors</p>
                        </div>

                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-5">


                        <img src="{{ url('frontendnew/img/icons/img-02.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="100" data-speed="8000"
                                    data-refresh-interval="30">100</span>+ Quizes
                            </h4>
                            <p>Online quiz & online tests to improve your skills</p>
                        </div>

                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-5">
                        <img src="{{ url('frontendnew/img/icons/img-03.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="10" data-speed="8000" data-refresh-interval="50">10</span>+
                                Hours
                            </h4>
                            <p>User daily average time spent on the platform</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-5">

                        <img src="{{ url('frontendnew/img/icons/img-04.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="1000" data-speed="8000"
                                    data-refresh-interval="100">1000</span>+ Users
                            </h4>
                            <p>Active instructor and students available on the platform</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <ul id="tu-counter" class="tu-stats">
                    <li>
                        <img src="{{ url('frontendnew/img/icons/img-01.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="50" data-speed="8000"
                                    data-refresh-interval="10">50</span>+ Courses
                            </h4>
                            <p>Courses available from verified and top tutors</p>
                        </div>
                    </li>
                    <li>
                        <img src="{{ url('frontendnew/img/icons/img-02.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="100" data-speed="8000"
                                    data-refresh-interval="30">100</span>+ Quizes
                            </h4>
                            <p>Online quiz & online tests to improve your skills</p>
                        </div>
                    </li>
                    <li>
                        <img src="{{ url('frontendnew/img/icons/img-03.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="10" data-speed="8000"
                                    data-refresh-interval="50">10</span>+ Hours
                            </h4>
                            <p>User daily average time spent on the platform</p>
                        </div>
                    </li>
                    <li>
                        <img src="{{ url('frontendnew/img/icons/img-04.png') }}" alt="img">
                        <div class="tu-stats_info">
                            <h4>
                                <span data-from="0" data-to="1000" data-speed="8000"
                                    data-refresh-interval="100">1000</span>+ Users
                            </h4>
                            <p>Active instructor and students available on the platform</p>
                        </div>
                    </li>
                </ul> -->
        </div>
    </div>
</section>
<!-- fact sector END -->


<section>
    <div class="container">
        <div class="tutor-banner bottom-banner2">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="trial">
                        <h2>Begin your tutoring journey now!<br> Join us as a tutor.</h2>
                        <div class="trialBtn">
                            <button onclick="redirect();">Get Started</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

     </div>
</section>

  <script src="{{ asset('js/tutor-filter.js') }}"></script>

@endsection
