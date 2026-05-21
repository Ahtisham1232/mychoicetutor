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
                    <h3>Test Response </h3>
                </div>
                <form id="payment-search">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 ">
                            <label>Class</label>
                            <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>Subject</label>
                            <select name="subject_name" class="form-control" id="subject" onchange="fetchTopics()">
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>Topic</label>
                            <input type="text" class="form-control" name="topic_name" id="topicid"
                                placeholder="Enter Topic">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>Test Name</label>
                            <input type="text" class="form-control" name="test_name" id="test_name"
                                placeholder="Enter Test Name">
                        </div>
                    </div>

                    <div class="row">
                        {{-- 
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date">
                        </div> 
                        --}}

                        <div class="col-md-3 mt-4 d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-primary px-4" style="margin-right: 16px;">
                                <span class="fa fa-search"></span> Search
                            </button>

                            <a href="{{ route('admin.onlinetests.responses.list') }}" class="btn btn-secondary px-4">
                                <span class="fa fa-refresh"></span> Reset
                            </a>
                        </div>
                    </div>

                </form>
                <hr>

                <table class="table table-hover table-striped align-middle mb-0  users-table">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Test Name</th>
                            <th scope="col">Class</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Topic</th>

                            <th scope="col">Test Date</th>
                            <th scope="col">Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($onlineTests)
                            @foreach ($onlineTests as $test)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->topic_name }}</td>
                                    <td>{{ $test->class_name }}</td>
                                    <td>{{ $test->sub_name }}</td>

                                    <td>{{ $test->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($test->test_start_date)->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ url('tutor/onlinetests/responses') }}/{{ $test->id }}"><button
                                                class="btn btn-sm btn-primary">View</button></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
            <!-- content-wrapper ends -->
            <script>
                function changestatus(id, status) {

                    var url = "{{ URL('admin/question/status') }}";
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
                                window.location = "/admin/questionbank";
                            } else {
                                alert("Something went wrong. Please try again later");
                            }

                        }
                    });
                }
            </script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function changestatus(id, status) {

                    var url = "{{ URL('admin/question/status') }}";
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
                                window.location = "/admin/questionbank";
                            } else {
                                alert("Something went wrong. Please try again later");
                            }

                        }
                    });

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

                function fetchTopics() {
                    var subjectId = $('#subject option:selected').val();
                    $("#topicid").html('');
                    $.ajax({
                        url: "{{ url('fetchtopics') }}",
                        type: "POST",
                        data: {
                            subject_id: subjectId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result)
                            $('#topicid').html('<option value="">-- Select Topic --</option>');
                            $.each(result.topics, function(key, value) {
                                $("#topicid").append('<option value="' + value
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
