@extends('front-cms.layouts.main')
@section('main-section')
<style>
    /* . Fix Horizontal Scroll */
    html, body {
        overflow-x: hidden;
        position: relative;
        width: 100%;
    }

</style>
    <!-- END header -->
    <section class="bannerSec tutBann">
        <div class="container-fluid">
            <div class="tutorHeader">
                <h1 class="mb-3">
                    Let our tutors worry about your
                </h1>

                <div class="text-center">
                    <p class="charcol">grades while you sit back and study</p>
                </div>

            </div>
        </div>

    </section>


    <section class="howitwork-sec2">
        <div class="container">

            <div class="row my-5">
                <div class="col-lg-12 col-md-12 col-sm-12 col-sm-12">
                    <div class="resource-left">

                        <h3>How it works </h3>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="how-card how-card2 card1">
                        <h2>Create Your Profile</h2>
                        <div class="cardText">
                            <p class="mt-4">Students and tutors sign up, set up their profiles, and add details such as
                                subjects, availability, and learning goals.</p>
                        </div>

                    </div>
                    <div class="imgNumber1">
                        <img class="shaddow-shape1" src="{{ url('frontendnew/img/icons/Vector 1.png') }}" alt="">
                        <img class="numbr11" src="{{ url('frontendnew/img/icons/one.png') }}" alt="">
                    </div>

                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="how-card how-card2 card2 " style=" float: right;">
                        <h2>Find Your Match</h2>
                        <div class="cardText">
                            <p class="mt-4">Students can search and connect with qualified tutors that fit their needs,
                                while tutors receive requests based on their expertise.</p>
                        </div>



                    </div>
                    <div class="imgNumber2">
                        <img class="shaddow-shape2" src="{{ url('frontendnew/img/icons/Vector 2.png') }}" alt="">
                        <img class="numbr22" src="{{ url('frontendnew/img/icons/two.png') }}" alt="">
                    </div>

                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="margin-top: 90px;">
                    <div class="how-card how-card2 card3">
                        <h2>Learn & Grow</h2>
                        <div class="cardText">
                            <p class="mt-4">Schedule sessions, attend interactive classes online, and track progress
                                ,making learning flexible, effective, and personalized.</p>
                        </div>
                    </div>
                    <div class="imgNumber3">
                        <img class="shaddow-shape3" src="{{ url('frontendnew/img/icons/Vector 3.png') }}" alt="">
                        <img class="numbr33" src="{{ url('frontendnew/img/icons/three.png') }}" alt="">
                    </div>
                </div>
            </div>


        </div>

    </section>


    <section>
        <div class="subj-bottom-banner">

            <div class="row">
                <div class="col-md-6 banncol">

                    <div class="bannText">
                        <h2>Is MCT the right fit for you?<br>
                            There is only one way to find out.</h2>

                        <a href="{{ route('std_tutor_registration') }}"><button class="orange-btn">Register Now</button></a>
                    </div>

                </div>
                <div class="col-md-6">
                    <img src="{{ url('frontendnew/img/subj-bann-img.png') }}" alt="">
                </div>
            </div>

        </div>

    </section>
@endsection
