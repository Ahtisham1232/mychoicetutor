@extends('front-cms.layouts.main')
@section('main-section')
    <!-- tutor section -->

    <section class="tutor-details">
        <div class="container tutor-card topheader-tutor">

            <div class="row justify-content-center text-center">
                <div class="col-lg-8">

                    <div class="aboutTutor">
                        <h3 style="margin-top: 75px">Profile Not Available</h3>
                        <p class="charcol mt-3">
                            This tutor has not added their profile information yet.
                        </p>
                        <p class="charcol">
                            Please check back later or explore other tutors available on our platform.
                        </p>

                        <a href="{{route('findatutor')}}" class="btn mt-3" style="background: #6a307d; color: white;">
                            Browse Other Tutors
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection
