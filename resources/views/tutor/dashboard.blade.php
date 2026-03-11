@extends('tutor.layouts.main')
@section('main-section')


        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            {{-- @if (Session::has('success')) --}}
            {{-- @endif
                @if (Session::has('fail'))
                <div class="alert alert-danger">{{Session::get('fail')}}</div>
                @endif --}}
                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Dashboard</h4>
                                    @if (session('userid')->is_active == 0)
                                    <div class="alert alert-danger">Your account is inactive. Please log out and log in again to check the status. If the issue persists, please contact the admin.  To chat with admin <a href="adminmessages" class="btn btn-sm btn-success">Click here</a></div>

                                    @endif

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
                                        <h2 class="pt-2" style="color:#D63531;"><span class="counter-value" data-target="{{$studentscount ?? "0"}}">{{$studentscount ?? "0"}}</span>
                                        </h2>

                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="{{url('tutor/students')}}" class="text-decoration-underline"><button
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
                                    <p class="fw-medium  mb-0"><b>Pending Classes</b></p>
                                    <div class="topCradCount">
                                        <h2 class="pt-2 text-primary"><span class="counter-value1"
                                                data-target="">{{$totalUpcomingClasses}}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="getclasslist" class="text-decoration-underline"><button
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
                                    <div class="topCradCount">
                                        <h2 class="pt-2" style="color: #88B0F1"><span class="counter-value" data-target="{{$pending_demos ?? '0'}}">{{$pending_demos ?? '0'}}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="{{url('tutor/demolist')}}" class="text-decoration-underline"><button
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
                                        <h2 class="pt-2" style="color: #59C069">£<span class="counter-value" data-target="{{$moneyEarned ?? "0"}}">{{$moneyEarned ?? "0"}}</span>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div style="float:right">
                                <a href="payouts" class="text-decoration-underline"><button
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
                            <h5>Scheduled Classes</h5>
                            <a href="getclasslist"><b>View All</b></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table dash_table  table-responsive">
                                    <thead>
                                        <tr class="">
                                            <th>Subject</th>
                                            <th>Student</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($upcomingClasses as $class)
                                        <tr>
                                            <td>{{$class->subject}}</td>
                                            <td>{{$class->studentname}}</td>
                                            <td>{{ \Carbon\Carbon::parse($class->slotdate)->format('d/m/Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($class->slottime)->format('h:i A') }}</td>
                                            <td>
                                                {{-- @if ($class->is_completed == 1) --}}
                                                <span class="confirm">{{$class->status}}</span></td>
                                                    {{-- @else --}}
                                                    {{-- <span class="confirm">Not Started</span> --}}
                                                {{-- @endif --}}
                                            </td>
                                            {{-- <td>
                                                @if ($class->status == 'Started')
                                               <a href="{{$class->join_url}}" target="_blank"> <span class="endClass"> Join Class</span></a>
                                                @else
                                                 <a href="getclasslist">   <span class="endClass"> Start Class</span> </a>

                                                @endif
                                            </td> --}}
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
                                            <th>Subject</th>
                                            <th>slot</th>
                                            <th>Status</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($upcoming_demos as $demo)
                                        <tr>
                                            <td><div class="namePic">
                                                    <span>{{$demo->student}}</span>
                                                </div>
                                            </td>
                                            <td>{{$demo->subject}}</td>

                                            <td>{{ $demo->slot_confirmed ? \App\Helpers\TimezoneHelper::formatInUserTz($demo->slot_confirmed, 'd/m/Y h:i A', 'UTC') : ($demo->slot_1 ? $demo->slot_1->format('d/m/Y h:i A') : '') }}</td>
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
                                            {{-- <td><a href="{{$demo->demo_link}}" target="_blank"><span class="endClass"> Launch Trial Class</span></a></td> --}}
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

            <div class="row" hidden>
                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="card card-animate">
                        <div class="cardTitle">
                            <h5>Homework</h5>
                            <div class="hwMenu" hidden>
                                <a href="#test" onclick="switchmode(1)"><span class="test">Test</span></a>
                                <a href="#assignment" onclick="switchmode(2)"><span class="test">Assignment</span></a>

                            </div>
                            <a href="assignments" id="trplink"><b>View All</b></a>
                            <a href="/student/assignments" id="tasslink" hidden><b>View All</b></a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table trialtable dash_table" id="tbltest">
                                    <!-- <thead>
                                        <tr class="">
                                            <th>Subject</th>
                                            <th>Test Name</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead> -->
                                    <tbody>

                                    </tbody>


                                </table>

                                <table class="table trialtable dash_table " id="tblassignment" hidden>
                                    <thead>
                                        <tr class="">
                                            <th>Subject</th>
                                            <th>Assignment Name</th>
                                            <th>Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <td>Maths</td>
                                            <td>Algebra</td>
                                            <td>Today 10:00am</td>

                                            <td><span class="test">Test</span></td>
                                        </tr>

                                        <tr>
                                            <td>Maths</td>
                                            <td>Algebra</td>
                                            <td>Today 10:00am</td>

                                            <td><span class="quizs">Quiz</span></td>
                                        </tr>
                                    </tbody>


                                </table>

                            </div>


                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <div class="card card-animate">
                        <div class="cardTitle">
                            <h5>Results </h5>
                            <div class="hwMenu">
                                <a href="#testreports" onclick="testprogress(1)"><span class="assign">Test
                                        Report</span></a>
                                <a href="#progressreports" onclick="testprogress(2)"><span class="assign">Progress
                                        Report</span></a>
                            </div>
                            <a href="onlinetestlist"><b>View All</b></a>
                        </div>
                        <div class="card-body" id="testreport">

                            <div class="table-responsive">
                                <table class="table trialtable dash_table" id="">
                                    <thead>
                                        <tr class="">

                                            <th>Total Marks</th>
                                            <th>Test</th>
                                            <th>Obtained</th>
                                            {{-- <th>Action</th> --}}
                                        </tr>
                                    </thead>

                                    <tbody>

                                    </tbody>



                                </table>
                            </div>
                        </div>
                        <div class="card-body" id="progressreport" hidden>

                            <table class="table trialtable dash_table table-responsive">
                                <thead>
                                    <tr class="">
                                        <th>Subject</th>
                                        <th>Total Tests</th>
                                        <th>Progress</th>
                                        <th>Action </th>
                                    </tr>
                                </thead>



                            </table>
                        </div>
                    </div>
                </div>

                <!-- end row-->
            </div>




                    <div class="row" hidden>
                        <div class="col-xxl-5">
                            <div class="d-flex flex-column h-100">
                                <div class="row h-100" hidden>
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body p-0">
                                                <div class="alert alert-success border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                                                    {{-- <i data-feather="alert-triangle" class="text-warning me-2 icon-sm"></i> --}}
                                                    <div class="flex-grow-1 text-truncate">
                                                        Welcome To Dashboard

                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        <a  class="text-reset text-decoration-underline"><b>{{session('userid')->name}}!</b></a>
                                                    </div>
                                                </div>

                                                <div class="row align-items-end">
                                                    <div class="col-sm-12">
                                                        <div class="p-3">
                                                            <p class="fs-16 lh-base">"Education is the key to unlocking a world of possibilities, and with this online tuition app, we're not just gaining knowledge; we're gaining the future. Welcome to a journey of learning, where every lesson brings us closer to our dreams. Together, let's explore, discover, and excel. Here's to the start of an exciting educational adventure!"</p>

                                                            <div class="mt-3">
                                                                {{-- <a href="pages-pricing.html" class="btn btn-success">Upgrade Account!</a> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-sm-4">
                                                        <div class="px-3">
                                                            <img src="assets/images/user-illustarator-2.png" class="img-fluid" alt="">
                                                        </div>
                                                    </div> -->
                                                </div>
                                            </div> <!-- end card-body-->
                                        </div>
                                    </div> <!-- end col-->
                                </div> <!-- end row-->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <p class="fw-medium text-muted mb-0">Demo Pending</p>
                                                       <div>
                                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{$pending_demos ?? '0'}}">{{$pending_demos ?? '0'}}</span></h4>
                                                            <a href="{{url('tutor/demolist')}}" class="text-decoration-underline">View details</a>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="avatar-sm flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                                <i data-feather="users" class="text-info"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->

                                    <div class="col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <p class="fw-medium text-muted mb-0">Classes Taken</p>
                                                        <div>
                                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{$classes_taken ?? '0'}}">{{$classes_taken ?? '0'}}</span></h4>
                                                            <a href="{{url('tutor/getclasslist')}}" class="text-decoration-underline">View details</a>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="avatar-sm flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                                <i data-feather="activity" class="text-info"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->
                                </div> <!-- end row-->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <p class="fw-medium text-muted mb-0">Total Earning</p>
                                                        <div>
                                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">£<span class="counter-value" data-target="{{$moneyEarned->total_earned ?? "0"}}">{{$moneyEarned->total_earned ?? "0"}}</span></h4>
                                                            <a href="{{url('tutor/payments')}}" class="text-decoration-underline">View details</a>
                                                        </div></div>
                                                    <div>
                                                        <div class="avatar-sm flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                                <i data-feather="clock" class="text-info"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->

                                    <div class="col-md-6">
                                        <div class="card card-animate">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <p class="fw-medium text-muted mb-0">Students Enrolled</p>
                                                        <div>
                                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{$moneyEarned->student_count ?? "0"}}">{{$moneyEarned->student_count ?? "0"}}</span></h4>
                                                            <a href="{{url('tutor/batches')}}" class="text-decoration-underline">View details</a>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="avatar-sm flex-shrink-0">
                                                            <span class="avatar-title bg-info-subtle rounded-circle fs-2">
                                                                <i data-feather="external-link" class="text-info"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><!-- end card body -->
                                        </div> <!-- end card-->
                                    </div> <!-- end col-->
                                </div> <!-- end row-->
                            </div>
                        </div> <!-- end col-->

                        <div class="col-xxl-7">
                            <div class="row h-100">
                                <div class="col-xxl-5">
                                    <div class="card card-height-100">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Upcoming Classes</h4>

                                        </div><!-- end card header -->

                                        {{-- <div class="card-body pt-0">
                                            <ul class="list-group list-group-flush border-dashed">
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">25</h5>
                                                                    <div class="text-muted">Tue</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">12:00am - 03:30pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Meeting for campaign with sales team</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">20</h5>
                                                                    <div class="text-muted">Wed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">02:00pm - 03:45pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Adding a new event with attachments</a>
                                                        </div>
                                                   </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">17</h5>
                                                                    <div class="text-muted">Wed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">04:30pm - 07:15pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Create new project Bundling Product</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">12</h5>
                                                                    <div class="text-muted">Tue</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">10:30am - 01:15pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Weekly closed sales won checking with sales team</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                            </ul><!-- end -->
                                            <div class="align-items-center mt-2 row text-center text-sm-start">
                                                <div class="col-sm">
                                                    <div class="text-muted">Showing<span class="fw-semibold">4</span> of <span class="fw-semibold">125</span> Results</div>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
                                                        <li class="page-item disabled">
                                                            <a href="#" class="page-link">←</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">1</a>
                                                        </li>
                                                        <li class="page-item active">
                                                            <a href="#" class="page-link">2</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">3</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">→</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div><!-- end card body --> --}}
                                        <div class="card-body pt-0">
                                            <ul class="list-group list-group-flush border-dashed">
                                                @foreach ($upcomingClasses as $class)
                                                    <li class="list-group-item ps-0">
                                                        <div class="row align-items-center g-3">
                                                            <div class="col-auto">
                                                                <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                    <div class="text-center">
                                                                        <h5 class="mb-0">{{ $class->start_time->format('d') }}</h5>
                                                                        <div class="text-muted">{{ $class->start_time->format('M') }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <h5 class="text-muted mt-0 mb-1 fs-13">{{ $class->start_time->format('h:iA') }} - {{ $class->start_time->addMinutes($class->duration)->format('h:iA') }}</h5>
                                                                <a href="#" class="text-reset fs-14 mb-0">{{ $class->topic_name }}</a>
                                                            </div>
                                                        </div><!-- end row -->
                                                    </li><!-- end -->
                                                @endforeach
                                            </ul><!-- end -->
                                            <div class="align-items-center mt-2 row text-center text-sm-start">
                                                <div class="col-sm">
                                                    {{-- <div class="text-muted">Showing <span class="fw-semibold">{{ count($upcomingClasses) }}</span> of <span class="fw-semibold">{{ $totalClassesCount }}</span> Results</div> --}}
                                                </div>
                                                <div class="col-sm-auto">
                                                    <!-- Pagination can be added here if needed -->
                                                </div>
                                            </div>
                                        </div>



                                    </div><!-- end card -->
                                </div>

                                <div class="col-xxl-5">
                                    <div class="card card-height-100">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Upcoming Tests/Quizes</h4>

                                        </div><!-- end card header -->

                                        {{-- <div class="card-body pt-0">
                                            <ul class="list-group list-group-flush border-dashed">
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">25</h5>
                                                                    <div class="text-muted">Tue</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">12:00am - 03:30pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Meeting for campaign with sales team</a>
                                                        </div>

                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">20</h5>
                                                                    <div class="text-muted">Wed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">02:00pm - 03:45pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Adding a new event with attachments</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">17</h5>
                                                                    <div class="text-muted">Wed</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">04:30pm - 07:15pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Create new project Bundling Product</a>
                                                        </div>
                                                   </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">12</h5>
                                                                    <div class="text-muted">Tue</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">10:30am - 01:15pm</h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">Weekly closed sales won checking with sales team</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                            </ul><!-- end -->
                                            <div class="align-items-center mt-2 row text-center text-sm-start">
                                                <div class="col-sm">
                                                    <div class="text-muted">Showing<span class="fw-semibold">4</span> of <span class="fw-semibold">125</span> Results</div>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
                                                        <li class="page-item disabled">
                                                            <a href="#" class="page-link">←</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">1</a>
                                                        </li>
                                                        <li class="page-item active">
                                                            <a href="#" class="page-link">2</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">3</a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a href="#" class="page-link">→</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div><!-- end card body --> --}}

                                        <div class="card-body pt-0">
                                            <ul class="list-group list-group-flush border-dashed">
                                                @foreach ($upcomingQuizes as $quiz)
                                                <li class="list-group-item ps-0">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-auto">
                                                            <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                                <div class="text-center">
                                                                    <h5 class="mb-0">{{ $quiz->test_start_date->format('d') }}</h5>
                                                                    <div class="text-muted">{{ $quiz->test_start_date->format('M') }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <h5 class="text-muted mt-0 mb-1 fs-13">
                                                                {{ $quiz->test_start_date->format('Y/m/d h:i:s A') }} - {{ $quiz->test_end_date->format('Y/m/d h:i:s A') }}
                                                            </h5>
                                                            <a href="#" class="text-reset fs-14 mb-0">{{ $quiz->name }}</a>
                                                        </div>
                                                    </div>
                                                    <!-- end row -->
                                                </li><!-- end -->
                                                @endforeach
                                            </ul><!-- end -->
                                            <div class="align-items-center mt-2 row text-center text-sm-start">
                                                <div class="col-sm">
                                                    <div class="text-muted">
                                                        {{-- Showing <span class="fw-semibold">{{ $upcomingQuizes->count() }}</span> of <span class="fw-semibold">{{ $totalQuizes }}</span> Results --}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-auto">
                                                    <!-- Pagination can be added here if needed -->
                                                </div>
                                            </div>
                                        </div><!-- end card -->
                                    </div>



                            </div> <!-- end row-->
                        </div><!-- end col -->
                    </div> <!-- end row-->
                    <div class="row">
                        <div class="col-xxl-3">
                            <div class="card card-height-100">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Latest Payments</h4>

                                </div><!-- end card header -->

                                {{-- <div class="card-body pt-0">
                                    <ul class="list-group list-group-flush border-dashed">
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                                <div class="col-auto">
                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                        <div class="text-center">
                                                            <h5 class="mb-0">25</h5>
                                                            <div class="text-muted">Tue</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h5 class="text-muted mt-0 mb-1 fs-13">txn-1234657890-5012450-5</h5>
                                                    <a href="#" class="text-reset fs-14 mb-0">Purchased 4 Classes For Maths</a>
                                                </div>
                                            </div>
                                            <!-- end row -->
                                        </li><!-- end -->
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                                <div class="col-auto">
                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                        <div class="text-center">
                                                            <h5 class="mb-0">20</h5>
                                                            <div class="text-muted">Wed</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h5 class="text-muted mt-0 mb-1 fs-13">txn-1234657890-5012450-5</h5>
                                                    <a href="#" class="text-reset fs-14 mb-0">Purchased 4 Classes For Maths</a>
                                                </div>
                                           </div>
                                            <!-- end row -->
                                        </li><!-- end -->
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                                <div class="col-auto">
                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                        <div class="text-center">
                                                            <h5 class="mb-0">17</h5>
                                                            <div class="text-muted">Wed</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h5 class="text-muted mt-0 mb-1 fs-13">txn-1234657890-5012450-5</h5>
                                                    <a href="#" class="text-reset fs-14 mb-0">Purchased 4 Classes For Maths</a>
                                                </div>
                                            </div>
                                            <!-- end row -->
                                        </li><!-- end -->
                                        <li class="list-group-item ps-0">
                                            <div class="row align-items-center g-3">
                                                <div class="col-auto">
                                                    <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                        <div class="text-center">
                                                            <h5 class="mb-0">12</h5>
                                                            <div class="text-muted">Tue</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <h5 class="text-muted mt-0 mb-1 fs-13">txn-1234657890-5012450-5</h5>
                                                    <a href="#" class="text-reset fs-14 mb-0">Purchased 4 Classes For Maths</a>
                                                </div>
                                            </div>
                                            <!-- end row -->
                                        </li><!-- end -->
                                    </ul><!-- end -->
                                    <div class="align-items-center mt-2 row text-center text-sm-start">
                                        <div class="col-sm">
                                            <div class="text-muted">Showing<span class="fw-semibold">4</span> of <span class="fw-semibold">125</span> Results</div>
                                        </div>
                                        <div class="col-sm-auto">
                                            <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
                                                <li class="page-item disabled">
                                                    <a href="#" class="page-link">←</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="#" class="page-link">1</a>
                                                </li>
                                                <li class="page-item active">
                                                    <a href="#" class="page-link">2</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="#" class="page-link">3</a>
                                                </li>
                                                <li class="page-item">
                                                    <a href="#" class="page-link">→</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="card-body pt-0">
                                    <ul class="list-group list-group-flush border-dashed">
                                        @foreach ($latest_payments as $payment)
                                            <li class="list-group-item ps-0">
                                                <div class="row align-items-center g-3">
                                                    <div class="col-auto">
                                                        <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                            <div class="text-center">
                                                                <h5 class="mb-0">{{ $payment->payment_date->format('d') }}</h5>
                                                                <div class="text-muted">{{ $payment->payment_date->format('M') }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <h5 class="text-muted mt-0 mb-1 fs-13">{{ $payment->transaction_id }}</h5>
                                                        <a href="#" class="text-reset fs-14 mb-0"> <a href="#" class="text-reset fs-14 mb-0">Purchased {{ $payment->classes_purchased }} Classes For {{ $payment->name }}</a></a>
                                                    </div>
                                                </div>
                                                <!-- end row -->
                                            </li><!-- end -->
                                        @endforeach
                                    </ul><!-- end -->
                                    <div class="align-items-center mt-2 row text-center text-sm-start">
                                        <div class="col-sm">
                                            {{-- <div class="text-muted">Showing <span class="fw-semibold">{{ $latest_payments->count() }}</span> of <span class="fw-semibold">125</span> Results</div> --}}
                                        </div>
                                        <div class="col-sm-auto">

                                        </div>
                                    </div>
                                </div>



                            </div><!-- end card -->
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card card-height-100">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Demo Conversion</h4>

                                </div>

                                <div class="card-body">

                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h6 class="text-muted text-uppercase fw-semibold text-truncate fs-12 mb-3">Total Demo Taken</h6>
                                            <h4 class="mb-0">{{$totalDemosTaken ?? '0'}}</h4>
                                            {{-- <p class="mb-0 mt-2 text-muted"><span class="badge bg-success-subtle text-success mb-0"> <i class="ri-arrow-up-line align-middle"></i> 15.72 % </span> vs. previous month</p> --}}
                                        </div><!-- end col -->
                                        <div class="col-6">
                                            <div class="text-center">
                                                <img src="assets/images/illustrator-1.png" class="img-fluid" alt="">
                                            </div>
                                        </div><!-- end col -->
                                    </div><!-- end row -->
                                    <div class="mt-3 pt-2">
                                        <div class="progress progress-lg rounded-pill">
                                            @php
                                                $colorClasses = ['primary', 'info', 'success', 'warning', 'danger'];
                                                $colorIndex = 0;
                                            @endphp

                                            @foreach ($subjectData as $subject)
                                                <div class="progress-bar bg-{{ $colorClasses[$colorIndex] }}" role="progressbar" style="width: {{ $subject['percentage'] }}%;" aria-valuenow="{{ $subject['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>

                                                {{-- Cycle the color index --}}
                                                @php
                                                    $colorIndex = ($colorIndex + 1) % count($colorClasses);
                                                @endphp
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2">
                                        @php
                                            $colorClasses = ['primary', 'info', 'success', 'warning', 'danger'];
                                            $colorIndex = 0;
                                        @endphp
                                        @foreach ($subjectData as $subject)
                                            <div class="d-flex mb-2">
                                                <div class="flex-grow-1">
                                                    <p class="text-truncate text-muted fs-14 mb-0"><i class="mdi mdi-circle align-middle text-{{ $colorClasses[$colorIndex] }} me-2"></i>{{ $subject['subject_name'] }}</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <p class="mb-0">{{ number_format($subject['percentage'], 2) }}%</p>
                                                </div>
                                            </div><!-- end -->
                                            @php
                                                $colorIndex = ($colorIndex + 1) % count($colorClasses);
                                            @endphp
                                        @endforeach
                                    </div><!-- end -->
                                    <div class="mt-2 text-center">
                                        {{-- <a href="javascript:void(0);" class="text-muted text-decoration-underline">Show All</a> --}}
                                    </div>

                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->

                        <div class="col-xxl-4">
                            <div class="card card-height-100">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Upcoming Demo Classes</h4>

                                </div><!-- end card header -->
                                <div class="card-body pt-0">
                                    <ul class="list-group list-group-flush border-dashed">
                                        @foreach ($upcoming_demos as $demo)
                                            @php
                                                $demoSlotTz = $demo->slot_confirmed ? \App\Helpers\TimezoneHelper::toUserTz($demo->slot_confirmed, 'UTC') : $demo->slot_1;
                                            @endphp
                                            <li class="list-group-item ps-0">
                                                <div class="row align-items-center g-3">
                                                    <div class="col-auto">
                                                        <div class="avatar-sm p-1 py-2 h-auto bg-light rounded-3">
                                                            <div class="text-center">
                                                                <h5 class="mb-0">{{ $demoSlotTz ? $demoSlotTz->format('d') : '' }}</h5>
                                                                <div class="text-muted">{{ $demoSlotTz ? $demoSlotTz->format('M') : '' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <h5 class="text-muted mt-0 mb-1 fs-13">{{ $demo->slot_confirmed ? \App\Helpers\TimezoneHelper::formatInUserTz($demo->slot_confirmed, 'Y/m/d h:i A', 'UTC') : ($demo->slot_1 ? $demo->slot_1->format('Y/m/d h:i A') : '') }}</h5>
                                                        <a href="#" class="text-reset fs-14 mb-0">{{ $demo->remarks ?? $demo->demo_link}}</a>
                                                    </div>

                                                </div>
                                                <!-- end row -->
                                            </li>
                                        @endforeach
                                    </ul><!-- end -->
                                    <div class="align-items-center mt-2 row text-center text-sm-start">
                                        <div class="col-sm">
                                            {{-- <div class="text-muted">Showing<span class="fw-semibold">4</span> of <span class="fw-semibold">125</span> Results</div> --}}
                                        </div>
                                        <div class="col-sm-auto">

                                        </div>
                                    </div>
                                </div>

                            </div><!-- end card -->
                        </div>

                    </div><!-- end row -->

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
