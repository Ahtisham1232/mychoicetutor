@extends('student.layouts.main')
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
                <div class="alert alert-light border shadow-sm mb-4" style="border-left: 5px solid #28a745 !important;">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-video-camera text-success me-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <h5 class="mb-1" style="font-size: 1rem; font-weight: 600;">How to Join Your Class</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                Click <strong>'Join Class'</strong> to enter the room. If it doesn't open, please
                                <strong>Allow Pop-ups</strong> in your browser settings. When prompted, select
                                <strong>'Allow'</strong> for your Camera and Microphone.
                            </p>
                        </div>
                    </div>
                </div>

                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>Trial Classes</h3>
                </div>

                <form id="payment-search">
                    <div class="row align-items-end">

                        <div class="col-md-2 mt-2">
                            <label for="tutor">Tutor</label>
                            <select class="form-control" name="tutor" id="tutor">
                                <option value="">Select Tutor</option>
                                @foreach ($tutors as $tutor)
                                    <option value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mt-2">
                            <label for="subject">Subject</label>

                            <select name="subject_name" class="form-control" id="subject">
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mt-2">
                            <label for="status">Status</label>

                            <select class="form-control" name="status" id="ststus">
                                <option value="">-- Status --</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="smob"
                                placeholder="Student Mobile">
                        </div>

                        <div class="col-md-2">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date" id="smob"
                                placeholder="Student Mobile">
                        </div> --}}
                      


                        <div class="col-md-4 mt-2">
                            <div class="d-flex align-items-center">
                                <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill px-4 me-2">
                                    <span class="fa fa-refresh"></span> Reset
                                </a>

                                <button type="submit" class="btn rounded-pill px-4 text-white" style="background-color: #43518c;">
                                    <span class="fa fa-search"></span> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <div class=" table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                        <thead class=" ">
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Tutor</th>
                                <th scope="col">Class</th>
                                <th scope="col">Subject</th>
                                {{-- <th scope="col">Prefer Slot-1</th> --}}
                                {{-- <th scope="col">Prefer Slot-2</th> --}}
                                {{-- <th scope="col">Prefer Slot-3</th> --}}
                                <th scope="col">Confirmed Slot</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Current Status</th>
                                @if (session('usertype') == 'Parent')
                                @else
                                    <th scope="col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($demos as $demo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="tutorprofile/{{ $demo->tutor_id }}">{{ $demo->tutor }}</a></td>
                                    <td>{{ $demo->class_name }}</td>
                                    <td>{{ $demo->subject }}</td>

                                    {{-- <td>{{ $demo->slot_1 }}</td> --}}
                                    {{-- <td>{{ $demo->slot_2 }}</td> --}}
                                    {{-- <td>{{ $demo->slot_3 }}</td> --}}
                                    <td>
                                        @userTz($demo->slot_confirmed)
                                    </td>

                                    <td>{{ $demo->remarks }}</td>
                                    <td>
                                        @if ($demo->status == 1)
                                            <span class="badge bg-info">Waiting for tutor's confirmation</span>
                                        @elseif ($demo->status == 2)
                                            <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 3)
                                            <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 4)
                                            <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                        @elseif ($demo->status == 5)
                                            <span class="badge bg-danger">{{ $demo->currentstatus }}</span>
                                        @endif
                                    </td>
                                    {{-- <td><a href="{{$demo->demo_link}}">{{$demo->demo_link}}</a></td> --}}
                                    @if (session('usertype') == 'Parent')
                                        <td>
                                            {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
                                            <a href="{{ $demo->demo_link }}" target="_blank"><button
                                                    class="badge bg-success"> Join Class</button></a>
                                        </td>
                                    @else
                                        @if ($demo->status == 1)
                                            <td>
                                                {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
                                                <a href="democancel/{{ $demo->demo_id }}"><button class="badge bg-danger" >
                                                        Cancel</button></a>
                                            </td>
                                        @elseif ($demo->status == 2)
                                            <td>
                                                {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
                                                <a href="democancel/{{ $demo->demo_id }}"><button class="badge bg-danger">
                                                        Cancel</button></a>
                                            </td>
                                            @elseif ($demo->status == 8)
                                            <td>
                                                <a href="{{ $demo->demo_link }}" target="_blank"><button
                                                        class="badge bg-success"> Join Class</button></a>
                                            </td>
                                        {{-- @elseif ($demo->status == 8 && $demo->can_join)
                                            <td>
                                                <a href="{{ $demo->demo_link }}" target="_blank">
                                                    <button class="badge bg-success"> Join Class</button>
                                                </a>
                                            </td>
                                        @elseif ($demo->status == 8)
                                            <td>
                                                <button class="badge bg-secondary" disabled>
                                                    Join Expired
                                                </button>
                                            </td> --}}
                                        @else
                                            <td>
                                                {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
                                                <button class="badge" style="background-color: rgb(215, 215, 215)"
                                                    disabled>
                                                    Cancelled</button>
                                            </td>
                                        @endif
                                        <td>
                                            @if ($demo->status == 4)
                                                <a href="enrollnow/{{ $demo->tutor_id }}"><button class="badge bg-success">
                                                        Book Now</button></a>
                                            @endif
                                        </td>
                                    @endif


                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
                <div class="d-flex justify-content-center" id="paginationContainer">
                    {!! $demos->links() !!}
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
