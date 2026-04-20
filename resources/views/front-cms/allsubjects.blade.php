@extends('front-cms.layouts.main')
@section('main-section')
<style>
    html, body {
    overflow-x: hidden;
}
</style>
<section class="bannerSec tutBann">
    <div class="container-fluid">
        <div class="tutorHeader">

            <h1 class="mt-5">
                Learn whatever you want
            </h1>
            <p class="charcol text-center mb-5">Our Subjects</p>


        </div>
    </div>
</section>
<!-- tutor section -->
<section class="mt-5">
    <div class="container tutor-card">
        <h3>Subjects</h3>
        <br>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">

                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="subjcol">
                            <h6>All Subjects</h6>
                            @foreach ($subjects as $subject)

                                <ul>
                                    <li><a href="#">{{$subject->name}}</a></li>
                                </ul>

                            @endforeach

                        </div>
                    </div>
                   
                </div>


            </div>

            {{-- <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                @foreach ($subjectswc as $category => $subjects)
                        <div class="col-12 mb-4">
                            <div class="subjcol">
                                <h6>{{ $category }}</h6>
                                <ul>
                                    @foreach ($subjects as $subject)
                                    <li><a href="#">{{ $subject->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                @endforeach

            </div> --}}


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

                    <a href="/student/register"> <button class="orange-btn">Register Now</button></a>
                </div>

            </div>
            <div class="col-md-6">
                <img  src="{{ url('frontendnew/img/subj-bann-img.png') }}" alt="">
            </div>
        </div>

    </div>

</section>
@endsection