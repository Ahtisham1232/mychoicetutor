@extends('tutor.layouts.main')
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

            .form-check-input:checked {
                background-color: #57b49d !important;
                border-color: #57b49d !important;
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
                <h3 class="text-center">Completed Classes </h3>
                <div class="mt-4" id="">

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middlemb-0 ">
                            <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    {{-- <th scope="col">Meeting ID</th> --}}
                                    <th scope="col">Student</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Slot Date</th>
                                    <th scope="col">Slot Time</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Attendance</th>
                                    <th scope="col">Recording Link</th>
                                    {{-- <th scope="col">Feedback</th> --}}
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($liveclasses as $liveclass)
                                    <tr>

                                        <td>{{ $loop->iteration }}</td>
                                        {{-- <td>{{ $liveclass->meeting_id }}</td> --}}
                                        <td>{{ $liveclass->student }}</td>
                                        <td>{{ $liveclass->class }}</td>
                                        <td>{{ $liveclass->subjects }}</td>
                                        <td>{{ \Carbon\Carbon::parse($liveclass->slotdate)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($liveclass->slottime)->format('h:i A') }}</td>
                                        <td>{{ $liveclass->duration }}</td>
                                        {{-- <td>{{ $liveclass->status }}</td> --}}
                                        <td>
                                            @if ($liveclass->status == 'confirmed' || $liveclass->status == 'Confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif ($liveclass->status == 'waiting' || $liveclass->status == 'Waiting')
                                                <span class="badge bg-primary">Waiting Confirmation</span>
                                            @elseif ($liveclass->status == 'started' || $liveclass->status == 'Started')
                                                <span class="badge bg-success">Started</span>
                                            @elseif ($liveclass->status == 'cancelled' || $liveclass->status == 'Cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @elseif ($liveclass->status == 'completed' || $liveclass->status == 'Completed')
                                                <span class="badge bg-success">Completed</span>
                                                {{-- @elseif ($liveclasses->status == 8)
                                    <span class="badge bg-primary">{{ $liveclasses->currentstatus }}</span> --}}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($liveclass->student_present == '0')
                                                <span class="badge bg-primary">Absent</span>
                                            @elseif ($liveclass->student_present == '1')
                                                <span class="badge bg-success">Present</span>
                                            @else
                                                <span class="badge bg-danger">Not taken</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $recordingUrl = filter_var($liveclass->recording_link, FILTER_VALIDATE_URL)
                                                    ? $liveclass->recording_link
                                                    : asset('storage/' . $liveclass->recording_link);
                                            @endphp
                                            <a href="{{ $recordingUrl }}" target="_blank"><button
                                                    class="btn btn-sm btn-success">Play Now</button></a>
                                        </td>
                                        {{-- <td>{{ $liveclass->tutor_review }} ({{$liveclass->tutor_rating}} ⭐)</td> --}}
                                        <td><button class="btn btn-sm btn-primary"
                                                onclick="openAttModal('{{ $liveclass->id }}','{{ $liveclass->student }}','{{ $liveclass->student_present }}')">Attendance</button>
                                        </td>
                                        <td><a href="assignments"><button class="btn btn-sm btn-success">Add
                                                    Assignment</button></a></td>
                                        <td><button class="btn btn-sm btn-success"
                                                onclick="updaterecordingmodal('{{ $liveclass->id }}','{{ $liveclass->recording_link }}')">Update
                                                Recording Link</button></td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{-- {!! $demos->links() !!} --}}
                    </div>


                </div>
            </div>
            <!-- content-wrapper ends -->



            <div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">


                        <div class="modal-body">


                            <header>
                                <h3 class="text-center mb-4" id="header">Attendance</h3>
                            </header>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Student Name<span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="" name="">
                                    </div>
                                </div>



                                <div style="float:right">
                                    <button type="button" class="btn btn-sm btn-danger mr-1 " data-dismiss="modal"><span
                                            class="fa fa-times"></span> Close</button>
                                    <button type="submit" id="" class="btn btn-sm btn-success ">Submit</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>


            <!--Student List modal -->
            <div class="modal fade" id="studentlistmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">


                        <div class="modal-body">


                            <header>

                                <h3 class="text-center mb-4" id="header">Attendance</h3>

                            </header>

                            <form action="{{ url('tutor/batches/update-attendance') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-12 col-ms-12 mb-3">
                                        {{-- <select id="studentlist" name="studentlist[]" multiple>

                         </select> --}}
                                        <style>
                                            .newclass td,
                                            .newclass th {
                                                padding: 2px !important
                                            }
                                        </style>
                                        <h5 id="attstudentname"></h5>
                                        <input type="checkbox" id="ispresent" name="ispresent"><span><label
                                                for="ispresent">&nbsp; Check to mark as present</label></span>
                                    </div>
                                </div>

                                <input type="hidden" id="post_meeting_id" name="post_meeting_id">



                                <div style="float:right">
                                    <button type="button" class="btn btn-sm btn-danger " data-dismiss="modal"
                                        onclick="closeModal();">
                                        Close</button>
                                    <button type="submit" class="btn btn-sm btn-success  " data-dismiss="modal">
                                        Submit</button>


                                </div>




                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!--Student List modal -->
            <div class="modal fade" id="recordingupdatemodal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body">
                            <header>
                                <h3 class="text-center mb-4" id="header">Recording Link</h3>
                            </header>
                            <form action="{{ url('tutor/batches/update-recording') }}" method="post">
                                @csrf
                                <input type="hidden" id="recording_link_id" name="recording_link_id">
                                <div class="col-12 col-md-12 col-ms-6 mb-3">
                                    <label>Topic</label>
                                    <input type="text" class="form-control" id="class_topic" name="class_topic" required>
                                </div>
                                <div class="col-12 col-md-12 col-ms-6 mb-3">
                                    <label>Recording Link<span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="recording_link" name="recording_link" required>
                                </div>
                                <div style="float:right">
                                    <button type="button" class="btn btn-sm btn-danger " data-dismiss="modal"
                                        onclick="closeRecordingModal();">
                                        Close</button>
                                    <button type="submit" class="btn btn-sm btn-success  " data-dismiss="modal">
                                        Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openAttModal(meeting_id, studentname, status) {
                    document.getElementById('attstudentname').innerHTML = "Attendance of " + studentname;
                    document.getElementById('post_meeting_id').value = meeting_id;
                    if (status == 1) {

                        document.getElementById('ispresent').checked = true;
                    } else {
                        document.getElementById('ispresent').checked = false;

                    }

                    $("#studentlistmodal").modal('show');
                }

                function closeModal() {
                    $("#studentlistmodal").modal('hide');
                }


                function updaterecordingmodal(id, recording_link) {
                    document.getElementById('recording_link_id').value = id;
                    document.getElementById('recording_link').value = recording_link;
                    $("#recordingupdatemodal").modal('show');
                }

                function closeRecordingModal() {
                    $("#recordingupdatemodal").modal('hide');
                }
            </script>
        @endsection
