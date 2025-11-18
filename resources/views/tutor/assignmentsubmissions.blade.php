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

        <div id="listHeader">
            <h3 class="mb-3">Student's Assignments</h3>

        </div>

        <table class="table table-hover table-striped align-middlemb-0 table-responsive">
            <thead>
                <tr>
                    <th scope="col">S.No.</th>
                    <th scope="col">Assignment</th>
                    {{-- <th scope="col">View Assignment</th> --}}
                    {{-- <th scope="col">Assigned On</th> --}}
                    {{-- <th scope="col">Assignment End Date</th> --}}
                    {{-- <th scope="col">Assigned By</th> --}}
                    <th scope="col">Submitted By Student</th>
                    <th scope="col">Submission Date</th>
                    <th scope="col">View Submission</th>
                    <th scope="col">Results (Marks)</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">Action</th>


                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                    <tr id="row_{{$data->id}}">
                        <td>{{$loop->iteration}}</td>
                        <td><div class="text-center"><b> {{$data->assignment_name}}</b><br><br><a class="badge bg-primary" href ="{{$data->assignment_link}}" target="_blank">View</a></td>
                        <td>{{$data->student_name}}</td>
                        <td>{{$data->submitted_on}}</td>
                        <td>
                            @if (Str::startsWith($data->submission_link, 'http') || Str::startsWith($data->submission_link, 'https') || Str::startsWith($data->submission_link, 'www'))
                            <a href="//{{$data->submission_link}}"
                                target="_blank"><button class="badge bg-primary"><span class="fa fa-search"></span> View Submission</button>
                            @else
                            <a href="{{ url('uploads/documents/assignments') }}/{{$data->submission_link}}"
                                target="_blank"><button class="badge bg-primary"><span class="fa fa-search"></span> View Submission</button>
                            @endif

                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" id="results_{{$data->id}}" 
                                value="{{$data->results ?? ''}}" 
                                placeholder="Enter marks" 
                                step="0.01" 
                                min="0"
                                style="width: 100px;">
                        </td>
                        <td>
                            <textarea class="form-control form-control-sm" id="remarks_{{$data->id}}" 
                                rows="2" 
                                placeholder="Enter remarks"
                                style="min-width: 200px;">{{$data->reamrks ?? ''}}</textarea>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-success" 
                                onclick="saveMarksRemarks({{$data->id}})">
                                <span class="fa fa-save"></span> Save
                            </button>
                        </td>

                    </tr>
                @endforeach

            </tbody>



        </table>




    </div>


<!-- login modal -->

<div class="modal fade" id="popUpVideoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="text-center mt-3">
        <h5 class="modal-title" id="exampleModalLabel"> Sample Video</h5>
    </div>
    <div class="modal-body">
        <iframe width="100%" height="300px" src="https://www.youtube.com/embed/n5FNuytDFFE"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>


    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


    </div>
    <!-- <div class="modal-body">
                <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
            </div> -->
</div>
</div>
</div>

<div class="modal fade" id="studyMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
    <div class="text-center mt-3">
        <h5 class="modal-title" id="exampleModalLabel">Study Content</h5>
    </div>
    <div class="modal-body">
        <iframe width="100%" height="500px"
            src="assets/studyMaterial/10_maths_key_notes_ch_01_real_numbers.pdf"
            title="YouTube video player" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe>


    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>


    </div>
    <!-- <div class="modal-body">
                    <p>Don't have an acocunt? <a onclick="registerModalShow();">Register</a></p>
                </div> -->
    </div>
</div>
</div>

<script>
    function saveMarksRemarks(submissionId) {
        var results = $('#results_' + submissionId).val();
        var remarks = $('#remarks_' + submissionId).val();
        
        // Show loading state
        var saveBtn = $('#row_' + submissionId).find('button');
        var originalText = saveBtn.html();
        saveBtn.prop('disabled', true).html('<span class="fa fa-spinner fa-spin"></span> Saving...');
        
        $.ajax({
            url: "{{ route('tutor.assignments.updateMarksRemarks') }}",
            type: "POST",
            data: {
                id: submissionId,
                results: results,
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert('Marks and remarks saved successfully!');
                    saveBtn.prop('disabled', false).html(originalText);
                } else {
                    alert('Error: ' + (response.message || 'Failed to save marks and remarks'));
                    saveBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Failed to save marks and remarks';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMsg);
                saveBtn.prop('disabled', false).html(originalText);
            }
        });
    }
</script>

@endsection
