@extends('student.layouts.main')
@section('main-section')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- partial -->
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <style>
                .listHeader {
                    display: flex;
                    justify-content: space-between;
                }


            </style>

            <div class="mt-4" id="">


                    <div id="" class="mb-3 listHeader page-title-box">
                        <h3>My Assignments</h3>
                    </div>

                    <form id="payment-search">
                        <div class="row ">
                            <div class="col-md-3 mt-4">
                                <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-4">
                                <select name="subject_name" class="form-control" id="subject">
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-md-3 mt-4">
                                <input type="text" class="form-control" name="topic" placeholder="Enter Topic">
                            </div> --}}



                            <div class="col-md-3 mt-4">
                                <button class="btn  btn-primary" style="float:right"> <span
                                    class="fa fa-search"></span> Search</button>
                            </div>
                        </div>
                    </form>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                            <thead class="">
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Tutor</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Topic</th>
                                    {{-- <th scope="col">Batch</th> --}}
                                    <th scope="col">Assignment Name</th>
                                    <th scope="col">Assignment Description</th>
                                    <th scope="col">Assignment Link</th>
                                    <th scope="col">Assignment Start Date</th>
                                    <th scope="col">Assignment End Date</th>
                                    <th scope="col">View Submission</th>
                                    <th scope="col">Results (Marks)</th>
                                    <th scope="col">Remarks</th>
                                    {{-- <th scope="col">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($assignments as $assignment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $assignment->class }}</td>
                                        <td>{{ $assignment->tutor_name }}</td>
                                        <td>{{ $assignment->subject }}</td>
                                        <td>{{ $assignment->topic }}</td>
                                        {{-- <td>{{ $assignment->batch }}</td> --}}
                                        <td>{{ $assignment->assignment_name }}</td>
                                        <td>{{ $assignment->assignment_description }}</td>
                                        <td><a href="{{ url('uploads/documents/assignments') }}/{{ $assignment->assignment_link }}"
                                                target="_blank"><button class="badge bg-primary">View
                                                    Assignment</button></td>
                                                    <td>{{ \Carbon\Carbon::parse($assignment->assignment_start_date)->format('d-m-Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($assignment->assignment_end_date)->format('d-m-Y') }}</td>

                                        <td>
                                            @php
                                                $isSubmitted = false;
                                                $submissionData = null;
                                                foreach ($submissions as $submission) {
                                                    if ($submission->assignment_id == $assignment->assignment_id) {
                                                        $isSubmitted = true;
                                                        $submissionData = $submission;
                                                        break;
                                                    }
                                                }
                                            @endphp

                                    @if ($isSubmitted)
                                        <a class="btn btn-sm btn-success"
                                            ><span class="fa fa-check"></span> Assignment Submitted</a>
                                    @else
                                    @if (session('usertype') == 'Parent')
                                    <a class="btn btn-sm btn-danger"
                                            > Not Submitted</a>
                                        @else
                                        <button class="btn btn-sm btn-primary"
                                        onclick="openmodal('{{ $assignment->assignment_id }}');" data-toggle="modal"
                                        data-target="#openmodal"><span class="fa fa-cloud-upload"></span> Submit Assignment</button>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($isSubmitted && $submissionData && $submissionData->results !== null)
                                        <span class="badge bg-info" style="font-size: 14px; padding: 8px;">
                                            {{ number_format($submissionData->results, 2) }}
                                        </span>
                                    @elseif ($isSubmitted)
                                        <span class="text-muted">Not Graded Yet</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($isSubmitted && $submissionData && $submissionData->remarks)
                                        <span style="max-width: 200px; display: inline-block;">{{ $submissionData->remarks }}</span>
                                    @elseif ($isSubmitted)
                                        <span class="text-muted">No remarks</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach


                            </tbody>

                        </table>
                    </div>
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {!! $assignments->links() !!}
                    </div>

                </table>
                <div class="d-flex justify-content-center">
                    {{-- {!! $demos->links() !!} --}}
                </div>


            </div>
        </div>
        <!-- content-wrapper ends -->

        <!-- modal -->
        <div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4" id="header">Upload Assignment</h3>
                        </header>

                        <form action="{{ route('student.assignments.upload') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id">
                            <div class="row">

                                <div class="col-12 col-md-12 col-ms-6 mb-3">
                                    <label>Upload Assignment<span style="color:red">*</span></label>
                                    <input type="file" class="form-control" id="assigupload" name="assigupload">
                                    <span class="text-danger">
                                        @error('assigupload')
                                            {{ 'Assignment is required' }}
                                        @enderror
                                    </span>
                                </div>
                                <div class="col-12 col-md-12 col-ms-6 mb-3">
                                    <label>Remarks</label>
                                    <textarea type="text" class="form-control" id="remarks" name="remarks" placeholder="Enter remarks..."></textarea>
                                    <span class="text-danger">
                                        @error('remarks')
                                            {{ 'Remarks is required' }}
                                        @enderror
                                    </span>
                                </div>

                            </div>


                            <button type="submit" id="" class="btn btn-sm btn-success float-right"><span
                                    class="fa fa-upload"> </span> Upload</button>

                        </div>



                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openmodal(id) {
                $('#header').html('Upload Assignment');
                $('#id').val(id);
                $("#openmodal").modal('show');
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
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function updateTableAndPagination(data) {
                // $('#tableContainer').html(data.table);
                $('.users-table tbody').html(data.table);
                $('#paginationContainer').html(data.pagination);
            }

            $(document).ready(function () {
                $('#payment-search').submit(function (e) {
                    e.preventDefault();
                    const page = 1;
                    const ajaxUrl = '{{ route("student.assignments.search") }}'
                    var formData = $(this).serialize();

                    formData += `&page=${page}`;

                    $.ajax({
                        type: 'post',
                        url: ajaxUrl, // Define your route here
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },

                        success: function (data) {
                            // console.log(data)
                            updateTableAndPagination(data);
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });

                });


                $(document).on('click', '#paginationContainer .pagination a', function (e) {
                e.preventDefault();
                var formData = $('#payment-search').serialize();
                const page = $(this).attr('href').split('page=')[1];
                formData += `&page=${page}`;
                $.ajax({
                    type: 'post',
                    url: '{{ route("student.assignments.search") }}', // Define your route here
                    data:formData,
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    success: function (data) {
                        updateTableAndPagination(data);
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });



            });
        </script>
    @endsection
