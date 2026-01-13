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
                    <div id="" class="mb-3 listHeader page-title-box">
                        <h3>Learning Contents</h3>
                        <button class="btn btn-sm btn-primary"> <a class="text-white"
                                href="{{ route('admin.addlearningcontents') }}">Add Content</a></button>
                    </div>
                    <form id="payment-search">
                        <div class="row">


                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="class_name" class="form-control" id="classname"
                                        onchange="fetchSubjects()">
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="subject_name" class="form-control" id="subject">
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="topic_name" id="topicid">
                                        <option value="">Select Topic</option>
                                        @foreach ($topics as $topic)
                                            <option value="{{ $topic->topic_name }}">{{ $topic->topic_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select class="form-control" name="status_field" id="class">
                                        <option value="">Select Status</option>
                                        <option value="1">Active</option>
                                        <option value="2">In Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button class="btn btn-primary" style="float:right"> <span class="fa fa-search"></span>
                                        Search</button>
                                    <a href="{{ route('admin.learningcontents') }}" class="btn btn-primary">
                                        Reset
                                        <span class=" fa fa-refresh"></span>

                                    </a>
                                </div>

                            </div>
                        </div>
                    </form>
                    <hr>



                    <div class="mt-4 table-responsive" id="">
                        <table class="table table-hover table-striped align-middlemb-0 users-table">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Class/Grade</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Topic</th>
                                    <th scope="col">Content</th>
                                    <th scope="col">Video</th>
                                    <th scope="col">Blog</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contents as $content)
                                    {{-- @dd($content) --}}
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $content->classname }}</td>
                                        <td>{{ $content->subjectname }}</td>
                                        <td>{{ $content->topic_name }}</td>
                                        <td>
                                            <div class="text-center">{{ $content->content_description }}</div><br>
                                            @if ($content->content_link)
                                                <div class="text-center"><a
                                                        href="{{ url('uploads/documents/learningcontents') }}/{{ $content->content_link }}"
                                                        target="_blank" class="badge bg-primary mt-2 ">View</a></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-center">{{ $content->video_description }}</div><br>
                                            @if ($content->video_link)
                                                <div class="text-center"><a
                                                        href="{{ url('uploads/videos/learningcontents') }}/{{ $content->video_link }}"
                                                        target="_blank" class="badge bg-primary">View</a></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-center">{{ $content->blog_description }}</div><br>
                                            @if ($content->blog_link)
                                                <div class="text-center"><a href="{{ $content->blog_link }}"
                                                        target="_blank" class="badge bg-primary">View</a></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                @if ($content->contentstatus == 1)
                                                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                                @else
                                                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                                @endif
                                                <input class="form-check-input checkbox" type="checkbox" role="switch"
                                                    id="SwitchCheck{{ $content->contentid }}"
                                                    onclick="changestatus('{{ $content->contentid }}','{{ $content->contentstatus }}');"
                                                    @if ($content->contentstatus == 1) checked @endif
                                                    onkeypress="handleKeyPress(event, '{{ $content->contentid }}')"
                                                    onkeydown="handleKeyDown(event, '{{ $content->contentid }}')"
                                                    onkeyup="handleKeyUp(event, '{{ $content->contentid }}')">
                                            </div>
                                        </td>

                                        <td><a href="learningcontents/{{ $content->contentid }}"><button
                                                    class="badge bg-danger">Modify</button></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {!! $contents->links() !!}
                    </div>



                </div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    function changestatus(id, status) {
                        var url = "{{ URL('admin/learningcontents/status') }}";
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
                                    window.location = "/admin/learningcontents";
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
                            const ajaxUrl = '{{ route('admin.learningcontents-search') }}'
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
                                url: '{{ route('admin.learningcontents-search') }}', // Define your route here
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
