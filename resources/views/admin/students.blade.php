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
                    <h3 class="text-center">Students List</h3>
                </div>

                <form id="payment-search">
                    <div class="row py-3">

                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_name" id="sname"
                                    placeholder="Student Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_mobile" id="smob"
                                    placeholder="Student Mobile">
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
                                    <option value="2">Inactive</option>
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                        <div class="form-group">
                            <button class="btn btn-primary" style="float:right"> <span class="fa fa-search"></span> Search</button>
                            <a class="btn btn-primary" style="float:right" href="{{route('admin.students')}}"> <span class="fa fa-search"></span> Reset</a>
                        </div>
                    </div> --}}
                        <div class="col-md-3">
                            <div class="form-group d-flex gap-2 justify-content-end">

                                <button type="submit" class="btn btn-primary">
                                    <span class="fa fa-search"></span> Search
                                </button>

                                <a href="{{ route('admin.students') }}" class="btn btn-secondary">
                                    Reset
                                </a>

                            </div>
                        </div>
                    </div>
                </form>
                <hr>

                <div class="mt-4 table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 users-table">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Student Name</th>
                                <th scope="col">Student Email</th>
                                <th scope="col">Student Mobile</th>
                                <th scope="col">Registered On</th>
                                <th scope="col">Current Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stdlists as $stdlist)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="studentprofile/{{ $stdlist->student_id }}">{{ $stdlist->student_name }}</a>
                                    </td>
                                    <td>{{ $stdlist->email }}</td>
                                    <td>{{ $stdlist->student_mobile }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stdlist->created_at)->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            @if ($stdlist->student_status == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="changestatus('{{ $stdlist->student_id }}','{{ $stdlist->student_status }}');"
                                                class="checkbox" @if ($stdlist->student_status == 1) checked @endif>
                                        </div>
                                    </td>
                                    <td><button class="btn btn-sm btn-danger"
                                            onclick="showDeleteWarning({{ $stdlist->student_id }});">Delete</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center" id="paginationContainer">
                    {!! $stdlists->links() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- content-wrapper ends -->

    <script>
        function changestatus(id, status) {
            var url = "{{ URL('admin/students/status') }}";
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
                        toastr.success('Status changed');
                        window.location = "{{ URL('admin/students') }}";
                    } else {
                        alert("Something went wrong. Please try again later");
                    }
                }
            });
        }

        function showDeleteWarning(studentId) {
            Swal.fire({
                title: 'Warning!',
                html: "Student once deleted can't be recovered.<br>Still you want to delete?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `{{ URL('admin/studentdelete') }}/${studentId}`;
                }
            });
        }
    </script>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateTableAndPagination(data) {
            $('.users-table tbody').html(data.table);
            $('#paginationContainer').html(data.pagination);
        }

        $(document).ready(function() {
            $('#payment-search').submit(function(e) {
                e.preventDefault();
                const page = 1;
                const ajaxUrl = '{{ route('admin.students-search') }}';
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
                    url: '{{ route('admin.students-search') }}', // Define your route here
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
@endsection
