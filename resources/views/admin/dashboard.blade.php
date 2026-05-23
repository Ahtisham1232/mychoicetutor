@extends('admin.layouts.main')
@section('main-section')


        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0"> Dashboard</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboards</a></li> --}}
                                        {{-- <li class="breadcrumb-item active">Analytics</li> --}}
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->


                    <div class="row">
                <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                    <div class="card card-animate dashboardcard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="dashCard">
                                    <p class="fw-medium  mb-0"><b>Student Enrolled</b></p>
                                    <div class="topCradCount">
                                        <h2 class="pt-2" style="color:#D63531;"><span class="counter-value" data-target="{{$stud_count ?? "0"}}">{{$stud_count ?? "0"}}</span>
                                        </h2>

                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="{{url('admin/students')}}" class="text-decoration-underline"><button
                                        class="badge bg-secondry p-2 text-dark border-0">View
                                        All</button></a>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                    <div class="card card-animate dashboardcard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="dashCard">
                                    <p class="fw-medium  mb-0"><b>Pending Schedule Classes</b></p>
                                    <div class="topCradCount">
                                        {{-- <h2 class="pt-2 text-primary"><span class="counter-value1"
                                                data-target="">{{$totalUpcomingClasses}}</span>
                                        </h2> --}}
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="{{route('admin.pendingscheduledclasses')}}" class="text-decoration-underline"><button
                                        class="badge bg-secondry p-2 text-dark border-0">View
                                        All</button></a>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                    <div class="card card-animate dashboardcard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="dashCard">
                                    <p class="fw-medium  mb-0"><b>Pending Trial Classes</b></p>
                                    {{-- <div class="topCradCount">
                                        <h2 class="pt-2" style="color: #88B0F1"><span class="counter-value" data-target="{{$pending_demos ?? '0'}}">{{$pending_demos ?? '0'}}</span>
                                        </h2>
                                    </div> --}}
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="{{url('admin.pendingtrials')}}" class="text-decoration-underline"><button
                                        class="badge bg-secondry p-2 text-dark border-0">View
                                        All</button></a>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                    <div class="card card-animate dashboardcard">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="dashCard">
                                    <p class="fw-medium  mb-0"><b>Total Earnings</b></p>
                                    <div class="topCradCount">
                                        <h2 class="pt-2" style="color: #59C069">{{config('common.currency.symbol')}}<span class="counter-value" data-target="{{$moneyEarned->total_earned ?? "0"}}">{{$moneyEarned->total_earned ?? "0"}}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="payments" class="text-decoration-underline"><button
                                        class="badge bg-secondry p-2 text-dark border-0">View
                                        All</button></a>
                            </div>
                        </div><!-- end card body -->
                    </div> <!-- end card-->
                </div>

            </div>

            <div class="row">
                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="card card-animate">
                        <div class="cardTitle">
                            <h5>Schedule Classes</h5>
                            <a href="scheduledclasses"><b>View All</b></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table dash_table  table-responsive">
                                    <thead>
                                        <tr class="">
                                            <th>Subject</th>
                                            <th>Student</th>
                                            <th>Tutor</th>
                                            <th>Booked Slot</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($upcomingClasses as $class)
                                        <tr>
                                            <td>{{$class->subject_name}}</td>
                                            <td>{{$class->student_name}}</td>
                                            <td>{{$class->tutor_name}}</td>
                                            <td>{{ $class->start_time ? \Carbon\Carbon::parse($class->start_time)->format('d-m-Y h:i A') : '' }}</td>
                                            <td>
                                                @if ($class->is_completed == 1)
                                                <span class="live">Live</span></td>
                                                    @else
                                                    <span class="confirm">Not Started</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($class->is_completed == 1)
                                               <a href="{{$class->join_url}}" target="_blank"> <span class="endClass"> Join Class</span></a>
                                                @else
                                                 {{-- <a href="getclasslist">   <span class="endClass"> Start Class</span> </a> --}}

                                                @endif
                                            </td>
                                            {{-- <td><span class="endClass"> End Class</span></td> --}}
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Schedule Trial Classes -->
                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="card card-animate">
                        <div class="cardTitle">
                            <h5>Trial Classes</h5>
                            <a href="demolist"><b>View All</b></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table dash_table">
                                    <thead>
                                        <tr class="">

                                            <th>Student</th>
                                            <th>Tutor</th>
                                            <th>Subject</th>
                                            <th>slot</th>
                                            <th>Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($upcoming_demos as $demo)
                                        <tr>
                                            {{-- <td><div class="namePic">
                                                    <img src="/images/students/profilepics/{{$demo->student_img}}"
                                                        alt="">
                                                    <span>{{$demo->student}}</span>
                                                </div>
                                            </td> --}}
                                            <td>{{$demo->student}}</td>
                                            <td>{{$demo->tutor_name}}</td>
                                            <td>{{$demo->subject}}</td>

                                            <td>
                                                {{ $demo->slot_1 ? \Carbon\Carbon::parse($demo->slot_1)->format('d-m-Y h:i:s A') : '' }}
                                            </td>

                                            <td>
                                            @if ($demo->status == 1)
                                            <span class="confirm">New</span>
                                            @elseif ($demo->status == 2)
                                            <span class="confirm">Scheduled</span>
                                            @elseif ($demo->status == 3)
                                            <span class="confirm">Confirmed</span>
                                            @elseif ($demo->status == 4)
                                            <span class="confirm">Attended</span>
                                            @elseif ($demo->status == 5)
                                            <span class="live">Cancelled</span>
                                            @else
                                            <span class="live">Started</span>
                                            @endif
                                            </td>
                                            {{-- <td><a href="{{ $demo->remarks ?? $demo->demo_link}}"><span class="endClass"> Launch Trial Class</span></a></td> --}}
                                        </tr>
                                        @endforeach

