@extends('front-cms.layouts.main')

@section('main-section')
<section class="bannerSec tutBann">
    <div class="container-fluid">
        <div class="tutorHeader text-center">
            <h1 class="mb-5">Your Partner in Smarter Learning.</h1>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="mt-5">
    <div class="container">
        <div class="row align-items-center">
            
            <!-- Text Section -->
            <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                <h2 class="mb-4">About Us</h2>
                <p>
                Since 2000, we have been dedicated to providing quality tutoring services to students across the UK.
                Our new platform builds on years of experience, bringing together highly qualified tutors — many from prestigious
                institutions such as LUMS, CNADE, and other renowned universities in Pakistan — to deliver one-on-one online learning tailored to each student’s needs.

                We pride ourselves on creating a safe, secure, and accessible environment where students and parents can trust the process. With features
                like free trial classes, assignment and test facilitation, complete student evaluations, call recording, and 24/7 live chat support,
                we ensure that learning is both effective and transparent. Our long-standing community of students and tutors is now moving to this new platform,
                ready to embrace a modern, connected, and result-driven tutoring experience.
                </p>

                <div class="about-points mt-4">
                    
                    <h4> Our Mission</h4>
                    <p>
                      To empower UK-based students with personalized, high-quality online tutoring through a safe and transparent platform,
                      making learning more accessible, effective, and engaging.
                    </p>

                    <h4> Our Vision</h4>
                    <p>
                       
                    To become the most trusted tutoring platform in the UK, where students achieve academic excellence with the guidance of world-class
                    tutors, parents stay connected with progress, and education is made simple, secure, and impactful.
                    </p>
                </div>
            </div>

            <!-- Image Section -->
            <div class="col-lg-6 col-md-12 text-center">
                <img src="{{ url('frontendnew/img/aboutus.jpeg') }}" style="width:100%;margin-bottom: 180px;" class="" alt="My Choice Tutor">
            </div>

        </div>
    </div>
</section>
@endsection
