@extends('admin.layouts.main')
@section('main-section')
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <!--==============================================================-->
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
                <!-- <h3 class="text-center"></h3> -->
                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>Blogs</h3>
                    <div class="dropdown">
                        <a href="/admin/blogs/create"> <button class="btn btn-primary" type="button" id="dropdownMenuButton"
                                data-toggle="" aria-haspopup="true" aria-expanded="false">
                                Add New Blog
                            </button>
                        </a>
                    </div>
                </div>

                <table class="table table-hover table-striped align-middlemb-0 table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Image</th>
                            <th scope="col">Banner</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blogs as $blog)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $blog->name }}</td>
                                <td>{!! $blog->description !!}</td>
                                <td><img src="{{ url('/images/blogs/' . $blog->image) }}" width="50px"></td>
                                <td><img src="{{ url('/images/blogs/' . $blog->banner) }}" width="50px"></td>

                                <td>
                                    <div class="form-check form-switch text-nowrap">
                                        <label class="form-check-label">
                                            @if ($blog->is_active)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                        </label>

                                        <input type="checkbox" class="form-check-input" role="switch"
                                            id="blogStatus{{ $blog->id }}" {{ $blog->is_active ? 'checked' : '' }}
                                            onclick="changestatus({{ $blog->id }}, {{ $blog->is_active }})">
                                    </div>

                                </td>


                                <td>
                                    <div class="text-center"><a class="btn btn-sm bg-primary text-white"
                                            href="{{ url('admin/blogs/update') . '/' . $blog->id }}">View/Update</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
        <!-- content-wrapper ends -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function changestatus(id, status) {
                $.ajax({
                    url: "{{ route('admin.blogs.status') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        status: status
                    },
                    success: function(response) {
                        if (response.statusCode === 200) {
                            location.reload();
                        } else {
                            alert("Something went wrong");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Route not found or server error");
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
                    e.preventDefault();
                    const page = 1;
                    const ajaxUrl = "{{ route('admin.questionbank-search') }}"
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
                        url: "{{ route('admin.questionbank-search') }}", // Define your route here
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
