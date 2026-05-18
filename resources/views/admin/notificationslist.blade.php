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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ Session::get('success') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (Session::has('fail'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ Session::get('fail') }}

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>Notifications</h3>
                </div>
                <div class=" table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                        <thead class=" ">
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Time</th>
                                <th scope="col">Notification</th>
                                <th scope="col">Action</th>
                                {{-- <th scope="col">From</th> --}}

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifications as $notification)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($notification->created_at)
                                            {{ $notification->created_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }}
                                        @else
                                            ''
                                        @endif
                                    </td>
                                    <td>{{ $notification->notification }}</td>
                                    {{-- <td><a href="/admin/notificationdelete/{{ $notification->id }}"><button type="button"
                                                class="btn btn-sm btn-danger">Delete</button></a></td> --}}
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmModal{{ $notification->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- Delete Confirm Modal -->
                                <div id="deleteConfirmModal{{ $notification->id }}" class="modal fade zoomIn" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Notification</h5>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="mt-2 text-center">

                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                                        colors="primary:#f7b84b,secondary:#f06548"
                                                        style="width:100px;height:100px">
                                                    </lord-icon>

                                                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                                        <h4>Are you sure?</h4>

                                                        <p class="text-muted mx-4 mb-0">
                                                            Are you sure you want to delete this notification?
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">

                                                    <button type="button" class="btn btn-light text-dark w-sm"
                                                        data-bs-dismiss="modal">
                                                        Cancel
                                                    </button>

                                                    <a href="/admin/notificationdelete/{{ $notification->id }}"
                                                        class="btn w-sm btn-danger">
                                                        Delete
                                                    </a>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>



                </div>
                <br>
                <div class="d-flex justify-content-center">
                    {!! $notifications->links() !!}
                </div>



            </div>


            <!-- content-wrapper ends -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function updateTableAndPagination(data) {
                    // $('#tableContainer').html(data.table);
                    $('.users-table tbody').html(data.table);
                    $('#paginationContainer').html(data.pagination);
                }

                $(document).ready(function() {
                    $('#payment-search').submit(function(e) {
                        e.preventDefault();
                        // alert('test');
                        const page = 1;
                        const ajaxUrl = '{{ route('student.demolist-search') }}'
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
                            url: '{{ route('student.demolist-search') }}', // Define your route here
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
