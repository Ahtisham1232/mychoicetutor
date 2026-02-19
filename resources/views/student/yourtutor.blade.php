@php
    use App\Models\payments\paymentstudents;
    use App\Models\students\studentattendance;
@endphp
@extends('student.layouts.main')
@section('main-section')
    <!-- partial -->
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
                <style>
                    .card .card-title {
                        margin-bottom: 0;
                    }

                    .cardBtn {
                        width: 90%;
                        margin-top: 4px;
                    }

                    .tu-primbtn {
                        padding: 8px !important;
                    }

                    @media (min-width: 481px) {

                        .bookedSlot,
                        .startChat {
                            width: 50%;
                        }

                        .tu-primbtn {
                            width: 100%;
                        }

                    }

                    .tu-primbtn,
                    .bookedSlot,
                    .startChat {
                        text-wrap: nowrap;
                    }

                    .aaa1 {
                        display: flex;
                        gap: 10px;
                    }

                    @media (max-width: 480px) {
                        .aaa1 {
                            flex-direction: column;
                        }

                        .bookedSlot,
                        .startChat,
                        .tu-primbtn {
                            width: 100%;
                        }
                    }
                </style>
                <link rel="stylesheet" href="{{ url('frontend/css/profile.css') }}">
                <div id="" class="mb-3 listHeader page-title-box">
                    <p style="font-size: 30px">My Tutors</p>
                </div>

                <hr>

                <div class="row">
                    <div class="col-xl-12 col-xxl-9">
                        <div class="">
                            @if (count($tutorlist) < 1)
                                <div style="display: flex; justify-content: center;">

                                    <img class="img-fluid" src="{{ asset('images/no-data-found.jpg') }}" width="50%">
                                </div>
                            @endif
                            @if (isset($tutorlist))
                                @foreach ($tutorlist as $tutorlist)
                                    <?php
                                    
                                    $class_purchased = paymentstudents::where('student_id', session('userid')->id)->where('class_id', $tutorlist->class_id)->where('tutor_id', $tutorlist->tutor_id)->where('subject_id', $tutorlist->subjectid)->sum('classes_purchased');
                                    
                                    $enrollment_data = paymentstudents::where('student_id', session('userid')->id)->where('class_id', $tutorlist->class_id)->where('tutor_id', $tutorlist->tutor_id)->where('subject_id', $tutorlist->subjectid)->select('classes_purchased', 'rate_per_hr')->get();
                                    
                                    $total_amount_paid = 0;
                                    
                                    foreach ($enrollment_data as $enrollment) {
                                        $total_amount_paid += $enrollment->classes_purchased * $enrollment->rate_per_hr;
                                    }
                                    
                                    $first_purchase_date = paymentstudents::where('student_id', session('userid')->id)
                                        // ->where('class_id', $tutorlist->class_id)
                                        ->where('tutor_id', $tutorlist->tutor_id)
                                        // ->where('subject_id', $tutorlist->subjectid)
                                        ->select('created_at')
                                        ->orderBy('created_at', 'asc') // Assuming you want to order by creation date to get the first purchase
                                        ->first();
                                    $formatted_date = $first_purchase_date ? $first_purchase_date->created_at->format('d-m-Y h:i a') : null;
                                    
                                    $class_attended = studentattendance::where('student_id', session('userid')->id)
                                        // ->where('class_id', $tutorlist->class_id)
                                        ->where('tutor_id', $tutorlist->tutor_id)
                                        // ->where('subject_id', $tutorlist->subjectid)
                                        ->count();
                                    
                                    ?>
                                    <div class="tu-listinginfo">
                                        <span class="tu-cardtag"></span>
                                        <div class="tu-listinginfo_wrapper">
                                            <div class="tu-listinginfo_title">
                                                <div class="tu-listinginfo-img">
                                                    <figure>
                                                        @if (empty($tutorlist->profile_pic))
                                                            <img src="{{asset('images/students/profilepics/no-img.jpg')}}"
                                                                class="rounded-circle mr-1" alt="empty-pic" width="40"
                                                                height="40">
                                                        @else
                                                            <img src="{{ url('images/tutors/profilepics') }}/{{ $tutorlist->profile_pic }}"
                                                                class="rounded-circle mr-1" alt="tutor-pic" width="40"
                                                                height="40">
                                                        @endif
                                                    </figure>
                                                    <div class="tu-listing-heading">
                                                        <h5><a href="{{ url('#') }}">{{ $tutorlist->name }}</a> <i
                                                                class="icon icon-check-circle tu-greenclr"
                                                                data-tippy-trigger="mouseenter"
                                                                data-tippy-html="#tu-verifed" data-tippy-interactive="true"
                                                                data-tippy-placement="top"></i><span
                                                                class="badge bg-success"> Enrolled</span></h5>
                                                        <div class="tu-listing-location">
                                                            <span>{{ $tutorlist->starrating ?? '0' }} <i
                                                                    class="fa-solid fa-star"></i><em></em></span>
                                                            <address><i class="icon icon-map-pin"></i> </address>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tu-listinginfo_price">
                                                    <span>Starting from:</span>
                                                    <h4>£{{ $tutorlist->rate }}/hr</h4>
                                                </div>
                                            </div>
                                            <div class="tu-listinginfo_description">
                                                <p>{{ $tutorlist->headline }}</p>
                                            </div>
                                            <div class="tu-listinginfo_service">
                                                <h6>Enrollment Date: {{ $formatted_date }}</h6>


                                                <ul class="tu-service-list">
                                                    <li>
                                                        <span>
                                                            <i class="icon icon-home tu-greenclr"></i>
                                                            {{ $tutorlist->subject }}
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span>
                                                            <i class="icon tu-blueclr bg-success"></i>
                                                            {{ $tutorlist->total_classes_purchased ?? '0' }} Classes
                                                            Purchased
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span>
                                                            <i class="icon tu-orangeclr"></i>
                                                            {{ $class_attended ?? '0' }} Classes Completed
                                                        </span>
                                                    </li>
                                                    <li>
                                                        <span style="color: green">
                                                            <i class="icon tu-orangeclr"></i>
                                                            {{ $tutorlist->total_classes_purchased ?? ('0' - $class_attended ?? '0') }}
                                                            Classes Remaining
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tu-listinginfo_btn">
                                            {{-- <div class="tu-iconheart">
                                                    <i class="icon icon-heart"></i><span>Add to favourite</span>
                                                < /div> --}}
                                            <div class="tu-btnarea">
                                                <div class="aaa1"style="margin-right:10px; margin-bottom:10px;">
                                                    <a href="enrollupdate/{{ $tutorlist->tutor_id }}"
                                                        class="bookedSlot"><button class="btn btn-sm btn-success">Booked
                                                            Slots</button></a>
                                                    <a href="tutormessages/{{ $tutorlist->tutor_id }}"
                                                        class="startChat"><button class="btn btn-sm btn-success">Start
                                                            Chat</button></a>
                                                </div>
                                                <a href="/student/tutorprofile/{{ $tutorlist->tutor_id }}"
                                                    class="tu-primbtn">View full profile</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <!-- content-wrapper ends -->

            <!-- modal -->
            <div class="modal fade" id="openClassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <style>
                            table tr td,
                            table tr th {
                                padding: 10px !important;
                            }
                        </style>

                        <div class="modal-body">
                            <header>
                                <h3 class="text-center mb-4">Details</h3>
                            </header>
                            <form method="" action="">
                                <table class="table table-bordered table-striped table-hover">
                                    <tbody name="classDetalsTable">
                                        <tr>
                                            <th>Student Name</th>
                                            <td id="studentName">{{ session('userid')->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tutor Name</th>
                                            <td id="tutorName"></td>
                                        </tr>
                                        <tr>
                                            <th>Class</th>
                                            <td id="className"></td>
                                        </tr>
                                        <tr>
                                            <th>Subject</th>
                                            <td id="subjectName"></td>
                                        </tr>
                                        <tr>
                                            <th>Enrollment Date</th>
                                            <td id="enrollmentDate"></td>
                                        </tr>
                                        <tr>
                                            <th>Paid Amount</th>
                                            <td id="paidAmount"></td>
                                        </tr>
                                        <tr>
                                            <th>Total Classes</th>
                                            <td id="totalClassCount"></td>
                                        </tr>

                                        <tr>
                                            <th>Available Classes</th>
                                            <td id="totalAvailableClass"></td>
                                        </tr>

                                        <tr>
                                            <th>Attended Classes</th>
                                            <td id="totalAttendedClass"></td>
                                        </tr>
                                    </tbody>

                                </table>
                                <div class="row float-right mt-2">
                                    <div class=" col-12 col-md-12 col-sm-12" id="fullDetailsBtn">
                                        {{-- <button class="btn btn-primary">View Full Details</button> --}}
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDemoModal(id, name, className, subjectId, subjectName, classPurchased, totalAmountPaid, enrollmentDate,
            classAttended) {
            // alert(totalAmountPaid)

            // document.getElementById('studentName').innerHTML = name;
            document.getElementById('tutorName').innerHTML = name;
            document.getElementById('className').innerHTML = className;
            document.getElementById('subjectName').innerHTML = subjectName;
            document.getElementById('enrollmentDate').innerHTML = enrollmentDate;
            document.getElementById('paidAmount').innerHTML = totalAmountPaid;
            document.getElementById('totalClassCount').innerHTML = classPurchased;
            document.getElementById('totalAvailableClass').innerHTML = classPurchased - classAttended;
            document.getElementById('totalAttendedClass').innerHTML = classAttended;
            // document.getElementById('fullDetailsBtn').innerHTML = "<a href='completed-classes'><button class='btn btn-primary'>View All Classes</button></a>";
            $("#openClassModal").modal('show');
        }
    </script>
    <!-- content-wrapper ends -->
@endsection
