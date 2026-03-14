@extends('student.layouts.main')
@section('main-section')
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            .headline h2,
            .headline p {
                display: flex;
                justify-content: start;
            }

            .skillTag {
                display: flex;

            }

            .skillTag h5 {
                margin: 10px;
            }
        </style>
        <link rel="stylesheet" href="{{ url('frontend/css/profile.css') }}">
        <main class="tu-main tu-bgmain">
            <section class="tu-main-section">
                <div class="container">
                    @if(Session::has('success'))
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ Session::get('success') }}',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    </script>
                @endif

                @if(Session::has('fail'))
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: '{{ Session::get('fail') }}',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    </script>
                @endif

                    <div class="container">
                        <div class="row gy-4">
                            <div class="col-xl-12 col-xxl-9">
                                <div class="tu-tutorprofilewrapp">
                                    <span class="tu-cardtag"></span>
                                    <div class="tu-profileview">
                                        <figure>
                                            {{-- <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorpd->profile_pic ?? url('images/avatar/default_avatar_img.jpg') }}"
                                                alt="image-description"> --}}
                                                <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorpd->profile_pic }}"
                                    alt="" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                        </figure>
                                        <div class="tu-protutorinfo">
                                            <div class="tu-protutordetail">
                                                <div class="tu-productorder-content">
                                                    <figure>
                                                        {{-- <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorpd->profile_pic ?? url('images/avatar/default_avatar_img.jpg') }}"
                                                            alt="images"> --}}
                                                            <img src="{{ url('images/tutors/profilepics', '/') }}{{ $tutorpd->profile_pic }}"
                                    alt="" onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                                                    </figure>
                                                    <div class="tu-product-title">
                                                        <h3>{{ $tutorpd->name }} <i
                                                                class="icon icon-check-circle tu-greenclr"
                                                                data-tippy-trigger="mouseenter"
                                                                data-tippy-html="#tu-verifed" data-tippy-interactive="true"
                                                                data-tippy-placement="top"></i></h3>
                                                                <div class="sub-btns">
                                                                    @foreach ($subjects as $subject)
                                                                        <button>{{ $subject->subject_name }}</button>
                                                                    @endforeach
                                                                </div>
                                                    </div>
                                                    <div class="tu-listinginfo_price">
                                                        <span>Starting from:</span>
                                                        <h4>£{{ $tutorpd->rate }}/hr</h4>
                                                    </div>
                                                </div>
                                                <ul class="tu-tutorreview">
                                                    <li>
                                                        <span><i class="fa fa-star tu-coloryellow">
                                                                <em>{{$tutorpd->avg_rating}}<span></span></em> </i>
                                                            <em>({{$tutorpd->total_reviews}})</em></span>
                                                    </li>
                                                    <li>
                                                        <span><i
                                                                class="fa fa-check-circle tu-colorgreen"><em>Qualification:</em></i><em>{{ $tutorpd->qualification }}</em></span>
                                                    </li>
                                                    <li>
                                                        <span><i
                                                                class="fa fa-check-circle tu-colorgreen"><em>Experience:</em></i><em>{{ $tutorpd->experience }}</em></span>
                                                    </li>
                                                </ul>
                                                <div class="tu-detailitem">
                                                    <h6>Certifications</h6>
                                                    <div class="tu-languagelist">
                                                        <ul class="tu-languages">
                                                            <li>{{ $tutorpd->certification }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tu-actionbts">
                                        <div class="tu-userurl">
                                            <a href="{{ $tutorpd->intro_video_link }}" target="_blank"><button
                                                    class="btn btn-danger">Watch Intro</button><i
                                                    class="icon icon-copy"></i></a>
                                        </div>
                                        @if (session('usertype') == 'Parent')
                                        @else
                                        <a href="#booktrial"> <button data-toggle="modal"
                                            data-target="#openDemoModal" class="btn btn-sm btn-primary"
                                            onclick="openDemoModal('{{ $tutorpd->tutor_id }}','{{ $tutorpd->name }}','{{ $tutorpd->subjectid }}','{{ $tutorpd->subject }}')">Trial
                                            Class</button></a>

                                            <a href="/student/enrollnow/{{ $tutorpd->tutor_id }}"> <button
                                                class="btn btn-sm btn-success" id="enrollnow">Book
                                                Classes</button></a>
@endif
                                        <a href="/student/tutormessages/{{ $tutorpd->tutor_id }}"><button
                                                class="btn btn-sm btn-success">Start Chat</button></a>
                                        {{-- <ul class="tu-profilelinksbtn">
                                    <li>
                                        <a class="tu-linkheart" href="javascript:void(0);"><i class="icon icon-heart"></i><span>Save</span></a>
                                    </li>
                                    <li><a href="/student/searchtutor" class="tu-secbtn">Let’s Book Trial</a></li>
                                    <li>
                                        <a href="/student/searchtutor" class="tu-primbtn">Book a tution</a>
                                    </li>
                                </ul> --}}
                                    </div>
                                </div>
                                <div class="tu-detailstabs">
                                    <ul class="nav nav-tabs tu-nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                                data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                                aria-selected="true"><i
                                                    class="icon icon-home"></i><span>Introduction</span></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#profile" type="button" role="tab"
                                                aria-controls="profile" aria-selected="false"><i
                                                    class="icon icon-message-circle"></i><span>Reviews</span></button>
                                        </li>
                                    </ul>
                                    <div class="tab-content tu-tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                                            aria-labelledby="home-tab">
                                            <div class="tu-tabswrapper">
                                                <div class="tu-tabstitle">
                                                    <h4>{{ $tutorpd->headline }}</h4>
                                                </div>
                                               <br>
                                                <hr>
                                                <div class="tu-tabstitle">
                                                    <h4>About {{$tutorpd->name}}</h4>
                                                </div>
                                                <div class="tu-description">
                                                    <p> {{ $tutorpd->goal }}</p>
                                                </div><br>
                                                <hr>
                                                <div class="tu-tabstitle">
                                                    <h4>Qualification</h4>
                                                </div>
                                                <div class="tu-description">
                                                    <p> {{ $tutorpd->qualification }}</p>
                                                </div><br>
                                                <hr>
                                                <div class="tu-tabstitle">
                                                    <h4>Experience</h4>
                                                </div>
                                                <div class="tu-description">
                                                    <p> {{ $tutorpd->experience }}</p>
                                                </div>


                                            </div>

                                            <div class="tu-tabswrapper">
                                                <div class="tu-tabstitle">
                                                    <h4>Achievement</h4>
                                                </div>
                                                <ul class="tu-icanteach">
                                                    <li>
                                                        {{-- <div class="tu-tech-title">
                                                    <h6>Class 9 - 10</h6>
                                                </div> --}}
                                                        <ul class="tu-serviceslist">
                                                            @foreach ($achievement as $achievement)
                                                                {{-- <tr> --}}
                                                                {{-- <td class="text-wrap">{{ $achievement->name }}</td> --}}
                                                                {{-- <td class="text-wrap">{{ $achievement->description }}</td> --}}
                                                                {{-- <td class="text-wrap">{{$achievement->date}}</td> --}}
                                                                {{-- <td>{{ \Carbon\Carbon::parse($achievement->date)->format('j-F-Y') }}</td> --}}
                                                                </tr>



                                                                <li>
                                                                    <a href="#achievement">{{ $achievement->name }}</a>
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </li>

                                                </ul>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="profile" role="tabpanel"
                                            aria-labelledby="profile-tab">
                                            <div class="tu-tabswrapper">
                                                <div class="tu-boxtitle">
                                                    <h4>Reviews ({{ count($reviews) }})</h4>
                                                </div>
                                                @foreach ($reviews as $reviews)
                                                    </tr>

                                                    <div class="tu-commentarea">
                                                        <div class="tu-commentlist">
                                                            <figure>
                                                                <img src="{{ url('images/students/profilepics', '/') }}{{ $reviews->student_pic ?? url('images/avatar/default_avatar_img.jpg') }}"
                                                                    alt="images">
                                                            </figure>
                                                            <div class="tu-coomentareaauth">
                                                                <div class="tu-commentright">
                                                                    <div class="tu-commentauthor">
                                                                        <h6><span>{{ $reviews->student_name }}</span></h6>
                                                                        <div class="tu-listing-location tu-ratingstars">
                                                                            <span>{{ $reviews->ratings }}</span>
                                                                            <span class="tu-stars tu-sm-stars">
                                                                                <span></span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tu-description">
                                                                    <p>{{ $reviews->name }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="tu-Joincommunity">
                                    <div class="tu-particles">
                                        <div id="tu-particlev2"></div>
                                    </div>
                                    <div class="tu-Joincommunity_content">
                                        <h4>Trending tutor directory of 2023</h4>
                                        <p>Its Free, Join today and start spreading knowledge with students out there</p>
                                    </div>
                                    <div class="tu-Joincommunity_btn">
                                        <a href="/student/register" class="tu-yellowbtn">Join our community</a>
                                    </div>
                                </div> --}}

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
                                </header>
                                <i style="color: red">*Please provide your preferred time slots for the trial class. Once confirmed by the tutor, be punctual and join at the specified time.</i>

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
                                            {{-- <input type="hidden" id="demosubjectid" name="demosubjectid">
                                            <input type="text" class="form-control" id="demosubjectname"
                                                name="demosubjectname" disabled> --}}
                                            <select class="form-control" id="demosubjectid" name="demosubjectid" required></select>
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
                <!-- content-wrapper ends -->

                <script>
                    function openDemoModal(tid, tname, sid, sname) {
                        getTutorSubjects(tid)
                        $('#demotutorid').val(tid)
                        // $('#demotutorname').val(tname)
                        // $('#demosubjectid').val(sid)
                        $('#demosubjectname').val(sname)
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
