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
                <!-- <h3 class="text-center"></h3> -->
                <div id="listHeader" class="mb-3 listHeader page-title-box">
                    <h3>Online Tests</h3>

                    <button class="btn btn-sm btn-success"> <a class="text-white"
                            href="{{ route('tutor.onlinetests.create') }}">Add New Test</a></button>

                </div>

                <!-- <div id="" class="mb-3 listHeader page-title-box">
                        <h3>Question Bank</h3>
                    </div> -->
                <form action="{{ route('tutor.onlinetests-search') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="test_name" id="aname"
                                    placeholder="Test Name">
                            </div>
                        </div>

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
                                <select name="subject_name" class="form-control" id="subject" onchange="fetchTopics()">
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="topic_name" id="topicid"
                                    placeholder="Enter Topic">
                            </div>
                        </div>



                    </div>
                    <div class="row align-items-end">
                        {{-- <div class="col-md-3">
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
                        </div> --}}
                        <div class="col-md-3 mt-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" name="status_field" id="class">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="2">In Active</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-3 mt-4">
                            <div class="form-group d-flex gap-1 justify-content-end">
                                <button class="btn btn-primary" type="submit">
                                    <span class="fa fa-search"></span> Search
                                </button>

                                <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill">
                                    <span class="fa fa-refresh"></span> Reset
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
                <hr>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middlemb-0 ">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Test Description</th>
                                <th scope="col">Class</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Topic</th>
                                <th>Duration(min)</th>
                                <th>Max Attempt</th>
                                {{-- <th>Test Start Date</th> --}}
                                {{-- <th>Test End Date</th> --}}
                                <th>Assign Test</th>
                                <th>Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($testlists as $testlist)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $testlist->test_type == 1 ? 'Objective' : 'Subjective' }}</td>
                                    <td>{{ $testlist->test_name }}</td>
                                    <td>{{ $testlist->test_description }}</td>
                                    <td>{{ $testlist->class_name }}</td>
                                    <td>{{ $testlist->subject_name }}</td>
                                    <td>{{ $testlist->topic_name }}</td>
                                    <td>{{ $testlist->test_duration }}</td>
                                    <td>{{ $testlist->max_attempt }}</td>
                                    {{-- <td>{{ $testlist->test_start_date }}</td> --}}
                                    {{-- <td>{{ $testlist->test_end_date }}</td> --}}
                                    <td><a href="{{ url('tutor/assigntest') }}/{{ $testlist->test_id }}"><button
                                                type="button" class="btn btn-sm btn-success">View/Assign</button></a></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            @if ($testlist->test_status == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="changestatus('{{ $testlist->test_id }}','{{ $testlist->test_status }}');"
                                                class="checkbox" @if ($testlist->test_status == 1) then checked @endif>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-center"><a class="btn btn-sm btn-danger"
                                                href="{{ url('tutor/onlinetests') . '/' . $testlist->test_id }}">Update</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <br>
                <div class="d-flex justify-content-center" id="paginationContainer">
                    {!! $testlists->links() !!}
                </div>

            </div>
            <!--Student List modal -->
            <div class="modal fade" id="studentlistmodal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">


                        <div class="modal-body">


                            <header>
                                <h3 class="text-center mb-4">Student List</h3>
                            </header>

                            <form action="" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-12 col-ms-12 mb-3">
                                        <input type="hidden" id="assigntestid" name="assigntestid">
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
                                                    <th>Select</th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentlist">
                                                {{-- @foreach ($students as $student)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$student->name}}</td>
                                                <td>
                                                    <input type="checkbox" name="selected_students[]" value="{{ $student->id }}">
                                                </td>
                                            </tr>
                                        @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-sm btn-success mr-1 moveRight" data-dismiss="modal"
                                    onclick=""><span class="fa fa-check"></span> Submit</button>

                                <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                    style="margin-right: 5px" data-dismiss="modal" onclick="closeassignmodal()"><span
                                        class="fa fa-times"></span> Close</button>



                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function changestatus(id, status) {

                    var url = "{{ URL('tutor/onlinetest/status') }}";
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
                                window.location = "{{ url('tutor/onlinetestlist') }}";
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
            </script>
            <script>
                function assigntest(id) {
                    document.getElementById('assigntestid').value = id;
                    $('#studentlistmodal').modal('show');
                }

                function closeassignmodal() {
                    $('#studentlistmodal').modal('hide');
                }
            </script>
        @endsection
