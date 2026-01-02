@extends('student.layouts.main')
@section('main-section')
{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <style>
    .listHeader {
        display: flex;
        justify-content: space-between;
    }
@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blinking-icon {
  animation: blink 1s infinite;
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
                <h3>Scheduled Classes</h3>
               <a href="completed-classes"> <button class="btn btn-primary">Completed Classes</button></a>
            </div>

            <form id="payment-search" action="{{ route("student.classes-search") }}" method="POST">
                @csrf
                <div class="row ">


                    <div class="col-md-2 mt-4">

                        <select name="subject_name" class="form-control" id="subject" onchange="batchbysubject()">
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mt-4" hidden>
                        <select class="form-control" name="batch" id="batchid">
                            <option value="">Select Batch</option>
                            @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>

                    </div>



                    <div class="col-md-2">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="smob"
                            placeholder="Student Mobile">
                    </div>

                    <div class="col-md-2">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">
                    </div>
                    <div class="col-md-2 mt-4">
                        <select class="form-control" name="status" id="class">
                            <option value="">Select Status</option>
                            <option value="completed">Completed</option>
                            <option value="waiting">Waiting</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="Started">Live</option>
                        </select>
                    </div>


                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary" style="float:right"> <span class="fa fa-search"></span>
                            Search</button>
                    </div>
                </div>
            </form>
            <hr>

            <div class=" table-responsive">
                <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                    <thead class="thead-dark ">
                        <tr>
                            <th scope="col">S.No.</th>
                            {{-- <th scope="col">Meeting ID</th> --}}
                            <th scope="col">Status</th>
                            <th scope="col">Subject</th>
                            {{-- <th scope="col">Batch</th> --}}
                            <th scope="col">Topic</th>
                            <th scope="col">Tutor</th>
                            <th scope="col">Start Time</th>
                            {{-- <th scope="col">Duration</th> --}}
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classes as $class)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td>{{ $class->meeting_id }}</td> --}}
                            {{-- <td>{{ $class->status }}</td> --}}
                            <td>
                                @if ($class->status == 'confirmed' || $class->status == 'Confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif ($class->status == 'waiting' || $class->status == 'Waiting')
                                    <span class="badge bg-primary">Waiting Confirmation</span>
                                @elseif ($class->status == 'started' || $class->status == 'Started')
                                    <span class="badge blinking-icon" style="background: red"><i class="ri-record-circle-line "></i> Live..</span>
                                @elseif ($class->status == 'cancelled' || $class->status == 'Cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif ($class->status == 'completed' || $class->status == 'Completed')
                                    <span class="badge bg-success">Completed</span>
                                {{-- @elseif ($liveclasses->status == 8)
                                    <span class="badge bg-primary">{{ $liveclasses->currentstatus }}</span> --}}
                                @endif
                            </td>
                            <td>{{ $class->subjects }}</td>
                            {{-- <td>{{ $class->batch }}</td> --}}
                            <td>{{ $class->topics }}</td>
                            <td>{{ $class->tutor_name }}</td>
                           <td>{{$class->slotdate ? Carbon\Carbon::parse($class->slotdate)->format('d/m/Y') :''}} {{$class->slottime ? Carbon\Carbon::parse($class->slottime)->format('h:i A') :''}}</td>

                            {{-- <td>{{ $class->duration }} min</td> --}}
                            <td>

                                @if ($class->status == 'started' || $class->status == 'Started')

                                {{-- <a href="{{ $class->join_url }}" target="_blank"><button --}}
                                    <button
                                        class="btn btn-sm btn-success" onclick="joinclass('{{$class->class_id}}','{{$class->join_url}}')">Join
                                        Class</button>
                                    {{-- </a> --}}
                                @endif
                                @if ($class->is_completed == 1 || $class->status == 'completed' || $class->status == 'Completed')
                                    @if (session('usertype') != 'Parent')
                                        @if($class->status == 'completed' || $class->status == 'Completed')
                                            <button class="btn btn-sm btn-primary" data-toggle="modal"
                                                data-target="#openreviewsmodal"
                                                onclick="openfeedbackmodal('{{$class->class_id}}','{{$class->subject_id}}','{{$class->tutor_id}}')">
                                                <span class="fa fa-check"></span> Give Feedback
                                            </button>
                                        @endif
                                    @endif
                                    
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
                                @endif
                            </td>
                            {{-- <td><button class="btn btn-sm btn-primary"
                                            onclick="openstudentmodal({{ $liveclass->batch_id }});"><span
                                class="fa fa-search"></span> Start Class</button>
                            </td> --}}
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- <div class="d-flex justify-content-center" id="paginationContainer">
                {!! $classes->links() !!}
            </div> --}}
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

<!-- feedback modal -->
<div class="modal fade" id="openreviewsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">


            <div class="modal-body">


                <header>
                    <h3 class="text-center mb-4" id="header">Add Feedback</h3>
                </header>

                <form action="{{route('student.feedback.submit')}}" method="POST">
                    @csrf
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="subject_id" name="subject_id">
                    <input type="hidden" id="tutor_id" name="tutor_id">
                    <div class="row">

                        <div class="col-12 col-md-12 col-ms-12 mb-3">
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

                        <div class="col-12 col-md-12 col-ms-12 mb-3">
                            <label>Comments<span style="color:red">*</span></label>

                            <textarea class="form-control" id="comments" name="comments" required></textarea>

                        </div>
                        <span class="text-danger">
                            @error('comments')
                            {{ $message }}
                            @enderror
                        </span>

                    </div>
                    <div style="float:right">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"
                            onclick="closeModal();">Close</button>
                        <button type="submit" id="" class="btn btn-sm btn-success "><span class="fa fa-check"></span>
                            Submit</button>
                    </div>

                </form>


            </div>
        </div>
    </div>
</div>


<!-- Button trigger modal -->




<script>
function openfeedbackmodal(id, subjectid, tutorid) {
    $('#id').val(id)
    $('#subject_id').val(subjectid)
    $('#tutor_id').val(tutorid)
    $('#openreviewsmodal').modal('show');


}

function closeModal() {
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

// $(document).ready(function() {
//     $('#payment-search').submit(function(e) {
//         e.preventDefault();
//         const page = 1;
//         const ajaxUrl = '{{ route("student.classes-search") }}'
//         var formData = $(this).serialize();

//         formData += `&page=${page}`;

//         $.ajax({
//             type: 'post',
//             url: ajaxUrl, // Define your route here
//             data: formData,
//             headers: {
//                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//             },

//             success: function(data) {
//                 // console.log(data)
//                 // updateTableAndPagination(data);
//             },
//             error: function(xhr, status, error) {
//                 console.log(xhr.responseText);
//             }
//         });

//     });

// });

function joinclass(id,link) {

var url = "{{ URL('student/liveclass/join/update') }}";
// var id=
$.ajax({
    url: url,
    type: "GET",
    cache: false,
    data: {
        _token: '{{ csrf_token() }}',
        id: id,

    },
    success: function(dataResult) {
        dataResult = JSON.parse(dataResult);
        if (dataResult.statusCode) {

            toastr.success('status changed')
            window.open(link, '_blank');

        } else {
            alert("Something went wrong. Please try again later");
        }

    }
});

}
</script>
@endsection
