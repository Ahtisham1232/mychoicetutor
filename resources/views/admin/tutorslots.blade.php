@extends('admin.layouts.main')
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
            <div id="" class="mb-3 listHeader page-title-box">
                <h3>Slots Booking </h3>
                <form action="{{route('tutor.slots.search')}}" method="GET">
                    @csrf
                {{-- <div style="display: flex">
                   
                    <input type="date" class="form-control" style="width:auto; margin-right:10px" onchange="searchSlots();" id="searchDate" name="searchDate">
                    <button class="btn btn-sm btn-primary" type="submit"><i class="ri-calendar-todo-fill"></i>
                        Search Slot</button> &nbsp;
                        <button class="btn btn-sm btn-success" type="button" onclick="openclassmodal();"><i class="ri-calendar-todo-fill"></i>
                            Create Slot</button>
                        </div> --}}
                    </form>
            </div>

            <div class="mt-4 table-responsive" id="">

                <table class="table table-hover table-striped align-middle mb-0 ">
                    <thead>
                        <tr>
                            <th scope="col">S.No.</th>
                            {{-- <th scope="col">Meeting ID</th> --}}
                            <th scope="col">Date</th>
                            <th scope="col">Slot</th>
                            <th scope="col">Status</th>
                            <th scope="col">Booked By</th>
                            <th scope="col">Booked At</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($slots as $slot)
                            @if($slot->status == 0)
                            <tr style="background-color: rgb(191, 255, 191)">
                            @elseif ($slot->status == 1)
                            <tr style="background-color: rgb(255, 189, 189);">
                            @elseif ($slot->status == 2)
                            <tr>
                            @endif
                                <td>{{$loop->iteration}}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->slot)->format('h:i A') }}</td>
                                @if ($slot->status == 0)
                                <td>Available</td>
                                @elseif ($slot->status == 1)
                                <td>Booked</td>
                                @else
                                <td>Completed</td>
                                @endif
                                <td>{{$slot->student_name}}</td>
                                <td>{{$slot->booked_at}}</td>
                                <td>{{$slot->subject}}</td>
                               
                                <td><button class="btn btn-sm btn-primary" onclick="openclassmodal('{{$slot->id}}','{{$slot->date}}','{{$slot->slot}}')">Update</button>
                                <button class="btn btn-sm btn-danger" onclick="warningModal('{{$slot->id}}')">Delete</button></td>
                                
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


        <!-- Schedule modal -->
        <div class="modal fade" id="scheduleclassmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-body">

                        <header>
                            <h3 class="text-center mb-4">Update Slot</h3>
                        </header>

                        <form action="{{ route('admin.slots.update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" id="slotid" name="slotid">
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Date<span style="color:red">*</span></label>
                                    <input type="date" class="form-control readonly" readonly id="classdate" name="classdate" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <span class="text-danger">
                                        @error('classdate')
                                        {{ 'Date is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label>Time<span style="color:red">*</span></label>
                                    <input type="time" class="form-control readonly" readonly id="classtime" name="classtime">
                                    <span class="text-danger">
                                        @error('classtime')
                                        {{ 'Time is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-lg-12 col-md-12 mb-3">
                                    {{-- <label>Time<span style="color:red">*</span></label> --}}
                                    <input type="checkbox" class="readonly" id="markactive" readonly name="markactive" checked> &nbsp; <label for="markactive">Mark As Available</label><br><i style="color: red">Once marked as available, it will be available to students for booking</i>
                                    
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

        <!--recording warning modal -->
        <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form action="{{route('admin.slots.delete')}}" method="POST">
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
                            <button class="btn btn-danger btn-sm" type="button" onclick="hidewarning()">Cancel</button>
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
                                    <input type="text" class="form-control" placeholder="Paste Video Link Here" name="video_link">
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
        $(document).ready(function () {
            $("#change-class-status").submit(function (e) {
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
                    success: function (response) {
                        toastr.success(response.message);
                        $('#markcompleted').modal('hide');

                    },
                    error: function (xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function (field, errorMessages) {
                                $.each(errorMessages, function (index, errorMessage) {
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
        _token: csrfToken,  // Include CSRF token
    },
    success: function (response) {
        console.log(response);
    },
    error: function (error) {
        console.error('Error fetching slot data:', error);
    }
});;
        }
    </script>
    
    
    
@endsection

