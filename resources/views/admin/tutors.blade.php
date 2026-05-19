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
                    <h3 class="text-center">Tutors List</h3>
                </div>



                <form id="payment-search">
                    <div class="row py-3">

                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tutor_name" id="sname"
                                    placeholder="Tutor Name">

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tutor_mobile" id="smob"
                                    placeholder="Tutor Mobile">

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select class="form-control" name="class_name" id="class">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select class="form-control" name="status_field">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">In Active</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group d-flex gap-2 justify-content-end">

                                <button type="submit" class="btn btn-primary">
                                    <span class="fa fa-search"></span> Search
                                </button>

                                <a href="{{ route('admin.tutors') }}" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>
                        </div>
                    </div>
                </form>
                <p style="color: red">* Partial Tutor Activation based on subject is not available at the moment. Either you
                    can activate tutor for all subjects or inactive</p>
                <hr>

                <div class="mt-4 table-responsive" id="">
                    <table class="table table-hover table-striped align-middle mb-0 users-table">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Tutor Name</th>
                                <th scope="col">Tutor Email</th>
                                <th scope="col">Tutor Mobile</th>
                                <th scope="col">Rate/Hr (£)</th>
                                <th scope="col">Commission/Hr</th>
                                <th scope="col">Check Slots</th>
                                <th scope="col">Current Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ttrlists as $ttrlist)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{-- @if ($ttrlist->subject_name) --}}
                                        <a href="tutorprofile/{{ $ttrlist->tutor_id }}">{{ $ttrlist->tutor_name }}</a>
                                        {{-- @else
                                            {{ $ttrlist->tutor_name }}
                                        @endif --}}
                                    </td>
                                    <td>{{ $ttrlist->tutor_email }}</td>
                                    <td>{{ $ttrlist->tutor_mobile }}</td>
                                    {{-- <td>£ {{ $ttrlist->rate }}</td> --}}
                                    @if ($ttrlist->tutor_status == 1)
                                        <td><a href="#"
                                                onclick="updaterate('{{ $ttrlist->tutor_id }}','{{ $ttrlist->rate }}')">
                                                {{ $ttrlist->rate }} <span class="badge bg-primary ml-3"> Update</span>
                                            </a>
                                        </td>
                                        <td><a href="#"
                                                onclick="updatecommission('{{ $ttrlist->tutor_id }}','{{ $ttrlist->admin_commission }}')">
                                                {{ $ttrlist->admin_commission }}% <span class="badge bg-primary ml-3">
                                                    Update</span> </a></td>
                                    @else
                                        <td>
                                            <p style="color: rgb(164, 164, 164)">{{ $ttrlist->rate }}</p>
                                        </td>
                                        <td>
                                            <p style="color: rgb(164, 164, 164)">{{ $ttrlist->admin_commission }}%</p>
                                        </td>
                                    @endif

                                    <td><a href="tutorslotscheck/{{ $ttrlist->tutor_id }}"><span
                                                class="badge bg-success ml-3">Check Slots</span> </a></td>
                                    {{-- @if ($ttrlist->subject_name) --}}
                                    <td>
                                        <div class="form-check form-switch">
                                            @if ($ttrlist->tutor_status == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input checkbox" type="checkbox" role="switch"
                                                id="SwitchCheck1"
                                                onclick="changestatus('{{ $ttrlist->tutor_id }}','{{ $ttrlist->tutor_status }}');"
                                                @if ($ttrlist->tutor_status == 1) checked @endif>

                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" onclick="deleteTutor('{{ $ttrlist->tutor_id }}')">
                                            <span class="btn btn-sm btn-danger ml-3">Delete</span>
                                        </a>
                                    </td>
                                    {{-- @else
                                        <td class="text-danger">Profile Not Updated</td>
                                        @endif --}}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>

    <!--confirm modal -->
    <div class="modal fade" id="admincommissionmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4">Admin Commission</h3>
                    </header>

                    {{-- <form action="{{ route('admin.demo.confirm') }}" method="POST">
                @csrf --}}
                    <div class="row mb-3">
                        <input type="hidden" id="commissionid" name="commissionid">

                        <div class="col-12 col-md-4 col-ms-12">
                            <label>
                                Commission/Hr <i style="color: red;">*</i>
                            </label>

                            <input type="number" class="form-control" id="commissionperhr" name="commissionperhr"
                                placeholder="0" min="1" max="100" step="0.01"
                                oninput="
                                    if(this.value > 100) this.value = 100;
                                    if(this.value.length > 6) this.value = this.value.slice(0,6);
                                ">

                            <small class="text-muted text-nowrap">
                                Commission must be between 1 and 100
                            </small>

                            <span class="text-danger">
                                @error('commissionperhr')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-12 col-md-6 col-ms-12" style="margin-top:35px">
                            <button type="button" id="" class="btn btn-sm btn-success float-right"
                                onclick="commissionupdate();"><span class="fa fa-check"></span> Update</button>
                            <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                onclick="closecommisionmodal();"><span class="fa fa-times"></span> Close</button>
                        </div>

                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>

    <!--confirm modal -->
    <div class="modal fade" id="adminratemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4">Admin Rate</h3>
                    </header>

                    {{-- <form action="{{ route('admin.demo.confirm') }}" method="POST">
                @csrf --}}
                    <div class="row mb-3">
                        <input type="hidden" id="rateid" name="rateid">

                        <div class="col-12 col-md-4 col-ms-12">
                            <label>Rate/Hr<i style="color: red;">*</i></label>
                            {{-- <input type="text" class="form-control" id="rateperhour" name="rateperhour"
                                placeholder="0" maxlength="5"> --}}
                            <input type="number" class="form-control" id="rateperhour" name="rateperhour"
                                placeholder="0" min="3" max="50" step="0.01"
                                oninput="
                                    if(this.value > 50) this.value = 50;
                                    if(this.value.length > 5) this.value = this.value.slice(0,5);">

                            <small class="text-muted text-nowrap">
                                Rate must be between 3 and 50
                            </small>

                            <span class="text-danger">
                                @error('rateperhour')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-12 col-md-6 col-ms-12" style="margin-top:35px">
                            <button type="button" id="" class="btn btn-sm btn-success float-right"
                                onclick="rateupdate();"><span class="fa fa-check"></span> Update</button>
                            <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                onclick="closeratemodal();"><span class="fa fa-times"></span> Close</button>
                        </div>

                    </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>



    <!-- Tutor Delete Confirmation -->
    <div id="deleteTutorModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>Are you sure?</h4>
                            <p class="text-muted mx-4 mb-0">Are you sure you want to Delete this Tutor?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn btn-light text-dark w-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <a href="#" class="btn w-sm btn-danger" id ="confirmDeleteBtn">Yes, Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function changestatus(id, status) {

            var url = "{{ URL('admin/tutors/status') }}";
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
                        // window.location = "/admin/tutors";
                        toastr.success('status changed')
                        window.location = "{{ URL('admin/tutors') }}";
                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });

        }

        function updatecommission(id, rate) {
            $('#commissionid').val(id);
            $('#commissionperhr').val(rate);
            $('#admincommissionmodal').modal('show')
        }

        function closecommisionmodal() {
            $('#admincommissionmodal').modal('hide')
        }

        function updaterate(id, rate) {
            $('#rateid').val(id);
            $('#rateperhour').val(rate);
            $('#adminratemodal').modal('show')
        }

        function closeratemodal() {
            $('#adminratemodal').modal('hide')
        }

        function commissionupdate() {
            var url = "{{ URL('admin/commission/update') }}";
            var id = $('#commissionid').val()
            var commission = $('#commissionperhr').val()
            $.ajax({
                url: url,
                type: "GET",
                cache: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    commission: commission
                },
                success: function(dataResult) {
                    dataResult = JSON.parse(dataResult);
                    if (dataResult.statusCode) {
                        window.location = "/admin/tutors";
                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });
        }

        function rateupdate() {
            var url = "{{ URL('admin/rate/update') }}";
            var id = $('#rateid').val()
            var rate = $('#rateperhour').val()
            if (rate === '' || isNaN(rate)) {
                alert('Please enter a valid number');
                return;
            }
            if (rate < 3 || rate > 50) {
                alert('Rate must be between 3 and 50');
                return;
            }
            $.ajax({
                url: url,
                type: "GET",
                cache: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    rate: rate
                },
                success: function(dataResult) {
                    dataResult = JSON.parse(dataResult);
                    if (dataResult.statusCode) {
                        window.location = "/admin/tutors";
                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });
        }
    </script>
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
                const ajaxUrl = '{{ route('admin.tutors-search') }}'
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
                    url: '{{ route('admin.tutors-search') }}', // Define your route here
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
    </script>
    <script>
        function deleteTutor(id) {
            var deleteUrl = "tutordelete/" + id;
            $('#confirmDeleteBtn').attr('href', deleteUrl);
            $('#deleteTutorModal').modal('show');
        }
    </script>
@endsection
