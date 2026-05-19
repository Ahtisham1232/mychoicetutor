@extends('admin.layouts.main')
@section('main-section')
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
                <div id="listHeader" class="mb-3">
                    <h3>{{ $pagename }}</h3>
                    <a href="{{ route('admin.learningcontents') }}"><button class="btn btn-primary btn-sm"><span
                                class="fa fa-arrow-left"> Back</span></button></a>
                </div>
                <div class="mt-4" id="">
                    <form action="{{ route('admin.learningcontents.create') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" id="contentid" name="contentid" value="{{ $ucontents->id ?? '' }}">
                            <div class="col-md-4 col-sm-4 col-12">
                                <label>Class<i style="color:red">*</i></label>
                                <select class="form-control" id="classid" name="classid" onchange="fetchSubjects();"
                                    value="{{ $ucontents->class_id ?? '' }}">
                                    <option value="">--Select--</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ isset($ucontents) && $ucontents->class_id == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach

                                </select>
                                <span class="text-danger">
                                    @error('classid')
                                        {{ 'Class field is required' }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-4 col-sm-4 col-12">
                                <label>Subject<i style="color:red">*</i></label>
                                <select class="form-control" id="subjectid" name="subjectid">
                                    <option value="">--Select--</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ isset($ucontents) && $ucontents->subject_id == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="text-danger">
                                    @error('subjectid')
                                        {{ 'Subject field is required' }}
                                    @enderror
                                </span>
                            </div>
                            <div class="col-md-4 col-sm-4 col-12">
                                <label>Topic<i style="color:red">*</i></label>
                                {{-- <select class="form-control" id="topicid" name="topic_name">
                                <option value="">--Select--</option>
                            </select> --}}
                                <input type="text" name="topic_name" class="form-control" placeholder="Enter Topic Name"
                                    value="{{ $ucontents->topic_name ?? '' }}">
                                <span class="text-danger">
                                    @error('topic_name')
                                        {{ 'Topic field is required' }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-sm-12 col-12 mb-3">
                                <label>Select Students <small class="text-muted">(Leave empty to show to all
                                        students)</small></label>
                                <select class="form-control student-select" id="student_ids" name="student_ids[]" multiple
                                    style="min-height: 150px; max-height: 200px; overflow-y: auto;">
                                    @if (isset($students))
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                @if (isset($ucontents) && $ucontents->student_ids) @php
                                                    $selectedIds = json_decode($ucontents->student_ids, true);
                                                @endphp
                                                @if (is_array($selectedIds) && in_array($student->id, $selectedIds)) selected @endif
                                                @endif
                                                >{{ $student->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple students.
                                    Leave empty to make content visible to all students.</small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 col-sm-4 col-12 mb-3">
                                <label>Upload Content</label>
                                <input type="file" accept="application/pdf" class="form-control" id="uploadcontent"
                                    name="uploadcontent">

                            </div>
                            <div class="col-md-8 col-sm-8 col-12 mb-3">
                                <label> Content Description</label>
                                <input type="text" class="form-control" placeholder="Content Description"
                                    id="contentdescription" name="contentdescription"
                                    value="{{ $ucontents->content_description ?? '' }}">

                            </div>
                            <div class="col-md-4 col-sm-4 col-12 mb-3">
                                <label>Upload Video</label>
                                <input type="file" accept="video/mp4,video/x-m4v,video/*" class="form-control"
                                    id="uploadvideo" name="uploadvideo">

                            </div>
                            <div class="col-md-8 col-sm-8 col-12 mb-3">
                                <label> Video Description</label>
                                <input type="text" class="form-control" placeholder="Video Description"
                                    id="videodescription" value="{{ $ucontents->video_description ?? '' }}"
                                    name="videodescription">

                            </div>

                            <div class="col-md-4 col-sm-4 col-12 mb-3">
                                <label>Blog Link</label>
                                <input type="text" class="form-control" placeholder="Paste Blog Link Here" id="bloglink"
                                    value="{{ $ucontents->blog_link ?? '' }}" name="bloglink">

                            </div>
                            <div class="col-md-8 col-sm-8 col-12 mb-3">
                                <label> Blog Description</label>
                                <input type="text" class="form-control" placeholder="Video Description"
                                    id="blogdescription" value="{{ $ucontents->blog_description ?? '' }}"
                                    name="blogdescription">

                            </div>

                            <div class="col-md-12 col-sm-12 col-12 text-right">
                                <button type="submit" class="btn btn-sm btn-success"><span class="fa fa-save"></span>
                                    Save</button>
                            </div>
                        </div>
                    </form>





                </div>



            </div>

            <script>
                function fetchSubjects() {

                    var classId = $('#classid option:selected').val();
                    $("#subjectid").html('');
                    $("#topicid").html('');
                    $.ajax({
                        url: "{{ url('fetchsubjects') }}",
                        type: "POST",
                        data: {
                            class_id: classId,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            $('#subjectid').html('<option value="">-- Select Type --</option>');
                            $.each(result.subjects, function(key, value) {
                                $("#subjectid").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });

                        }
                    });
                };

                function fetchTopics() {

                    var subjectId = $('#subjectid option:selected').val();
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
                            $('#topicid').html('<option value="">-- Select Type --</option>');
                            $.each(result.topics, function(key, value) {
                                $("#topicid").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });

                        }
                    });
                };
            </script>
        @endsection
