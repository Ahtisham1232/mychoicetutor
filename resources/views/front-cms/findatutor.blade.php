@extends('front-cms.layouts.main')
@section('main-section')
    <style>
        .input-group-text {
            height: 26px;
        }
    </style>
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader ">
                <h1 class="findtutorHeader">
                    Discover the perfect tutor for you
                </h1>
                <form action="{{ url('toptutorsearch') }}" method="POST">
                    @csrf
                    <div class="findtutor-btns">
                        <div class="custom-select" style="width:300px;">
                            <select id="subject" name="subject">
                                <option value="">Select a Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ isset($subjectid) && $subjectid == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="custom-select" style="width:300px;">
                            <select id="grade" name="grade">
                                <option value="">Select Grade</option>
                                @foreach ($gradelists as $grade)
                                    <option value="{{ $grade->id }}"
                                        {{ isset($classid) && $classid == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="drpdwnSearch">
                            <button type="submit" class="btn search-tutor">Search</button>
                            <a href="{{ url('findatutor') }}" class="btn search-tutor">
                                Reset Filters
                            </a>
                        </div>
                    </div>
                </form>


                <div id="accordion">

                    <div class="accor">
                        <div class="advceAccordian">
                            <div class="" id="headingTwo">

                                <div class="advance-search">
                                    <a href="javascript:void(0)" class="collapsed advSearTextLeft" data-toggle="collapse"
                                        data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Find
                                        the tutor of your choice use advance
                                        search</a>
                                    <span>
                                        <a href="javascript:void(0)" class="collapsed advSearch2" data-toggle="collapse"
                                            data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <img src="{{ url('frontendnew/img/icons/magnifire.png') }}" alt="">
                                            Advance Search
                                        </a>
                                    </span>
                                </div>

                            </div>
                            <div id="collapseTwo" class="collapse collapseAdvSearch" aria-labelledby="headingTwo"
                                data-parent="#accordion">
                                <form class="advSearchForm" action="{{ url('advancesearch') }}" method="POST">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                                            <div class="form-group">
                                                <label for="name">Tutor Name</label>
                                                <input type="text" class="form-control" aria-describedby=""
                                                    placeholder="Search" id="name" name="name" maxlength="100"
                                                    value="{{ $tutorname ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                            <label for="">Subject</label>
                                            <select class="form-control" id="subject" name="subject">
                                                <option value="">Select a subject</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ isset($subjectid) && $subjectid == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                            <label for="">Grade</label>
                                            <select class="form-control" id="grade" name="grade">
                                                <option value="">Select a grade</option>
                                                @foreach ($gradelists as $grade)
                                                    <option value="{{ $grade->id }}"
                                                        {{ isset($classid) && $classid == $grade->id ? 'selected' : '' }}>
                                                        {{ $grade->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                            <label for="tminprice">Min Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text">{{ config('common.currency.symbol') }}</span>
                                                </div>
                                                <input type="text" class="form-control" id="tminprice" name="tminprice"
                                                    placeholder="0.00" value="{{ $minPrice ?? '' }}">
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
                                            <label for="tmaxprice">Max Price</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span
                                                        class="input-group-text">{{ config('common.currency.symbol') }}</span>
                                                </div>
                                                <input type="text" class="form-control" id="tmaxprice" name="tmaxprice"
                                                    placeholder="0.00" value="{{ $maxPrice ?? '' }}">

                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">


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
                <br>
                <br>
                <br>

            </div>
        </div>

    </section>
    <!-- tutor section -->
    <section class="findtutSecs mar-top-40">
        <div class="container tutor-card">
            <h4>Explore our evaluated private tutors</h4>
            <br>
            <div class="row">
                @forelse ($tutors as $tutor)
                    <a href="tutor-details/{{ $tutor->tutor_id }}" style="color: black">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 tutorCol mb-5">
                            <div class="tutorDetails padd-50">
                                <div class="tutorImg">
                                    <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutor->profile_pic }}"
                                        width="100%" alt=""
                                        onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                </div>
                                <div class="star">
                                    <span>
                                        <i class="fa fa-star"></i>
                                        {{ $tutor->avg_rating }} ({{ $tutor->total_reviews }})
                                    </span>
                                    <span>&#163; {{ $tutor->rateperhour }}/h</span>
                                </div>
                                <a href="tutor-details/{{ $tutor->tutor_id }}" style="color: black;line-height: 0px;">
                                    <span class="name">{{ $tutor->name }}</span>
                                </a>
                                <p style="line-height: 14px;">{{ $tutor->subject }}</p>
                                <p class="desc-tutor" style="font-weight: 400;line-height: 14px;font-size: 13px;">
                                    {{ strlen($tutor->headline) > 100 ? substr($tutor->headline, 0, 100) . '...' : $tutor->headline }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-12 mb-5">
                        <div class="text-center py-5 border rounded bg-light">
                            <div class="mb-3">
                                <i class="fa fa-search fa-3x text-secondary"></i>
                            </div>
                            <h5 class="fw-bold">No Results Found</h5>
                            <p class="text-muted mb-3">
                                We couldn't find any tutors matching your search.
                            </p>
                            <p class="mb-0">
                                Try changing the <strong>subject, grade, name, or price range</strong> and search again.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
    <section>
        <div class="container">
            <div class="tutor-banner bottom-banner1 ">

                <div class="rightside">
                    <h2>Experience our free trial classes today!</h2>
                    <a href="{{route('std_tutor_registration')}}"><button>Book free trial class today</button></a>
                </div>

            </div>
        </div>

        <script src="{{ asset('js/tutor-filter.js') }}"></script>

    </section>
@endsection
