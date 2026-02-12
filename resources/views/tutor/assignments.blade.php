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
                    <h3>Assignments </h3>
                    <button class="btn btn-sm btn-success" onclick="openmodal()"> <span
                            class="fa fa-plus"></span> Create
                        Assignment</button>
                </div>
                <div class="mt-4 table-responsive" id="">
                    <table class="table table-hover table-striped align-middlemb-0 ">
                        <thead>
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Student</th>
                                <th scope="col">Topic</th>
                                <th scope="col">Assignment Name</th>
                                <th scope="col">Assignment Link</th>
                                <th scope="col">Status</th>
                                <th scope="col">View Submission</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assignments as $assignment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $assignment->studentname }}</td>
                                    <td>{{ $assignment->topic }}</td>
                                    <td>{{ $assignment->assignment_name }}</td>
                                    <td><a href="{{ url('uploads/documents/assignments') }}/{{ $assignment->assignment_link }}"
                                            target="_blank"><button class="badge bg-success p-2 border-0">View
                                                Assignment</button></td>
                                    <td>
                                        @if($assignment->is_submitted)
                                            <span class="badge bg-success">Submitted</span>
                                        @else
                                            <span class="badge bg-danger">Not Submitted</span>
                                        @endif
                                    </td>

                                    <td><a href="{{ url('tutor/assignments') . '/' . $assignment->assignment_id }}"> <button
                                                class="badge bg-success p-2 border-0">View Submissions</button></td>
                                    <td><button class="btn btn-sm btn-danger"
                                            onclick="editdata('{{ $assignment->assignment_id }}','{{ $assignment->class_id }}','{{ $assignment->subject_id }}','{{ $assignment->batch_id }}','{{ $assignment->topic_id }}','{{ $assignment->assignment_name }}','{{ $assignment->assignment_description }}','{{ $assignment->assignment_start_date }}','{{ $assignment->assignment_end_date }}')">Update</button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
            <!-- content-wrapper ends -->

            <!-- modal -->
            <div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="header">Add New Assignment</h5>
                            <button type="button" class="btn-close" onclick="closemodal();" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">


                            <header>
                                <h3 class="text-center mb-4" id="header">Add New Assignment</h3>
                            </header>

                            <form action="{{ route('tutor.assignments.create') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="id" name="id">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Class/Grade<span style="color:red">*</span></label>
                                        <select type="text" class="form-control" id="class" name="class" required
                                            onchange="fetchSubjects()">
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">
                                            @error('class')
                                                {{ 'Class is required' }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Subject<span style="color:red">*</span></label>
                                        <select type="text" class="form-control" id="subject" name="subject" required
                                            onchange="batchbysubject()">

                                        </select>
                                        <span class="text-danger">
                                            @error('subject')
                                                {{ 'Subject is required' }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Assign To<span style="color:red">*</span></label>
                                        <select type="text" class="form-control" id="studentid" name="studentid"
                                            required>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger">
                                            @error('studentid')
                                                {{ 'Student is required' }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Topic<span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="topic" name="topic" required>
                                        <span class="text-danger">
                                            @error('topic')
                                                {{ 'Topic is required' }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Upload Assignment (max 2MB)<span style="color:red">*</span></label>
                                        <input type="file" class="form-control" id="assigupload" name="assigupload"
                                            onchange="validateFile()">
                                        <span class="text-danger" id="file-error">
                                            @error('assigupload')
                                                {{ 'Assignment is required' }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Assignment Name<span style="color:red">*</span></label>
                                        <input type="text" class="form-control" id="assignname" name="assignname"
                                            required>
                                        <span class="text-danger">
                                            @error('assignname')
                                                {{ 'Name is required' }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-12 col-ms-12 mb-3">
                                        <label>Assignment Description<span style="color:red">*</span></label>
                                        <textarea class="form-control" id="assigndesc" name="assigndesc" required></textarea>
                                        <span class="text-danger">
                                            @error('assigndesc')
                                                {{ 'Description is required' }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Assignment Start Date<span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="assigstartdate"
                                            name="assigstartdate" required>
                                        <span class="text-danger">
                                            @error('assigstartdate')
                                                {{ 'Start date is required' }}
                                            @enderror
                                        </span>
                                    </div>
                                    <div class="col-12 col-md-6 col-ms-6 mb-3">
                                        <label>Assignment End Date<span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="assigenddate" name="assigenddate"
                                            required>
                                        <span class="text-danger">
                                            @error('assigenddate')
                                                {{ 'End date is required' }}
                                            @enderror
                                        </span>
                                    </div>


                                </div>

                                <div style="float:right">


                                    <button type="button" class="btn btn-sm btn-danger mr-1 " onclick="closemodal();"
                                        data-dismiss="modal"><span class="fa fa-times"></span> Close</button>
                                    <button type="submit" id=""
                                        class="btn btn-sm btn-success float-right">Submit</button>



                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <script>
            function openmodal() {
                $('#header').html('Add Assignment');
                $('#id').val('');
                $('#class').val('');
                $('#subject').val('');
                $('#studentname').val('');
                $('#topic').val('');
                $('#assignname').val('');
                $('#assigndesc').val('');
                $('#assigstartdate').val('');
                $('#assigenddate').val('');
                $('#openmodal').modal({
                    backdrop: 'static', // Prevent closing by clicking outside
                    keyboard: false // Prevent closing by pressing Esc
                });
                $("#openmodal").modal('show');

            }

            function closemodal() {
                $("#openmodal").modal('hide');
            }

            function fetchSubjects() {

                var classId = $('#class option:selected').val();
                $("#subject").html('');
                $("#topic").html('');
                $.ajax({
                    url: "{{ url('fetchsubjects') }}",
                    type: "POST",
                    data: {
                        class_id: classId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#subject').html('<option value="">-- Select Type --</option>');
                        $.each(result.subjects, function(key, value) {
                            $("#subject").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });

            };

            function fetchTopics() {

                var subjectId = $('#subject option:selected').val();
                $("#topic").html('');
                $.ajax({
                    url: "{{ url('fetchtopics') }}",
                    type: "POST",
                    data: {
                        subject_id: subjectId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#topic').html('<option value="">-- Select Type --</option>');
                        $.each(result.topics, function(key, value) {
                            $("#topic").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });

            };

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
                        $('#batchid').html('<option value="">-- Select Type --</option>');
                        $.each(result.batches, function(key, value) {
                            $("#batchid").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });

            };

            function editdata(id, classid, subjectid, studentname, topicid, name, description, startdate, enddate) {
                $.ajax({
                    url: "{{ url('fetchsubjects') }}",
                    type: "POST",
                    data: {
                        class_id: classid,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#subject').html('<option value="">-- Select Type --</option>');
                        $.each(result.subjects, function(key, value) {
                            $("#subject").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });
                $.ajax({
                    url: "{{ url('fetchtopics') }}",
                    type: "POST",
                    data: {
                        subject_id: subjectid,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#topic').html('<option value="">-- Select Type --</option>');
                        $.each(result.topics, function(key, value) {
                            $("#topic").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });
                $.ajax({
                    url: "{{ url('batchbysubject') }}",
                    type: "POST",
                    data: {
                        subject_id: subjectid,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#batchid').html('<option value="">-- Select Type --</option>');
                        $.each(result.batches, function(key, value) {
                            $("#batchid").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });

                    }

                });

                $('#header').html('Update Assignment');
                $('#id').val(id);
                $('#class').val(classid);
                // Code to be executed after 1 second
                function delayedFunction() {
                    $('#subject').val(subjectid);
                    $('#batchid').val(batchid);
                    $('#topic').val(topicid);

                }
                $('#assignname').val(name);
                $('#assigndesc').val(description);
                $('#assigstartdate').val(startdate);
                $('#assigenddate').val(enddate);

                // Set the delay to 1000 milliseconds (1 second)
                const delay = 300;

                // Execute the delayedFunction after the specified delay
                setTimeout(delayedFunction, delay);
                $("#openmodal").modal('show');
            };

            function validateFile() {
                const fileInput = document.getElementById('assigupload');
                const filePath = fileInput.value;
                const allowedExtensions = /(\.pdf)$/i;
                const file = fileInput.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                const errorElement = document.getElementById('file-error');

                // Reset previous error message
                errorElement.textContent = '';

                // Check file extension
                if (!allowedExtensions.exec(filePath)) {
                    errorElement.textContent = 'Only .pdf files are allowed';
                    fileInput.value = '';
                    return false;
                }
                // Check file size
                if (file.size > maxSize) {
                    errorElement.textContent = 'File size must not exceed 2MB';
                    fileInput.value = '';
                    return false;
                }

                return true;
            }
        </script>
    @endsection
