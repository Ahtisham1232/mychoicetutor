@extends('tutor.layouts.main')
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

            .student-select {
                min-height: 150px;
                max-height: 200px;
                overflow-y: auto;
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
                <div id="listHeader" class="mb-3">
                    <h3>{{ $pagename }}</h3>
                    <a href="{{ route('tutor.learningcontents') }}"><button class="btn btn-primary btn-sm"><span
                                class="fa fa-arrow-left"> Back</span></button></a>
                </div>
                <div class="mt-4" id="">
                    <form action="{{ route('tutor.learningcontents.create') }}" method="POST"
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
                                            @if (isset($ucontents) && $ucontents->class_id == $class->id) selected @endif>{{ $class->name }}</option>
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
                                    @if (isset($subjects))
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                @if (isset($ucontents) && $ucontents->subject_id == $subject->id) selected @endif>{{ $subject->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="text-danger">
                                    @error('subjectid')
                                        {{ 'Subject field is required' }}
                                    @enderror
                                </span>
                            </div>

                            <div class="col-md-4 col-sm-4 col-12">
                                <label>Topic<i style="color:red">*</i></label>
                                <input type="text" class="form-control" placeholder="Topic Name" id="topicid"
                                    name="topicid" value="{{ $ucontents->topic_name ?? '' }}">
                                <span class="text-danger">
                                    @error('topicid')
                                        {{ 'Topic field is required' }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 col-sm-12 col-12 mb-3">
                                <label for="student_ids">Assign to Students <small class="text-muted">(Select one or more students)</small></label>
                                <select class="form-control student-select" id="student_ids" name="student_ids[]" multiple>
                                    @foreach ($students as $student)
                                        @php
                                            $selectedIds = isset($ucontents) && $ucontents->student_ids
                                                ? (is_array($ucontents->student_ids) ? $ucontents->student_ids : json_decode($ucontents->student_ids, true))
                                                : [];
                                        @endphp
                                        <option value="{{ $student->id }}"
                                            @if (is_array($selectedIds) && in_array($student->id, $selectedIds)) selected @endif
                                            >{{ $student->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple. Only assigned students will see this under Study Materials.</small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 col-sm-4 col-12 mb-3">
                                <label>Upload Content</label>
                                <input type="file" accept="application/pdf" class="form-control" id="uploadcontent"
                                    name="uploadcontent">
                                @if (isset($ucontents) && $ucontents->content_link)
                                    <small class="text-success">Current file: <a
                                            href="{{ url('uploads/documents/learningcontents') }}/{{ $ucontents->content_link }}"
                                            target="_blank">View</a></small>
                                @endif
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
                                <small class="text-danger">Maximum file size should be 20 MB.</small>
                                @if (isset($ucontents) && $ucontents->video_link)
                                    <small class="text-success">Current file: <a
                                            href="{{ url('uploads/videos/learningcontents') }}/{{ $ucontents->video_link }}"
                                            target="_blank">View</a></small>
                                @endif
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
                                <input type="text" class="form-control" placeholder="Blog Description"
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
        </div>
    </div>
    <script>
        @if (isset($ucontents))
            // Load existing data
            $(document).ready(function() {
                var classId = {{ $ucontents->class_id ?? 'null' }};
                var topicId = {{ $ucontents->topic_id ?? 'null' }};

                if (classId) {
                    $('#classid').val(classId);
                    // Subject is now handled by PHP
                }
            });
        @endif
    </script>
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
                    $('#subjectid').html('<option value="">-- Select Subject --</option>');
                    $.each(result.subjects, function(key, value) {
                        var selected = '';
                        @if (isset($ucontents))
                            if (value.id == {{ $ucontents->subject_id ?? 'null' }}) {
                                selected = 'selected';
                            }
                        @endif
                        $("#subjectid").append('<option value="' + value.id + '" ' + selected + '>' +
                            value.name + '</option>');
                    });
                    @if (isset($ucontents))
                        $('#subjectid').trigger('change');
                    @endif
                }
            });
        };
    </script>
@endsection
