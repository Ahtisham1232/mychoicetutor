@extends('tutor.layouts.main')
@section('main-section')
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <div class="page-title-box">
                    <h3 class="text-center">Trial Class </h3>
                </div>
                <form action="{{ route('tutor.demolist-search') }}" method="POST">
                    @csrf
                    <div class="row py-3">

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_name" id="sname"
                                    placeholder="Student Name">


                            </div>
                        </div>
                        <!-- <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="student_mobile" id="smob" placeholder="Student Mobile">

                                        </div>
                                    </div> -->


                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="subject_name" class="form-control" id="subject">
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <select class="form-control" name="status" id="ststus">
                                    <option value="">-- Status --</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="smob"
                                    placeholder="Student Mobile">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" id="smob"
                                    placeholder="Student Mobile">

                            </div>
                        </div>


                        <div class="col-md-6 mt-4">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" style="float:right"> <span
                                        class="fa fa-search"></span>
                                    Search</button>
                            </div>
                        </div>
                    </div>

                </form>
                <hr>




                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Student Name</th>
                                <th scope="col">class</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Current Status</th>
                                <th scope="col">Confirmed Slot</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($demos as $demo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="studentprofile/{{ $demo->student_id }}">{{ $demo->student_name }}</a>
                                    </td>
                                    <td>{{ $demo->class_name }}</td>
                                    <td>{{ $demo->subject }}</td>
                                    <td>
                                        @if ($demo->status == 1)
                                            <span class="badge bg-info">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 2)
                                            <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 3)
                                            <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 4)
                                            <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 5)
                                            <span class="badge bg-danger">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 8)
                                            <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                                        @endif
                                    </td>
                                    {{-- <td>{{ $demo->slot_1 }}</td> --}}
                                    {{-- <td>{{ $demo->slot_2 }}</td> --}}
                                    {{-- <td>{{ $demo->slot_3 }}</td> --}}
                                    <td>{{ $demo->slot_confirmed->timezone('Asia/Karachi')->format('d M Y, h:i A') ?? '' }}</td> 
                                    {{-- <td><a href="{{ $demo->demo_link }}">{{ $demo->demo_link }}</a></td> --}}
                                    <td>{{ $demo->remarks }}</td>
                                    <td>
                                        @if ($demo->status == 1)
                                            <button class="btn btn-sm btn-primary"
                                                onclick="openconfirmmodal({{ $demo->demo_id }});">Confirm</button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="openupdatemodal({{ $demo->demo_id }})">Modify</button>
                                        @endif

                                        @if ($demo->status == 3)
                                            <button class="btn btn-sm btn-primary"
                                                onclick="updatedemostatus({{ $demo->demo_id }})"><i
                                                    class="ri-play-circle-fill"></i> Start Class</button>
                                        @endif
                                        @if ($demo->status == 8)
                                            <button class="btn btn-sm btn-success"
                                                onclick="warningModal('{{ $demo->demo_link }}')"><i
                                                    class="ri-vidicon-fill"></i> Launch Class</button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="endWarningModal('{{ $demo->demo_id }}')"><i
                                                    class="ri-vidicon-fill"></i> End Class</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center" id="paginationContainer">
        {!! $demos->links() !!}
    </div>
    <!-- content-wrapper ends -->

    <!--confirm modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4">Confirm Trial</h3>
                    </header>

                    <form action="{{ route('tutor.demo.confirm') }}" method="POST">
                        @csrf
                        <label>Select Slot to Confirm</label>
                        <div class="row mb-3">
                            <input type="hidden" id="confirmid" name="confirmid">
                            <div class="col-12 col-md-12 col-ms-12 p-2" id="slot1Div">
                                <label class="mr-4 text-secondary">Slot 1</label>
                                <input type="radio" id="slot1" value="" name="slot">&nbsp;<span
                                    id="slot_1"></span>
                            </div>
                            <div class="col-12 col-md-12 col-ms-12 p-2" id="slot2Div">
                                <label class="mr-4 text-secondary">Slot 2</label>
                                <input type="radio" id="slot2" value="" name="slot">&nbsp;<span
                                    id="slot_2"></span>
                            </div>
                            <div class="col-12 col-md-12 col-ms-12 p-2 mb-3" id="slot3Div">
                                <label class="mr-4 text-secondary">Slot 3</label>
                                <input type="radio" id="slot3" value="" name="slot">&nbsp;<span
                                    id="slot_3"></span>
                            </div>
                            <span class="text-danger">
                                @error('slot')
                                    {{ $message }}
                                @enderror
                            </span>


                            <div class="col-12 col-md-12 col-ms-12">
                                <label>Remarks</label>
                                <textarea type="text" class="form-control" id="demoremarks" name="demoremarks" value=""
                                    placeholder="Remarks"></textarea>
                            </div>

                        </div>


                        <div style="float:right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"
                                onclick="closeModal2();">Close</button>
                            <button type="submit" id="confirmBtn" class="btn btn-sm btn-success"><span
                                    class="fa fa-check"></span> Confirm</button>

                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--edit modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4">Change Slot</h3>
                    </header>

                    <form action="{{ route('tutor.demo.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6 col-sm-6 mb-3 ">
                                <input type="hidden" value="" id="demoupdateid" name="demoupdateid">
                                <label>Preferred Slot-1<i style="color: red">*</i></label>
                                <input type="datetime-local" class="form-control" id="slotupdate1" name="slotupdate1">
                                <span class="text-danger">
                                    @error('slotupdate1')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-12 col-md-6 col-sm-6 mb-3 ">
                                <label>Preferred Slot-2</label>
                                <input type="datetime-local" class="form-control" id="slotupdate2" name="slotupdate2">
                                <span class="text-danger">
                                    @error('slotupdate2')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-12 col-md-6 col-sm-6 mb-3 ">
                                <label>Preferred Slot-3</label>
                                <input type="datetime-local" class="form-control" id="slotupdate3" name="slotupdate3">
                                <span class="text-danger">
                                    @error('slotupdate3')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-12 col-md-6 col-sm-6 mb-3 ">
                                <label>Status<i style="color: red">*</i></label>
                                <select class="form-control" id="statusupdate" name="statusupdate"
                                    placeholder="Select Status">
                                    <option value="">--Select--</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach

                                </select>
                                <span class="text-danger">
                                    @error('statusupdate')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div style="float:right">


                            <button type="button" class="btn btn-danger" data-dismiss="modal"
                                onclick="closeModal();">Close</button>
                            <button type="submit" id="" class="btn btn-sm btn-success "><span
                                    class="fa fa-check"></span> Update</button>
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


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4 text-danger"><u>Warning!</u></h3>
                    </header>

                    <h4 class="">Please Make Sure To Start The <span style="color: red"><i
                                class="ri-record-circle-fill"></i> Recording</span></h4>
                    <br>
                    <p class=""><span class="text-primary"><b>Hint To Start Recording :</b></span> <br>Step 1: Start
                        The Class<br>Step 2: Click On 3 vertical dots(<i class="ri-more-2-fill"></i>)<br>Step 3: Manage
                        Recordings<br>Step 4: Start Recording<br>Step 5: Start(Sometimes A Consent Message Will Display)</p>
                    <div id='warningbtn' style="float:right">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end class warning modal -->
    <div class="modal fade" id="endWarningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">

                    <form action="{{ route('tutor.demo.end') }}" method="POST">
                        @csrf
                        <header>
                            <h3 class="text-center mb-4 text-danger"><u>Warning!</u></h3>
                        </header>
                        <input type="hidden" id="demoendid" name="demoendid">
                        <h4 class="">You are about to <span style="color: red">End the session</span></h4>
                        <br>
                        <input type="text" class="form-control" name="demoendremarks" id="demoendremarks"
                            placeholder="Remarks"><br>
                        <div id='warningbtn' style="float:right">
                            <button class="btn btn-danger" type="submit">End Class</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>   
        $(document).ready(function () {

            let isSubmitting = false;

            $('#confirmModal form').on('submit', function (e) {

                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }

                isSubmitting = true;

                $('#confirmBtn')
                    .prop('disabled', true)
                    .html('Please wait...');

                // allow submission once
                return true;
            });

        });


        function closeModal() {
            $('#editModal').modal('hide');
        }

        function closeModal2() {
            $('#confirmModal').modal('hide');
        }

        function openconfirmmodal(id) {
            $.ajax({
                url: "{{ url('tutor/demodetails') }}/" + id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result);
                    result = result[0];

                    // Set the confirm ID
                    $('#confirmid').val(id);

                    // Format and display the slots
                    if (result.slot_1) {
                        $('#slot_1').html(formatDate(result.slot_1)); // Format date
                        $('#slot1').val(result.slot_1);
                        document.getElementById('slot1Div').style.display = 'block'; // Ensure visibility
                    } else {
                        document.getElementById('slot1Div').style.display = 'none'; // Hide if no data
                    }

                    if (result.slot_2) {
                        $('#slot_2').html(formatDate(result.slot_2)); // Format date
                        $('#slot2').val(result.slot_2);
                        document.getElementById('slot2Div').style.display = 'block'; // Ensure visibility
                    } else {
                        document.getElementById('slot2Div').style.display = 'none'; // Hide if no data
                    }

                    if (result.slot_3) {
                        $('#slot_3').html(formatDate(result.slot_3)); // Format date
                        $('#slot3').val(result.slot_3);
                        document.getElementById('slot3Div').style.display = 'block'; // Ensure visibility
                    } else {
                        document.getElementById('slot3Div').style.display = 'none'; // Hide if no data
                    }

                    // Set demo link and remarks
                    $('#demolink').val(result.demo_link);
                    $('#demoremarks').val(result.remarks);

                    // Show the modal
                    $('#confirmModal').modal('show');
                }
            });
        }

        // Helper function to format date into DD-MM-YYYY hh:mm AM/PM
        function formatDate(dateString) {
            const date = new Date(dateString);

            // Extract date components
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
            const year = date.getFullYear();

            // Extract time components
            let hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            // Convert hours to 12-hour format
            hours = hours % 12;
            hours = hours ? hours : 12; // The hour '0' should be '12'

            return `${day}-${month}-${year} ${hours}:${minutes} ${ampm}`;
        }


        function openupdatemodal(id) {
            $.ajax({
                url: "{{ url('tutor/demodetails') }}/" + id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result)
                    result = result[0]
                    $('#demoupdateid').val(id)
                    $('#slotupdate1').val(result.slot_1)
                    $('#slotupdate2').val(result.slot_2)
                    $('#slotupdate3').val(result.slot_3)
                    $('#statusupdate').val(result.status)

                }
            });
            $('#editModal').modal('show')
        }

        function fetchSubjects() {

            var classId = $('#classname option:selected').val();
            $("#subject").html('');
            $.ajax({
                url: "{{ url('fetchsubjects') }}",
                type: "POST",
                data: {
                    class_id: classId,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#subject').html('<option value="">-- Select Subject --</option>');
                    $.each(result.subjects, function(key, value) {
                        $("#subject").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });

                }

            });

        };
    </script>
    <script>
        function updateTableAndPagination(data) {
            // $('#tableContainer').html(data.table);
            $('.users-table tbody').html(data.table);
            $('#paginationContainer').html(data.pagination);
        }

        // $(document).ready(function() {
        //     $('#payment-search').submit(function(e) {
        //         // alert('test');
        //         e.preventDefault();
        //         const page = 1;
        //         const ajaxUrl = '{{ route('tutor.demolist-search') }}'
        //         var formData = $(this).serialize();

        //         formData += `&page=${page}`;

        //         $.ajax({
        //             type: 'post',
        //             url: ajaxUrl, // Define your route here
        //             data: formData,
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },

        //             success: function(data) {
        //                 // console.log(data)
        //                 updateTableAndPagination(data);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.log(xhr.responseText);
        //             }
        //         });

        //     });


        //     $(document).on('click', '#paginationContainer .pagination a', function(e) {
        //         e.preventDefault();
        //         var formData = $('#payment-search').serialize();
        //         const page = $(this).attr('href').split('page=')[1];
        //         formData += `&page=${page}`;
        //         $.ajax({
        //             type: 'post',
        //             url: '{{ route('tutor.demolist-search') }}', // Define your route here
        //             data: formData,
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             },
        //             success: function(data) {
        //                 updateTableAndPagination(data);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.log(xhr.responseText);
        //             }
        //         });
        //     });



        // });

        function updatedemostatus(id) {

            var url = "{{ URL('tutor/demo/status/update') }}";
            // var id=
            $.ajax({
                url: url,
                type: "GET",
                cache: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                },
                success: function(dataResult) {
                    dataResult = JSON.parse(dataResult);
                    if (dataResult.statusCode) {

                        toastr.success('status changed')
                        window.location = "{{ URL('tutor/demolist') }}";

                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });

        }

        function warningModal(link) {
            document.getElementById('warningbtn').innerHTML =
                `<a href="${link}" target="_blank"><button class="btn btn-sm btn-success">Ok</button></a>`;
            $('#warningModal').modal('show');

        }

        function endWarningModal(id) {
            document.getElementById('demoendid').value = id;
            $('#endWarningModal').modal('show');
        }
    </script>
@endsection
