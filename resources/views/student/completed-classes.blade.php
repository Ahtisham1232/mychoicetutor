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
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>Completed Classes</h3>
                </div>



                <form action="{{ route('student.completed-classes-search') }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-md-2 mt-4">
                            <select name="subject_name" class="form-control" id="subject" onchange="batchbysubject()">
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ request('subject_name') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" hidden>
                            <select class="form-control" name="batch" id="batchid">
                                <option value="">Select Batch</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="col-md-2">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date">
                    </div>

                    <div class="col-md-2">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date">
                    </div> --}}

                        {{-- <div class="col-md-4 mt-4">
                            <button class="btn btn-primary" style="float:right">
                                <span class="fa fa-search"></span> Search
                            </button>
                        </div> --}}
                        <div class="col-md-4 mt-4">
                            <div class="d-flex align-items-center">
                                <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill px-4 me-2">
                                    <span class="fa fa-refresh"></span> Reset
                                </a>

                                <button type="submit" class="btn rounded-pill px-4 text-white"
                                    style="background-color: #43518c;">
                                    <span class="fa fa-search"></span> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                        <thead class="thead-dark ">
                            <tr>
                                <th scope="col">S.No.</th>
                                {{-- <th scope="col">Meeting ID</th> --}}
                                {{-- <th scope="col">Status</th> --}}
                                <th scope="col">Tutor</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Topic</th>
                                {{-- <th scope="col">Batch</th> --}}
                                {{-- <th scope="col">Topic</th> --}}
                                <th scope="col">Started At</th>
                                <th scope="col">Completed At</th>
                                <th scope="col">Attendance</th>
                                <th scope="col">Feedback</th>
                                <th scope="col">Recordings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    {{-- <td>{{ $class->meeting_id }}</td> --}}
                                    {{-- <td>{{ $class->status }}</td> --}}
                                    <td>{{ $class->tutor_name }}</td>
                                    <td>{{ $class->subject_name }}</td>
                                    <td>{{ $class->topic_name }}</td>
                                    {{-- <td>{{ $class->batch }}</td> --}}
                                    {{-- <td>{{ $class->topics }}</td> --}}
                                    <td>
                                        {{ $class->started_at ? \Carbon\Carbon::parse($class->started_at)->format('d-m-Y h:i A') : '' }}
                                    </td>
                                    <td>
                                        {{ $class->completed_at ? \Carbon\Carbon::parse($class->completed_at)->format('d-m-Y h:i A') : '' }}
                                    </td>

                                    <td>
                                        @if ($class->student_present == 1)
                                            <span style="color: green">Present</span>
                                        @else
                                            <span style="color: red">Absent</span>
                                        @endif

                                    </td>
                                    <td>
                                        @if ($class->tutor_review > 0)
                                            <span>{{ $class->tutor_review_text }} ({{ $class->tutor_review }} ⭐)</span>
                                        @else
                                            <button class="btn btn-sm btn-success"
                                                onclick="openfeedbackmodal('{{ $class->id }}','{{ $class->subject_id }}','{{ $class->tutor_id }}')"><i
                                                    class="ri-play-circle-line"></i> Add Feedback</button>
                                        @endif

                                    </td>
                                    <td>
                                        @if ($class->recording_link)
                                            @php
                                                $recordingUrl = filter_var($class->recording_link, FILTER_VALIDATE_URL)
                                                    ? $class->recording_link
                                                    : asset('storage/' . $class->recording_link);
                                            @endphp
                                            <a href="{{ $recordingUrl }}" target="_blank">
                                                <button class="btn btn-sm btn-success">
                                                    <i class="ri-play-circle-line"></i> Watch Recording
                                                </button>
                                            </a>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center" id="paginationContainer">
                    {!! $classes->links() !!}
                </div>
                {{-- <form action="{{ route('tutor.liveclass.store') }}" method="POST">
            @csrf
            <input type="text" id="url" name="url" value="{{ url()->full() }}">{{ url()->full('code') }}
            <button type="submit" class="success">Submit</button>
            </form> --}}
                <br>

                {{-- <form action="{{ route('tutor.liveclass.getuser') }}" method="GET">
            @csrf
            <input type="text" id="zuser" name="zuser"><button type="submit" class="success">Submit</button>
            </form> --}}

            </div>
        </div>

    </div>
    <!-- content-wrapper ends -->

    <!-- modal -->
    <div class="modal fade" id="openreviewsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <header>
                        <h3 class="text-center mb-4" id="header">Add Feedback</h3>
                    </header>

                    <form action="{{ route('student.feedback.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="subject_id" name="subject_id">
                        <input type="hidden" id="tutor_id" name="tutor_id">
                        <div class="row">

                            <div class="col-12 col-md-6 mb-3">
                                <label>Rating<span style="color:red">*</span></label>
                                <select type="text" class="form-control" id="rating" name="rating" required>
                                    <option value="0">0</option>
                                    <option value="0.5">0.5</option>
                                    <option value="1">1</option>
                                    <option value="1.5">1.5</option>
                                    <option value="2">2</option>
                                    <option value="2.5">2.5</option>
                                    <option value="3">3</option>
                                    <option value="3.5">3.5</option>
                                    <option value="4">4</option>
                                    <option value="4.5">4.5</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <span class="text-danger">
                                @error('rating')
                                    {{ $message }}
                                @enderror
                            </span>

                            <div class="col-12 col-md-6 mb-3">
                                <label>Comments<span style="color:red">*</span></label>
                                <textarea type="text" class="form-control" id="comments" name="comments" required></textarea>
                            </div>
                            <span class="text-danger">
                                @error('comments')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <!-- Button container -->
                        <div class="d-flex justify-content-between">
                            <button type="button" onclick="closefeedbackmodal();" class="btn btn-sm btn-danger"
                                data-dismiss="modal">
                                <span class="fa fa-times"></span> Close
                            </button>
                            <button type="submit" class="btn btn-sm btn-success">
                                <span class="fa fa-check"></span> Submit
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="playModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-body" id="iframeid">
                </div>
                <p id="link_test"></p>
            </div>
        </div>
    </div>

    <script>
        function openfeedbackmodal(id, subjectid, tutorid) {
            // alert(subjectid)
            $('#id').val(id)
            $('#subject_id').val(subjectid)
            $('#tutor_id').val(tutorid)
            $('#openreviewsmodal').modal('show');


        }

        function closefeedbackmodal() {
            // alert(subjectid)
            $('#openreviewsmodal').modal('hide');


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
                    $('#batchid').html('<option value="">-- Select Batch --</option>');
                    $.each(result.batches, function(key, value) {
                        $("#batchid").append('<option value="' + value
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

        function play(link) {
            // document.getElementById('iframeid').innerHTML = `<iframe src="${link}" width="100%" height="480" allow="autoplay" allowfullscreen ="true"></iframe>`
            // document.getElementById('link_test').innerHTML = `${link}`

            // $('#playModal').modal('show');
            window.location = link;
        }

        $(document).ready(function() {
            $('#payment-search').submit(function(e) {
                e.preventDefault();
                const page = 1;
                const ajaxUrl = '{{ route('student.classes-search') }}'
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
                    url: '{{ route('student.classes-search') }}', // Define your route here
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
