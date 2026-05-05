@extends('front-cms.layouts.main')
@section('main-section')
    <!-- tutor section -->

    <section class="tutor-details">
        <div class="container tutor-card topheader-tutor">

            <div class="row">

                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12 profileSec1">
                    <h2>{{ $tutorpd->headline ?? '' }}</h2>


                    @if (isset($subjects) && $subjects->count() > 0)
                        <h6 class="mb-2">Subjects</h6>

                        <div class="sub-btns">
                            @foreach ($subjects as $subject)
                                <button class="my-1">{{ $subject->subject_name }}</button>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($tutorpd->goal) && !empty($tutorpd->detail_1))
                        <div class="aboutTutor">

                            <h5>About {{ $tutorpd->name ?? '' }}</h5>
                            <p class="charcol">{{ $tutorpd->goal ?? '' }}</p>
                            <p class="charcol">{{ $tutorpd->detail_1 ?? '' }}</p>

                        </div>
                    @endif

                    @if (!empty(trim($tutorpd->qualification)) && isset($tutorpd->qualification))
                        <div class="aboutTutor">

                            <h5>Qualification</h5>
                            <p class="charcol">{{ $tutorpd->qualification ?? '' }}</p>

                        </div>
                    @endif

                    @if (!empty($tutorpd->certification))
                        <div class="aboutTutor">

                            <h5>Certification</h5>
                            <p class="charcol">{{ $tutorpd->certification ?? '' }}</p>

                        </div>
                    @endif


                    @if (isset($reviews) && $reviews->count() > 0)
                        <div class="aboutTutor review-top">
                            <h5>Recent Review</h5>
                            <div class="star">
                                <span>
                                    {{-- Loop to display the average number of stars --}}
                                    @if (isset($averagereview->avg_rating))
                                        @for ($i = 1; $i <= round($averagereview->avg_rating); $i++)
                                            <i class="fa fa-star"></i>
                                        @endfor
                                    @else
                                        {{-- Display no stars if no average rating is available --}}
                                        <i class="fa fa-star"></i>
                                    @endif

                                    {{-- Show the average rating and review count --}}
                                    <span class="ml-2">
                                        {{ $averagereview->avg_rating ?? '0' }}
                                        ({{ $averagecount ?? '0' }} reviews)
                                    </span>
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        @if (isset($reviews))
                            @foreach ($reviews->take(10) as $review)
                                <div class="col-12 mb-4">
                                    <div class="review-text" style="padding: 20px !important">
                                        <div class="review-top">
                                            <h6>{{ $review->student_name }}</h6>
                                            <div class="star">
                                                <span>
                                                    {{-- Loop to display the number of stars based on rating --}}
                                                    @for ($i = 1; $i <= $review->ratings; $i++)
                                                        <i class="fa fa-star"></i>
                                                    @endfor
                                                    {{-- Display the numeric value of the rating --}}
                                                    <span class="ml-2">{{ $review->ratings }}</span>
                                                </span>
                                            </div>
                                        </div>
                                        <p class="charcol font-14">{{ $review->name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>


                    {{-- <div class="aboutTutor">
                        <h5>Introduction Video</h5>

                        <div class="tutor-Video">
                            <iframe width="100%" height="400" src="{{ $tutorpd->intro_video_link ?? '' }}"
                                title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                        </div>
                    </div> --}}
                    @php
                        $videoUrl = $tutorpd->intro_video_link ?? '';

                        // Remove query params
                        $videoUrl = explode('?', $videoUrl)[0];

                        // Extract video ID safely
                        if (str_contains($videoUrl, 'youtube.com/watch')) {
                            parse_str(parse_url($tutorpd->intro_video_link, PHP_URL_QUERY), $params);
                            $videoId = $params['v'] ?? '';
                        } elseif (str_contains($videoUrl, 'youtu.be/')) {
                            $videoId = basename($videoUrl);
                        } elseif (str_contains($videoUrl, 'shorts/')) {
                            $videoId = basename($videoUrl);
                        } else {
                            $videoId = '';
                        }

                        $videoUrl = $videoId ? "https://www.youtube.com/embed/$videoId" : '';
                    @endphp
                    {{-- <p>{{ $tutorpd->intro_video_link }}</p><br>
                    <p>{{ $videoUrl }}</p> --}}
                    @if (!empty($videoUrl))
                        <div class="aboutTutor">
                            <h5>Introduction Viasdfsadsddeo</h5>

                            <div class="tutor-Video">
                                <iframe width="100%" height="400" src="{{ $videoUrl }}" frameborder="0"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    @endif

                    <br>
                    {{-- @if (isset($tutorpd->keywords) && !empty($tutorpd->keywords))
                        <h6 class="mb-2">Skills</h6>

                        <div class="sub-btns">
                            @foreach (explode(',', $tutorpd->keywords) as $keyword)
                            @dump($keyword)
                                <button class="mb-2">{{ trim($keyword) }}</button>
                            @endforeach
                        </div>
                    @endif --}}
                    @if (isset($tutorpd->keywords) && !empty($tutorpd->keywords))
                        <h6 class="mb-2">Skills</h6>

                        <div class="sub-btns">
                            @foreach (array_filter(explode(',', $tutorpd->keywords)) as $keyword)
                                @php
                                    $cleanKeyword = trim($keyword);
                                    $cleanKeyword = trim($cleanKeyword, '"'); // remove quotes
                                @endphp

                                @if ($cleanKeyword !== '')
                                    <button class="mb-2">{{ $cleanKeyword }}</button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                    <div class="tutorDetails tutorProfPic mt-5" style="padding: 30px !important">
                        <div class="row">
                            <div class="col-lg-12 col-md-5">
                                @if (isset($tutorpd->profile_pic))
                                    <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorpd->profile_pic ?? '' }}"
                                        width="100%" alt=""
                                        onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                @endif
                            </div>

                        </div>
                        <div class="col-lg-12 col-md-7">

                            <p class="name mt-3" style="line-height: 27px">{{ $tutorpd->name ?? '' }}</p>
                            <div class="star">
                                <span>
                                    {{-- Loop to display the average number of stars --}}
                                    @if (isset($averagereview->avg_rating))
                                        @for ($i = 1; $i <= round($averagereview->avg_rating); $i++)
                                            <i class="fa fa-star"></i>
                                        @endfor
                                    @else
                                        {{-- Display no stars if no average rating is available --}}
                                        <i class="fa fa-star"></i>
                                    @endif

                                    {{-- Show the average rating and review count --}}
                                    <span class="ml-2">
                                        {{ $averagereview->avg_rating ?? '0' }}
                                        ({{ $averagecount ?? '0' }} reviews)
                                    </span>
                                </span>
                            </div>


                            <table class="priceTable">


                                <tr>
                                    <td>Hourly rate:</td>
                                    <td>£{{ $tutorpd->rateperhour ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td>Experience:</td>
                                    <td>{{ $tutorpd->experience ?? '' }}</td>
                                </tr>

                            </table>

                            <div class="tabView">
                                <div class="freeTrial btnSize ">
                                    @if (isset($tutorpd->tutor_id))
                                        @php
                                            $tutorId = $tutorpd->tutor_id;
                                            $userObj = session('userid');
                                            $userType = session('usertype');
                                            $roleId = $userObj->role_id ?? null;

                                            $isStudentOrParent =
                                                ($userType === 'Student' || $userType === 'Parent')
                                                || ($roleId === 3 || $roleId === 4);
                                            $isTutor = ($userType === 'Tutor') || ($roleId === 2);

                                            $tutorProfileUrl = route('student.tutorprofile', $tutorId);
                                        @endphp

                                        @if(!$isTutor)
                                            <a href="{{ $tutorProfileUrl }}"
                                               class="btn require-login-for-tutorprofile"
                                               data-redirect="{{ $tutorProfileUrl }}">Free Trial Class</a>
                                        @endif
                                    @endif
                                </div>
                                <div></div>

                                <div class="expMore btnSize">
                                    @if (isset($tutorpd->tutor_id))
                                        @php
                                            // $tutorProfileUrl already set above when tutor_id exists
                                        @endphp
                                        @if(!$isTutor)
                                            <a href="{{ $tutorProfileUrl }}"
                                               class="btn require-login-for-tutorprofile"
                                               data-redirect="{{ $tutorProfileUrl }}">Book Classes</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>


        @if (isset($othertutors) && $othertutors->count() > 0)

            <div class="container tutor-card">

                <div class="aboutTutor">
                    <h5 class="my-5">More tutors in {{ $primarysubjects->subject_name ?? '' }}</h5>
                </div>

                <div class="row">
                    @if (isset($othertutors))
                        @foreach ($othertutors->slice(0, 4) as $othertutor)
                            <a href="/tutor-details/{{ $othertutor->tutor_id }}" style="color: black">
                                <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 tutorCol">
                                    <div class="tutorDetails padd-200">
                                        <div class="tutorImg">
                                            @if (isset($othertutor->profile_pic))
                                                <img src="{{ url('images/tutors/profilepics', '/') }}{{ $othertutor->profile_pic }}"
                                                    width="100%" alt=""
                                                    onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                            @endif
                                        </div>
                                        <div class="star">
                                            <span>
                                                <i class="fa fa-star"></i>
                                                {{ $othertutor->avg_rating }} ({{ $othertutor->total_reviews ?? '' }})
                                            </span>
                                            <span>£{{ $othertutor->rateperhour ?? '' }}/h</span>
                                        </div>
                                        <a href="/findatutor" style="color: black"> <span class="name">
                                                {{ $othertutor->name }}
                                                <p>{{ $primarysubjects->subject_name ?? '' }} teacher</p>
                                            </span></a>

                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>

            </div>
        @endif

    </section>
@endsection
