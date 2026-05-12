@extends('tutor.layouts.main')
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
                    <h3>Question Bank</h3>
                    <!-- <a class="btn btn-primary" href="{{ route('admin.questionbank.create') }}">Add New Question</a> -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Add New Question
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('tutor.questionbank.create') }}">Objective</a>
                            <a class="dropdown-item"
                                href="{{ route('tutor.questionbank.subjective.create') }}">Subjective</a>

                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middlemb-0 ">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Class</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Topic</th>
                                <th scope="col">Type</th>
                                <th scope="col">Question</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $question)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-nowrap">{{ $question->class }}</td>
                                    <td>{{ $question->subject }}</td>
                                    <td>{{ $question->topic }}</td>
                                    <td>{{ $question->type == 1 ? 'Objective' : 'Subjective' }}</td>
                                    <td>{!! $question->question !!}</td>
                                    {{-- <td><div ><textarea class="form-control">{{$question->question}}</textarea>
                            </div>
                            </td> --}}
                                    <td>
                                        <div class="form-check form-switch text-nowrap">
                                            @if ($question->question_status == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="changestatus('{{ $question->question_id }}','{{ $question->question_status }}');"
                                                class="checkbox" @if ($question->question_status == 1) then checked @endif>
                                        </div>
                                    </td>


                                    <td>
                                        <div class="text-center"><a class="btn btn-sm bg-danger text-white"
                                                href="{{ url('tutor/questionupdate') . '/' . $question->question_id }}">View/Update</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $questions->links() }}
                </div>
            </div>
            <!-- content-wrapper ends -->
            <script>
                function changestatus(id, status) {

                    var url = "{{ URL('tutor/question/status') }}";
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
                                window.location = "{{ URL('tutor/questionbank') }}";
                            } else {
                                alert("Something went wrong. Please try again later");
                            }

                        }
                    });
                }
            </script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
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