{{--
                                        <tr>
                                            <td>
                                                <div class="namePic">
                                                    <img src="/images/students/profilepics/1688709846.jpg"
                                                        alt="">
                                                    <span>Deepesh</span>
                                                </div>
                                            </td>
                                            <td>Maths</td>

                                            <td>Today<br>10:00am</td>
                                            <td> <span class="confirm">Conffirmed</span></td>
                                            <td><span class="hrsLeft">9 Hours Left</span></td>
                                        </tr>



                                        <tr>
                                        <td>
                                                <div class="namePic">
                                                    <img src="/images/students/profilepics/1688709846.jpg"
                                                        alt="">
                                                    <span>Deepesh</span>
                                                </div>
                                            </td>
                                            <td>Maths</td>

                                            <td>Today<br>10:00am</td>
                                            <td> <span class="pending">Pending</span></td>
                                            <td>
                                                <div class="apprv">
                                                <span class="accept">Accept</span>
                                                <span class="reject">Reject</span>

                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                        <td>
                                                <div class="namePic">
                                                    <img src="/images/students/profilepics/1688709846.jpg"
                                                        alt="">
                                                    <span>Deepesh</span>
                                                </div>
                                            </td>
                                            <td>Maths</td>

                                            <td>Today<br>10:00am</td>
                                            <td> <span class="pending">Pending</span></td>
                                            <td>
                                                <div class="apprv">
                                                <span class="accept">Accept</span>
                                                <span class="reject">Reject</span>

                                                </div>
                                            </td>
                                        </tr> --}}
                                    </tbody>

                                </table>
                            </div>


                        </div>
                    </div>
                </div>
            </div> <!-- end row-->



                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <script>
    function switchmode(id) {
        if (id == 1) {
            document.getElementById('tblassignment').hidden = true;
            document.getElementById('tbltest').hidden = false;
            document.getElementById('tasslink').hidden = true;
            document.getElementById('trplink').hidden = false;
        } else {
            document.getElementById('tblassignment').hidden = false;
            document.getElementById('tbltest').hidden = true;
            document.getElementById('tasslink').hidden = false;
            document.getElementById('trplink').hidden = true;
        }
    }
    </script>

@endsection
