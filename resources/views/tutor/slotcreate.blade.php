@extends('tutor.layouts.main')
@section('main-section')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">a
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <style>
    .listHeader {
        display: flex;
        justify-content: space-between;
    }

    .moveRight {
        float: right;
    }

    .bookingBtns {
        display: inline-flex;
    }

    .bookingBtns1 {
        display: inline-flex;
        align-items: center
    }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-content">
        <div class="container-fluid">

            @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
            <br>
            @endif
            @if (Session::has('fail'))
            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            <br>
            @endif
            <div id="" class="mb-3  page-title-box1">
               <div class="listHeader mb-5">
               <h3 >Slots Management </h3>
                <button class="btn btn-sm btn-success bookingBtns1" style="margin-right: 5px" type="button"
                            onclick="openclassmodalcreate();"><i class="ri-calendar-todo-fill"></i> &nbsp;Create
                            Slot</button>

               </div>
                <form action="{{ route('tutor.slots.search') }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 mb-2">
                        <select type="text" class="form-control" style="width:100%"
                            id="selectsubject" name="selectsubject">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 mb-2">
                        <select type="text" class="form-control" sstyle="width:100%"
                            id="selectstudent" name="selectstudent">
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 mb-2">
                        <select type="text" class="form-control" style="width:100%"
                            id="bookingstatus" name="bookingstatus">
                            <option value="" disabled selected>Select Slot Status</option>
                            <option value="">All Slots</option>
                            <option value="1">Booked Slots</option>
                            <option value="2">Available Slots</option>
                        </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 mb-2">
                        <select type="text" class="form-control" style="width:100%" id="classstatus"
                            name="classstatus">
                            <option value="" disabled selected>Select Class Status</option>
                            <option value="">All Classes</option>
                            {{-- <option value="0">Not Scheduled Classes</option> --}}
                            <option value="1">Scheduled Classes</option>
                            <option value="2">Completed Classes</option>
                        </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12 mb-2">
                        <input type="date" class="form-control" style="width:100%"
                        onchange="searchSlots();" id="searchDate" name="searchDate">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12">
                        <button class="btn btn-sm btn-primary bookingBtns1" type="submit"><i
                        class="ri-calendar-todo-fill"></i> &nbsp;Search Slot</button> 
                        </div>
                       
                        
                        
                       
                       
                       
                        

                    </div>
                </form>
            </div>

            <div class="mt-4 table-responsive" id="">

                <table class="table table-hover table-striped align-middle mb-0 ">
                    <thead>
                        <tr>
                            <th scope="col">S.No.</th>
                            <th class="text-nowrap" scope="col">Booked By</th>
                            <th class="text-nowrap" scope="col">Booked At</th>
                            <th scope="col">Subject</th>
                            {{-- <th scope="col">Meeting ID</th> --}}
                            <th scope="col">Date</th>
                            <th scope="col">Slot</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slots as $slot)
                        {{-- @if ($slot->status == 0)
                            <tr style="background-color: rgb(191, 255, 191)">
                            @elseif ($slot->status == 1)
                            <tr style="background-color: rgb(255, 189, 189);">
                            @elseif ($slot->status == 2) --}}
                        <tr>
                            {{-- @endif --}}
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $slot->student_name }}</td>
                            <td>{{ $slot->booked_at ? \Carbon\Carbon::parse($slot->booked_at)->format('d-m-Y h:i A') : '' }}
                            </td>
                            <td>{{ $slot->subject }}</td>
                            <td>{{ $slot->date }}</td>
                            <td>{{ \Carbon\Carbon::parse($slot->slot)->format('h:i A') }}</td>
                            @if ($slot->status == 0)
                            <td class=""><span class="badge bg-success">Available</span></td>
                            @elseif ($slot->status == 1)
                            <td>
                                @if ($slot->is_class_scheduled == 1)
                                <span class="badge bg-primary">Class Scheduled</span>
                                @elseif($slot->is_class_scheduled == 2)
                                <span class="badge bg-danger">Class Completed</span>
                                @else
                                <span class="badge bg-danger">Booked</span>
                                @endif
                            </td>
                            @else
                            <td>Completed</td>
                            @endif

                            @if ($slot->status == 0)
                            <td><button class="btn btn-sm btn-primary"
                                    onclick="openclassmodal('{{ $slot->id }}','{{ $slot->date }}','{{ $slot->slot }}')">Update</button>
                                <button class="btn btn-sm btn-danger"
                                    onclick="warningModal('{{ $slot->id }}')">Delete</button>
                            </td>
                            @else
                            <td>
                                @if ($slot->is_class_scheduled == 1)
                                <a href="/tutor/getclasslist"><button class="btn btn-sm btn-success bookingBtns">Start
                                        Class</button></a>
                            </td>
                            @elseif ($slot->is_class_scheduled == 2)
                            @else
                            <button
                                onclick="newscheduleclassmodal('{{ $slot->id }}','{{ $slot->student_name }}','{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}','{{ \Carbon\Carbon::parse($slot->slot)->format('h:i A') }}')"
                                class="btn btn-sm btn-success bookingBtns"><i class="ri-calendar-todo-fill"></i>
                                &nbsp;Schedule Class</button>
                            <button
                                onclick="modifyclassmodal('{{ $slot->id }}','{{ $slot->student_name }}','{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}','{{ \Carbon\Carbon::parse($slot->slot)->format('h:i A') }}')"
                                class="btn btn-sm btn-warning bookingBtns"><i class="ri-calendar-todo-fill"></i>
                                &nbsp;Modify Class</button>
                            </td>
                            @endif
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{-- {!! $demos->links() !!} --}}
                </div>
                <br>

                {{-- <form action="{{ route('tutor.liveclass.getuser') }}" method="GET">
                @csrf
                <input type="text" id="zuser" name="zuser"><button type="submit" class="success">Submit</button>
                </form> --}}

            </div>
        </div>
        <!-- content-wrapper ends -->


        <!--Student List modal -->
        <div class="modal fade" id="studentlistmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4">Student List</h3>
                        </header>

                        <form action="" method="">
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
                                    <table class="table table-bordered newclass" style="margin: 0%;">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Student Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentlist">

                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                data-dismiss="modal"><span class="fa fa-times"></span> Close</button>



                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- slot modal -->
        <div class="modal fade" id="scheduleclassmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">

                        <header>
                            <h3 class="text-center mb-4">Slot Details</h3>
                        </header>

                        <form action="{{ route('tutor.slots.create') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" id="slotid" name="slotid">
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Date<span style="color:red">*</span></label>
                                    <input type="date" class="form-control" id="classdate" name="classdate">
                                    <span class="text-danger">
                                        @error('classdate')
                                        {{ 'Date is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Time<span style="color:red">*</span></label>
                                    <input type="time" class="form-control" id="classtime" name="classtime">
                                    <span class="text-danger">
                                        @error('classtime')
                                        {{ 'Time is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <label>
                                        <input type="radio" id="repeatOption" name="repeatOption" value="forday"
                                            checked> Apply for one day.
                                    </label>
                                </div>
                                <div class="col-12 col-md-12 mb-3" id="forweekdiv">
                                    <label>
                                        <input type="radio" id="repeatOption" name="repeatOption" value="forweek">
                                        Apply for next 7 days.
                                    </label>
                                </div>
                                <div class="col-12 col-md-12 mb-3" id="formonthdiv">
                                    <label>
                                        <input type="radio" id="repeatOption" name="repeatOption" value="formonth">
                                        Apply for next 30 days.
                                    </label>
                                </div>



                                <!-- Your other form fields here -->

                            </div>

                            <div style="float:right">
                                <button type="button" class="btn btn-sm btn-danger mr-1" data-dismiss="modal"
                                    onclick="closeModal();">Close</button>
                                <button type="submit" class="btn btn-sm btn-success">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--Schedule modal -->
        <div class="modal fade" id="newscheduleclassmodal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4">Schedule New Class</h3>
                        </header>

                        <form action="{{ route('tutor.liveclass.scheduleclass') }}" method="POST">
                            @csrf
                            <div class="row">


                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Student<span style="color:red">*</span></label>
                                    <input type="hidden" class="form-control" id="classslotid" name="classslotid">
                                    <input type="text" class="form-control" id="studentname" name="studentname"
                                        disabled>
                                </div>
                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Start Time<span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="classstarttime" name="classstarttime"
                                        disabled>

                                </div>

                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Duration(minutes)</label>
                                    <input type="tet" class="form-control" id="classduration" name="classduration"
                                        value="60" disabled>
                                </div>
                                <div class="col-12 col-md-6 col-ms-6 mb-3">
                                    <label>Class Password<span style="color:red">*</span></label>
                                    <input type="tet" class="form-control" id="classpassword" name="classpassword"
                                        required>
                                    <span class="text-danger">
                                        @error('classpassword')
                                        {{ 'Class password is required' }}
                                        @enderror
                                    </span>
                                </div>
                            </div>


                            <div style="float:right">
                                <button type="button" class="btn btn-sm btn-danger mr-1 "
                                    onclick="newscheduleclassmodalclose();">Close</button>
                                <button type="submit" id="" class="btn btn-sm btn-success ">Submit</button>

                            </div>



                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--recording warning modal -->
        <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form action="{{ route('tutor.slots.delete') }}" method="POST">
                        @csrf
                        <input id="slotdeleteid" name="slotdeleteid" type="hidden">
                        <div class="modal-body">

                            <header>
                                <h3 class="text-center mb-4 text-danger"><u>Warning!</u></h3>
                            </header>

                            <h4 class="">Are you sure to <span style="color: red"> Delete</span> this slot?</h4>
                            <br>

                            <div id='warningbtn' style="float:right">
                                <button class="btn btn-success btn-sm" type="submit">Delete</button>
                                <button class="btn btn-danger btn-sm" type="button"
                                    onclick="hidewarning()">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="markcompleted" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Recorded Video Link</h5>
                    </div>
                    <form id="change-class-status">
                        <input type="hidden" id="liveclass-id" name="class_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 col-12 col-sm-12">
                                    <input type="text" class="form-control" placeholder="Paste Video Link Here"
                                        name="video_link">
                                </div>
                            </div>
                            <div style="float:right; margin-top:5px">
                                <button class="btn btn-sm btn-success">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- modify booked slots modal -->
        <div class="modal fade" id="modifyclassmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">

                        <header>
                            <h3 class="text-center mb-4">Change Slot Details</h3>
                        </header>

                        <form action="{{ route('tutor.slots.reschedule') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" id="currentslotid" name="currentslotid">
                                <label style="background-color: yellow; color:red">Current Slot Details</label>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Current Slot Date</label>
                                    <input type="text" class="form-control" id="currentslotdate" name="currentslotdate"
                                        disabled>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Current Slot Time</label>
                                    <input type="text" class="form-control" id="currentslottime" name="currentslottime"
                                        disabled>
                                </div>
                                <label style="background-color: yellow; color:red">Updated Slot Details</label>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Updated Slot Date<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="changedslotdate" name="changedslotdate"
                                        required onchange="fetchslotlist()">
                                        <option value="">Select Slot Date</option>
                                        @foreach ($slots as $slot)
                                        <option value="{{ $slot->date }}">{{ $slot->date }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        @error('changedslotdate')
                                        {{ 'Date is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Updated Slot Time<span style="color:red">*</span></label>
                                    <select type="text" class="form-control" id="changedslottime" name="changedslottime"
                                        required>

                                    </select>
                                    <span class="text-danger">
                                        @error('classtime')
                                        {{ 'Time is required' }}
                                        @enderror
                                    </span>
                                </div>




                                <!-- Your other form fields here -->

                            </div>

                            <div style="float:right">
                                <button type="button" class="btn btn-sm btn-danger mr-1" data-dismiss="modal"
                                    onclick="modifyclassmodalclose();">Close</button>
                                <button type="submit" class="btn btn-sm btn-success">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function closeModal() {
        $('#scheduleclassmodal').modal('hide');
    }

    function openMarkModal(class_id) {
        $('#liveclass-id').val(class_id);
        $('#markcompleted').modal('show');
    }

    function openclassmodal(id, date, slot) {
        $('#slotid').val(id);
        $('#classdate').val(date);
        $('#classtime').val(slot);
        $('#forweekdiv').hide();
        $('#formonthdiv').hide();



        $('#scheduleclassmodal').modal('show')

    }

    function openclassmodalcreate(id, date, slot) {
        $('#slotid').val(id);
        $('#classdate').val(date);
        $('#classtime').val(slot);
        $('#forweekdiv').show();
        $('#formonthdiv').show();



        $('#scheduleclassmodal').modal('show')

    }

    function openstudentmodal(id) {
        var classId = $('#classname option:selected').val();
        $("#subject").html('');
        $.ajax({
            url: "{{ url('tutor/batches/students') }}/" + id,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                // console.log(result)
                $('#studentlist').html('');
                $.each(result, function(key, value) {
                    // $("#studentlist").append(value.name);
                });
                var table = "";
                var p = 0;
                for (var i in result) {
                    p++;
                    table += "<tr>";
                    table += "<td hidden>" +
                        result[i].id + "</td>" +
                        "<td>" + p + "</td>" +
                        "<td>" + result[i].name + "</td>";
                    table += "</tr>";
                }

                document.getElementById("studentlist").innerHTML = table;
            }

        });
        // $('#studentlist').val()
        $('#studentlistmodal').modal('show')

    }

    function fetchSubjects() {

        var classId = $('#class option:selected').val();
        $("#subject").html('');
        $("#topic").html('');
        $.ajax({
            url: "{{ url('fetchsubjects') }}",
            type: "POST",
            data: {
                class_id: classId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#subject').html('<option value="">-- Select Type --</option>');
                $.each(result.subjects, function(key, value) {
                    $("#subject").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    function fetchTopics() {

        var subjectId = $('#subject option:selected').val();
        $("#topic").html('');
        $.ajax({
            url: "{{ url('fetchtopics') }}",
            type: "POST",
            data: {
                subject_id: subjectId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#topic').html('<option value="">-- Select Type --</option>');
                $.each(result.topics, function(key, value) {
                    $("#topic").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    function fetchslotlist() {
        var date = $('#changedslotdate option:selected').val();
        $("#changedslottime").html('');
        $.ajax({
            url: "{{ url('fetchslottime') }}",
            type: "POST",
            data: {
                date: date,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#changedslottime').html('<option value="">-- Select Slot Time --</option>');
                $.each(result.times, function(key, value) {
                    var formattedTime = formatTimeToAMPM(value.slot);
                    $("#changedslottime").append('<option value="' + value.id + '">' +
                        formattedTime + '</option>');
                });
            }
        });
    }

    function formatTimeToAMPM(time) {
        var hours = parseInt(time.substr(0, 2));
        var minutes = time.substr(3, 2);
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        var formattedTime = hours + ':' + minutes + ' ' + ampm;
        return formattedTime;
    }


    function newscheduleclassmodal(id, studentname, date, time) {
        document.getElementById('classslotid').value = id;
        document.getElementById('studentname').value = studentname;
        document.getElementById('classstarttime').value = date + ' ' + time;
        $('#newscheduleclassmodal').modal('show');
    }

    function newscheduleclassmodalclose() {
        $('#newscheduleclassmodal').modal('hide');
    }

    function modifyclassmodal(id, studentname, date, time) {
        document.getElementById('currentslotid').value = id;
        document.getElementById('currentslotdate').value = date;
        document.getElementById('currentslottime').value = time;
        $('#modifyclassmodal').modal('show');
    }

    function modifyclassmodalclose() {
        $('#modifyclassmodal').modal('hide');
    }

    function batchbysubject() {

        var subjectId = $('#subject option:selected').val();
        $("#batchid").html('');
        $.ajax({
            url: "{{ url('batchbysubject') }}",
            type: "POST",
            data: {
                subject_id: subjectId,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(result) {
                $('#batchid').html('<option value="">-- Select Type --</option>');
                $.each(result.batches, function(key, value) {
                    $("#batchid").append('<option value="' + value
                        .id + '">' + value.name + '</option>');
                });

            }

        });

    };

    //     function warningModal(link){
    //     document.getElementById('warningbtn').innerHTML = `<a href="${link}"><button class="btn btn-sm btn-success">Ok</button></a>`;
    //     $('#warningModal').modal('show');

    // }
    function warningModal(id) {
        $('#slotdeleteid').val(id);
        $('#warningModal').modal('show');
    }

    function hidewarning() {
        $('#warningModal').modal('hide');
    }
    </script>
    <script>
    $(document).ready(function() {
        $("#change-class-status").submit(function(e) {
            e.preventDefault();
            var class_id = $("#liveclass-id").val();
            const ajaxUrl = "{{ url('tutor/liveclass/completed') }}/" + class_id;
            var formData = $(this).serialize();

            // Send an AJAX request
            $.ajax({
                type: "POST", // Change to the appropriate HTTP method (GET, POST, etc.)
                url: ajaxUrl, // Replace with your server-side endpoint URL
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#markcompleted').modal('hide');

                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, errorMessages) {
                            $.each(errorMessages, function(index, errorMessage) {
                                toastr.error(errorMessage);
                            });
                        });
                    }
                },
            });
        });
    });
    </script>
    <script>
    function searchSlots() {
        // Get the selected date
        const searchDate = document.getElementById('searchDate').value;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        $.ajax({
            url: 'tutor/tutorslotsearch',
            type: 'GET',
            data: {
                date: searchDate,
                _token: csrfToken, // Include CSRF token
            },
            success: function(response) {
                console.log(response);
            },
            error: function(error) {
                console.error('Error fetching slot data:', error);
            }
        });;
    }
    </script>
    <script>
    // Function to set the minimum date and time
    function setMinDateTime() {
        // Get the current date and time
        var currentDateTime = new Date();

        // Get the current year, month, and day
        var currentYear = currentDateTime.getFullYear();
        var currentMonth = ('0' + (currentDateTime.getMonth() + 1)).slice(-2); // Adding 1 because months are zero-based
        var currentDay = ('0' + currentDateTime.getDate()).slice(-2);

        // Format the current date in the required format (YYYY-MM-DD)
        var minDate = currentYear + '-' + currentMonth + '-' + currentDay;

        // Set the minimum date attribute of the input element
        document.getElementById("classdate").min = minDate;

        // Function to set the minimum time if the selected date is today
        function setMinTime() {
            // Get the selected date
            var selectedDate = document.getElementById('classdate').value;

            // If the selected date is the current date, set the minimum time
            if (selectedDate === minDate) {
                // Get the current time
                var currentTime = new Date();
                var hours = currentTime.getHours();
                var minutes = currentTime.getMinutes();

                // Format hours and minutes as two digits
                if (hours < 10) {
                    hours = '0' + hours;
                }
                if (minutes < 10) {
                    minutes = '0' + minutes;
                }

                // Construct the minimum time string
                var minTime = hours + ':' + minutes;

                // Set the minimum attribute of the time input element
                document.getElementById('classtime').min = minTime;
            } else {
                // Remove the minimum attribute if the selected date is not the current date
                document.getElementById('classtime').removeAttribute('min');
            }
        }

        // Add event listener to the date input element
        document.getElementById('classdate').addEventListener('change', setMinTime);

        // Initial call to set the minimum time based on the default date value
        setMinTime();
    }

    // Initial call to set the minimum date and time on page load
    setMinDateTime();
    </script>
    @endsection