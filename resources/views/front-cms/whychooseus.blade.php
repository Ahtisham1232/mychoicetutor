@extends('front-cms.layouts.main')
@section('main-section')
<section class="bannerSec tutBann">
    <div class="container-fluid">
        <div class="tutorHeader">
            <h1 class="mb-5">
                Exceptional tutoring at <br>an affordable price
            </h1>

            <div class="text-center">
                <div class="abc">
                    <div class="flex-container">
                        <div class="charcol">One free trial class<span class="dash px-2">- </span></div>
                        <div class="charcol">One-on-one lessons<span class="dash px-2">- </span></div>
                        <div class="charcol">Flexible scheduling<span class="dash px-2">-</span></div>
                        <div class="charcol">Lecture Recording</div>
                    </div>
                </div>
                <a href="{{route('std_tutor_registration')}}"> <button class="btn search-tutor mt-4">Book free trial class today</button></a>
            </div>

        </div>
    </div>
</section>
<!-- tutor section -->
<section class="mt-5">
    <div class="container ">

        <div class="whyChooseUsouter">
            <div class="whyChooseUsTop">
                <div class="row ">
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="whyChooseUsCol">
                            <h3>Why Choose Us?</h3>
                            <p class="charcol">My Choice Tutor is designed to connect students with qualified and dedicated tutors who can guide them towards academic success. We focus on flexibility, personalized learning, and reliable support, ensuring that every student gets the right tutor to match their learning needs, schedule, and study goals.
                            </p>

                        </div>

                    </div>

                    <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12 getstartedBtnPosition">
                        {{-- <div class="whyChooseUsCol">
                            <a href="{{route('std_tutor_registration')}}"> <button>Get started</button></a>
                        </div> --}}
                    </div>

                    <!-- <div class="Bg_purple1">
                        <div class="p-5">
                        <h3>Why choose us?</h3>

                        <p class="charcol">Education is the best investment one can make in their children’s lives. It’s
                            crucial to be well-informed before selecting tutors and the type of tutoring they receive.
                        </p>
                        <a href="findatutor">    <button class="btn search-tutor">Book now</button></a>

                        </div>
                        
                        <img src="{{ url('frontendnew/img/whychoose1.png') }}" width="100%" alt="">
                    </div> -->


                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 order1"  >

                <div class="Bg_purple">
                    <div class="p-5">
                        <h3>One free trial Class</h3>

                        <p>MCT offers the option to book a free trial class before committing to a tutor. This
                            allows you to familiarize yourself with the tutor’s teaching style. Students typically
                            spend this free trial class planning lessons, identifying weak areas to address, and
                            discussing various strategies to improve grades in any subject.</p>
                        <a href="{{route('std_tutor_registration')}}"> <button class="btn search-tutor">Book now</button></a>

                    </div>

                    <img src="{{ url('frontendnew/img/OnefretrialClass.png') }}" width="100%" alt="">
                </div>

            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 order2">

                <div class="row">
                    <div class="col-12 mb-5">
                        <div class="boeder_Bg">
                            <div class="p-5">
                                <h3>Flexible scheduling</h3>

                                <p class="charcol">At MCT, flexibility is at the heart of what we offer. We understand that every educator’s lifestyle is unique, which is why we provide flexible schedules that adapt to your routine — not the other way around. With no employment constraints or time limits, you have the freedom to choose the timings that suit you best and even reschedule classes when your plans change. Whether you're at home or on the go, you can teach from anywhere, without time pressure, and connect with students in your own way and at your own pace.</p>


                            </div>


                        </div>
                    </div>

                    <div class="col-12">
                        <div class="boeder_Bg">
                            <div class="p-5">
                                <h3>Engage and Excel with Our Lecture Recordings!</h3>

                                <p class="charcol">At My Choice Tutor, we are committed to making learning more accessible and convenient. To support our students, we offer the feature to record lectures so you can revisit lessons anytime, anywhere. Whether you need to revise a topic, catch up on a missed class, or simply reinforce your understanding, recorded sessions ensure you never miss out on valuable learning.
                                     This feature empowers students to learn at their own pace and build long-term retention with ease.
                                </p>

                            </div>
                            <img src="{{ url('frontendnew/img/Engage_and_Excel.png') }}" width="100%" alt="">

                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 order3">
                <div class="boeder_Bg">
                    <div class="p-5">
                        <h3>One-on-one lessons</h3>

                        <p class="charcol">At My Choice Tutor, we believe every learner deserves personal attention. That’s why we offer one-on-one lessons designed to match each student’s unique learning style, pace, and goals. Our tutors focus entirely on you, ensuring that every concept is explained clearly and every session is productive. With personalized guidance, students gain confidence, strengthen their skills, and achieve better results in a supportive and distraction-free environment.</p>

                    </div>
                    <img src="{{ url('frontendnew/img/One-on-one-lessons.png') }}" width="100%" alt="">
                </div>

            </div>

        </div>

    </div>

</section>
@endsection