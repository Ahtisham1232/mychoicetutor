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
        </style>

        <div class="page-content">
            <div class="container-fluid">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div id="listHeader" class="mb-3">
                    <h3>My Feedback </h3>

                </div>
                <style>
                    .star-icon,
                    .half-star-icon {
                        font-size: 20px;
                        color: gold;
                        display: inline-block;
                        margin-right: 2px;
                    }

                    .star-rating {
                        font-size: 20px;
                        /* Adjust the font size as needed */
                        color: gold;
                        /* Adjust the color to your preferred star color */
                        position: relative;
                    }

                    .star-rating::before {
                        content: "\2605";
                        /* Unicode character for a solid star */
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 50%;
                        /* Adjust the width to control the filled part */
                        overflow: hidden;
                        color: transparent;
                        /* Hide the filled part of the star */
                    }
                </style>
                <div class="mt-4" id="">

                    <table class="table table-hover table-bordered">
                        <thead class="thead-dark ">
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Feedback</th>
                                <th scope="col">Ratings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($feedbacks as $feedback)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $feedback->subject }}</td>
                                    <td>{{ $feedback->name }}</td>
                                    <td>
                                        @php
                                            $fullStars = floor($feedback->ratings);
                                            $halfStar = round($feedback->ratings - $fullStars, 1) == 0.5;
                                            $emptyStars = 5 - ceil($feedback->ratings);
                                        @endphp

                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $fullStars)
                                                <span class="star-icon">★</span>
                                            @elseif ($i == $fullStars + 1 && $halfStar)
                                                <span class="star-icon">☆</span>
                                            @else
                                                <span class="star-icon">☆</span>
                                            @endif
                                        @endfor
                                    </td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{-- {!! $demos->links() !!} --}}
                    </div>


                </div>
            </div>
            <!-- content-wrapper ends -->
        @endsection
