@extends('student.layouts.main')
@section('main-section')
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->

    <style>
        .listHeader {
            display: flex;
            justify-content: space-between;
        }

        table td,
        table th {
            padding: 9px;
        }
    </style>

    <div class="page-content">

        <div class="card" style="width: 50rem; margin: 0 auto;">

            <div class=" card-header bg-white">

                <div class="row">
                    <div class="col-md-3">
                        <div class="ms-4 d-flex flex-column" style="width: 150px;">
                            <img src="{{ url('images/students/profilepics', '/') }}{{ $student->profile_pic }}"
                                alt="Generic placeholder image" class="img-fluid img-thumbnail mb-2"
                                style="width: 150px; z-index: 1; border-radius: 50%;"
                                onerror="this.onerror=null;this.src='https://mychoicetutor.com/images/avatar/default_avatar_img.jpg';">
                            @if (session('usertype') == 'Parent')
                            @else
                                <a type="button" class="btn btn-outline-dark bg-primary"
                                    href="profileupdate/{{ session('userid')->id }}" style="z-index: 1;">
                                    Edit profile
                                </a>
                            @endif

                        </div>
                    </div>
                    <div class="col-9 text-left">
                        <h2 style="margin-top: 60px;">{{ $student->name ?? session('userid')->name }}</h2>
                    </div>

                </div>

            </div>


            <div class="card-body">

                <h5>Personal Details</h5>
                <table class="tab table-bordered" width="100%" id="">
                    <tr>

                        <th>Name:</th>
                        <td>{{ $student->name ?? session('userid')->name }}</td>
                    </tr>
                    <tr>
                        <th>Date Of Birth:</th>
                        <td>{{ $dob ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Grade:</th>
                        <td>{{ $student->gradename ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Mobile:</th>
                        <td>{{ $student->mobile ?? session('userid')->mobile }}</td>
                    </tr>
                    <tr>
                        <th>Secondry Mobile:</th>
                        <td>{{ $student->secondary_mobile ?? '' }}</td>
                    </tr>

                    <tr>
                        <th>Email:</th>
                        <td>{{ $student->email ?? session('userid')->email }}</td>
                    </tr>
                    <tr>
                        <th>School:</th>
                        <td>{{ $student->school_name ?? '' }}</td>
                    </tr>

                    <tr>
                        <th>Father's Name:</th>
                        <td>{{ $student->fathers_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Mother's Name:</th>
                        <td>{{ $student->mothers_name ?? '' }}</td>
                    </tr>
                </table>
                <br>
                <h5>Achievements</h5>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Details</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($achievement as $achievement)
                            <tr>
                                <td class="text-wrap">{{ $achievement->name }}</td>
                                <td class="text-wrap">{{ $achievement->description }}</td>
                                {{-- <td class="text-wrap">{{$achievement->date}}</td> --}}
                                <td>{{ \Carbon\Carbon::parse($achievement->date)->format('j-F-Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <br>
                <h5>Reviews</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Subject</th>
                        <th>details</th>
                        <th>Rating</th>
                    </tr>

                    @foreach ($reviews as $reviews)
                        <tr>
                            <td>{{ $reviews->subject }}</td>
                            <td>{{ $reviews->name }}</td>

                            <td>
                                @if ($reviews->ratings >= 1)
                                    <span class="fa fa-star checked"></span>
                                @endif
                                @if ($reviews->ratings >= 2)
                                    <span class="fa fa-star checked"></span>
                                @endif
                                @if ($reviews->ratings >= 3)
                                    <span class="fa fa-star checked"></span>
                                @endif
                                @if ($reviews->ratings >= 4)
                                    <span class="fa fa-star checked"></span>
                                @endif
                                @if ($reviews->ratings >= 5)
                                    <span class="fa fa-star checked"></span>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                </table>

            </div>
        </div>


    </div>
    <!-- content-wrapper ends -->
@endsection
