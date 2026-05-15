@extends('admin.layouts.main')
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
                    <h3 class="">Slots List</h3>
                </div>
                <form action="{{ route('admin.tutorslotssearch') }}" method="POST">
                    @csrf
                    <div class="row py-3">

                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_name" id="sname"
                                    placeholder="Student Name">


                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_mobile" id="student_mobile"
                                    placeholder="Student Mobile">

                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tutor_name" id="tutor_name"
                                    placeholder="Tutor Name">

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tutor_mobile" id="tutor_mobile"
                                    placeholder="Tutor Mobile">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    placeholder="Student Mobile">

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    placeholder="Student Mobile">

                            </div>
                        </div>

                        <div class="col-md-2 mt-4">
                            <div class="form-group">
                                <select class="form-control" name="status" id="ststus">
                                    <option value="">-- Status --</option>

                                    <option value="1">Booked</option>
                                    <option value="0">Available</option>

                                </select>
                            </div>
                        </div>
   
                        <div class="col-md-6 mt-4">
                            <div class="form-group d-flex gap-2 justify-content-end">

                                <button type="submit" class="btn btn-primary">
                                    <span class="fa fa-search"></span> Search
                                </button>

                                <a href="{{ route('admin.tutorslots') }}" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>
                        </div>
                    </div>

                </form>
                <hr>



                <div class="mt-4 table-responsive" id="">

                    <table class="table table-hover table-striped align-middle mb-0 ">
                        <thead>
                            <tr>
                                <th scope="col">S.No.</th>
                                {{-- <th scope="col">Meeting ID</th> --}}
                                <th scope="col">Tutor</th>
                                <th scope="col">Date</th>
                                <th scope="col">Slot</th>
                                <th scope="col">Status</th>
                                <th scope="col">Booked By</th>
                                <th scope="col">Booked At</th>
                                <th scope="col">Subject</th>
                                {{-- <th scope="col">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slots as $slot)
                                @if ($slot->status == 0)
                                    <tr style="background-color: rgb(191, 255, 191)">
                                    @elseif ($slot->status == 1)
                                    <tr style="background-color: rgb(255, 189, 189);">
                                    @elseif ($slot->status == 2)
                                    <tr>
                                @endif
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $slot->tutor_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($slot->slot)->format('h:i A') }}</td>
                                @if ($slot->status == 0)
                                    <td>Available</td>
                                @elseif ($slot->status == 1)
                                    <td>Booked</td>
                                @else
                                    <td>Completed</td>
                                @endif
                                <td>{{ $slot->student_name }}</td>
                                <td>{{ $slot->booked_at }}</td>
                                <td>{{ $slot->subject }}</td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <br>

                    {{-- <form action="{{ route('tutor.liveclass.getuser') }}" method="GET">
                    @csrf
                    <input type="text" id="zuser" name="zuser"><button type="submit" class="success">Submit</button>
                    </form> --}}

                </div>
            </div>
        </div>
    </div>

    <!-- content-wrapper ends -->

    <!--confirm modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4">Confirm Demo</h3>
                    </header>

                    <form action="{{ route('admin.demo.confirm') }}" method="POST">
                        @csrf
                        <label>Select Slot to Confirm</label>
                        <div class="row mb-3">
                            <input type="hidden" id="confirmid" name="confirmid">
                            <div class="col-12 col-md-12 col-ms-12 p-2">
                                <label class="mr-4 text-secondary">Slot 1</label>
                                <input type="radio" id="slot1" value="" name="slot">&nbsp;<span
                                    id="slot_1"></span>
                            </div>
                            <div class="col-12 col-md-12 col-ms-12 p-2">
                                <label class="mr-4 text-secondary">Slot 2</label>
                                <input type="radio" id="slot2" value="" name="slot">&nbsp;<span
                                    id="slot_2"></span>
                            </div>

                            <div class="col-12 col-md-12 col-ms-12 p-2 mb-3">
                                <label class="mr-4 text-secondary">Slot 3</label>
                                <input type="radio" id="slot3" value="" name="slot">&nbsp;<span
                                    id="slot_3"></span>
                            </div>
                            <span class="text-danger">
                                @error('slot')
                                    {{ $message }}
                                @enderror
                            </span>


                            {{-- <div class="col-12 col-md-12 col-ms-12">
                                    <label>Demo Link<i style="color: red;">*</i></label>
                                    <input type="text" class="form-control" id="demolink" name="demolink"
                                        placeholder="Paste Demo Link Here">
                                    <span class="text-danger">
                                        @error('demolink')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div> --}}
                            <div class="col-12 col-md-12 col-ms-12">
                                <label>Remarks</label>
                                <textarea type="text" class="form-control" id="demoremarks" name="demoremarks" value=""
                                    placeholder="Remarks"></textarea>
                            </div>

                        </div>


                        <button type="submit" id="" class="btn btn-sm btn-success float-right"><span
                                class="fa fa-check"></span> Confirm</button>
                        <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" data-dismiss="modal"><span
                                class="fa fa-times"></span> Close</button>



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

                    <form action="{{ route('admin.demo.update') }}" method="POST">
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

                        <button type="submit" id="" class="btn btn-sm btn-success float-right"><span
                                class="fa fa-check"></span> Update</button>
                        <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" data-dismiss="modal"><span
                                class="fa fa-times"></span> Close</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openconfirmmodal(id) {
            $.ajax({
                url: "{{ url('admin/demodetails') }}/" + id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result)
                    result = result[0]
                    $('#confirmid').val(id)
                    $('#slot_1').html(result.slot_1)
                    $('#slot_2').html(result.slot_2)
                    $('#slot_3').html(result.slot_3)
                    $('#demolink').val(result.demo_link)
                    $('#demoremarks').val(result.remarks)
                    $('#slot1').val(result.slot_1)
                    $('#slot2').val(result.slot_2)
                    $('#slot3').val(result.slot_3)

                }
            });

            $('#confirmModal').modal('show')
        }

        function openupdatemodal(id) {
            $.ajax({
                url: "{{ url('admin/demodetails') }}/" + id,
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateTableAndPagination(data) {
            // $('#tableContainer').html(data.table);
            $('.users-table tbody').html(data.table);
            $('#paginationContainer').html(data.pagination);
        }

        $(document).ready(function() {
            $('#payment-search').submit(function(e) {
                // alert('test');
                e.preventDefault();
                const page = 1;
                const ajaxUrl = '{{ route('admin.demolist-search') }}'
                var formData = $(this).serialize();

                formData += `&page=${page}`;

                $.ajax({
                    type: 'post',
                    url: ajaxUrl, // Define your route here
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function(data) {
                        // console.log(data)
                        updateTableAndPagination(data);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            });


            $(document).on('click', '#paginationContainer .pagination a', function(e) {
                e.preventDefault();
                var formData = $('#payment-search').serialize();
                const page = $(this).attr('href').split('page=')[1];
                formData += `&page=${page}`;
                $.ajax({
                    type: 'post',
                    url: '{{ route('admin.demolist-search') }}', // Define your route here
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        updateTableAndPagination(data);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });



        });

        function updatedemostatus(id) {

            var url = "{{ URL('admin/demo/status/update') }}";
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
                        window.location = "{{ URL('admin/demolist') }}";

                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });

        }
    </script>
@endsection
