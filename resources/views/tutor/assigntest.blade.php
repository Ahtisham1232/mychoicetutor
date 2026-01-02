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
            @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
            <br>
            @endif
            @if (Session::has('fail'))
            <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            <br>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div id="listHeader" class="mb-3 listHeader page-title-box">
                <h3>Online Test Assignments</h3>
                
                {{-- <button class="btn btn-sm btn-success" onclick="studentlistmodal()"> <a  class="text-white" href="#">Assign New Student</a></button> --}}
                
                <h5>Test Name: {{$testdata->name}}</h5>
            </div>
            <form action="{{route('tutor.assigntestdata')}}" method="POST">
                @csrf
            <div class="row">
                <input type="hidden" id="testid" name="testid" value="{{$test_id}}" required>
                <div class="col-md-3">
                    <select class="form-control" id="student" name="student" required>
                        <option value="">Select Student</option>
                        @foreach ($students as $student)
                        <option value="{{$student->id}}">{{$student->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="datetime-local" id="starttime" name="starttime" required>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="datetime-local" id="endtime" name="endtime" required>
                </div>
                <div class="col-md-3 mt-1">
                    <button class="btn btn-sm btn-success" type="submit">Assign Test</button>
                </div>
            </div>
        </form>
            <!-- <div id="" class="mb-3 listHeader page-title-box">
                <h3>Question Bank</h3>
            </div> -->
            {{-- <form action="{{route('tutor.onlinetests-search')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text"  class="form-control" name="test_name" id="aname" placeholder="Test Name">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="class_name" class="form-control" id="classname" onchange="fetchSubjects()">
                                <option value="">Select Class</option>
                                @isset($classes)
                                    
                                @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="subject_name" class="form-control" id="subject" onchange="fetchTopics()">
                                <option value="">Select Subject</option>
                                @isset($subjects)
                                    
                                @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select class="form-control" name="topic_name" id="topicid">
                                <option value="">Select Topic</option>
                               
                            </select>
                        </div>
                    </div>



                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                        <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="smob" placeholder="Student Mobile">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                        <label>End Date</label>
                            <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">

                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                            <select class="form-control" name="status_field" id="class">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="2">In Active</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                        <button class="btn btn-primary" type="submit" style="float:right"> <span
                            class="fa fa-search"></span> Search</button>
                        </div>
                    </div>
                </div>
            </form> --}}
            <hr>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th>Test Name</th>
                            <th>Student Name</th>
                            {{-- <th scope="col">Class</th> --}}
                            {{-- <th scope="col">Subject</th> --}}
                            {{-- <th scope="col">Topic</th> --}}
                            {{-- <th>Duration(min)</th> --}}
                            {{-- <th>Max Attempt</th> --}}
                            <th>Test Start Date</th>
                            <th>Test End Date</th>
                            {{-- <th>Assign Test</th> --}}
                            <th>Attempted?</th>
                            <th>Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($studentdata as $stdata)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$stdata->test_name}}</td>
                            <td>{{$stdata->student_name}}</td>
                            <td>{{ \Carbon\Carbon::parse($stdata->start_time)->format('d/m/Y h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($stdata->endtime)->format('d/m/Y h:i A') }}</td>
                            <td>
                                @if($stdata->is_attempted == 1)
                                    <span style="color: green">Attempted</span>
                                
                                @else
                                    <span style="color: red">Not Attempted</span>
                                
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    @if($stdata->is_attempted == 1)
                                    @if ($stdata->status == 1)
                                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                    @else
                                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                    @endif
                                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                    class="checkbox" @if ($stdata->status == 1) then checked @endif disabled>
                                    @else

                                    @if ($stdata->status == 1)
                                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                    @else
                                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                    @endif
                                    <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1" onclick="changestatus('{{ $stdata->id }}','{{ $stdata->status }}');"
                                    class="checkbox" @if ($stdata->status == 1) then checked @endif>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($stdata->is_attempted == 1)
                                    
                                <a href="/tutor/exam/report/{{$stdata->id}}"><button class="btn btn-sm btn-success">Report</button></a></td>
                                @else
                                <button onclick="deleteassigntest('{{$stdata->id}}')" class="btn btn-sm btn-danger">Delete</button></td>
                                @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center" id="paginationContainer">
                {{-- {!! $testlists->links() !!} --}}
            </div>

        </div>
         <!--Student List modal -->
         <div class="modal fade" id="studentlistmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">


                 <div class="modal-body">


                     <header>
                         <h3 class="text-center mb-4">Student List</h3>
                     </header>

                     <form action="{{route('tutor.assigntestdata')}}" method="POST">
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
                                        <tr>
                                       @foreach ($students as $student)
                                           <td>{{$loop->iteration}}</td>
                                           <td>{{$student->name}}</td>
                                           <td>
                                            <input type="checkbox" name="selected_students[]" value="{{$student->id}}">
                                        </td>
                                       @endforeach
                                        </tr>
                                    </tbody>
                                 </table>
                             </div>
                         </div>
                         <button type="submit" class="btn btn-sm btn-success mr-1 moveRight"
                             data-dismiss="modal" onclick=""><span class="fa fa-check"></span> Submit</button>
                         
                         <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" style="margin-right: 5px"
                             data-dismiss="modal" onclick="closeassignmodal()"><span class="fa fa-times"></span> Close</button>



                     </form>
                 </div>
             </div>
         </div>
     </div>
     <!--recording warning modal -->
     <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">

             <form action="{{route('tutor.assigntest.delete')}}" method="POST">
                 @csrf
                 <input id="assigntestdeleteid" name="assigntestdeleteid" type="hidden">
             <div class="modal-body">

                 <header>
                     <h3 class="text-center mb-4 text-danger"><u>Warning!</u></h3>
                 </header>

                 <h4 class="">Are you sure to <span style="color: red"> Delete</span> this record?</h4>
                 <p>Once the record is deleted, it cannot be recovered, and any student to whom this test was assigned will also no longer be able to access this test.</p>
                 <br>
                 
                 <div id='warningbtn' style="float:right; margin-bottom:10px">
                     <button class="btn btn-success btn-sm" type="submit">Delete</button>
                     <button class="btn btn-danger btn-sm" type="button" onclick="hidewarning()">Cancel</button>
                 </div>
             </div>
             </form>
         </div>
     </div>
 </div>
        <!-- content-wrapper ends -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function changestatus(id, status) {

                var url = "{{ URL('tutor/onlinetest/assign/status') }}";
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
                            location.reload();
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
            function deleteassigntest($id){
                document.getElementById('assigntestdeleteid').value = $id;
                $('#warningModal').modal('show');
            }
            function hidewarning(){
                document.getElementById('assigntestdeleteid').value = '';
                $('#warningModal').modal('hide');
            }

        </script>
        <script>
        function studentlistmodal(id){
       document.getElementById('assigntestid').value = id;
        $('#studentlistmodal').modal('show');
        }

        function closeassignmodal(){
            $('#studentlistmodal').modal('hide');
        }

        </script>
    @endsection
