@extends('parent.layouts.main')
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
                    <div class="alert alert-success">{{Session::get('success')}}</div>
                    @endif
                    @if (Session::has('fail'))
                    <div class="alert alert-danger">{{Session::get('fail')}}</div>
                    @endif

                    <div id="" class="mb-3 listHeader page-title-box">
                        <h3>Demo Classes</h3>
                    </div>

                    <form id="payment-search">
                        <div class="row ">

                            <div class="col-md-2 mt-4">
                                <select  class="form-control" name="tutor" id="tutor">
                                    <option value="">Select Tutor</option>
                                    @foreach ($tutors as $tutor)
                                        <option  value="{{ $tutor->id }}">{{ $tutor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-4">

                                <select name="subject_name" class="form-control" id="subject">
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option  value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-2">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="smob" placeholder="Student Mobile">
                            </div>

                            <div class="col-md-2">
                                <label>End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">
                            </div>
                            <div class="col-md-2 mt-4">
                                <select  class="form-control" name="status" id="ststus">
                                    <option value="">-- Status --</option>
                                    @foreach ($statuses as $status)
                                        <option  value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-2 mt-4">
                                <button class="btn btn-primary" style="float:right"> <span
                                    class="fa fa-search"></span> Search</button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class=" table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                            <thead class=" ">
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Tutor</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Current Status</th>
                                    <th scope="col">Prefer Slot-1</th>
                                    <th scope="col">Prefer Slot-2</th>
                                    <th scope="col">Prefer Slot-3</th>
                                    <th scope="col">Confirm Slot</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($demos as $demo)
                                  <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$demo->tutor}}</td>
                                    <td>{{$demo->class_name}}</td>
                                    <td>{{$demo->subject}}</td>
                                    <td>
                                        @if($demo->status == 1)
                                        <span class="badge bg-info">{{$demo->currentstatus}}</span>
                                        @elseif ($demo->status == 2)
                                        <span class="badge bg-primary">{{$demo->currentstatus}}</span>
                                        @elseif ($demo->status == 3)
                                        <span class="badge bg-success">{{$demo->currentstatus}}</span>
                                        @elseif ($demo->status == 4)
                                        <span class="badge bg-success">{{$demo->currentstatus}}</span>
                                        @elseif ($demo->status == 5)
                                        <span class="badge bg-danger">{{$demo->currentstatus}}</span>

                                        @endif
                                    </td>
                                    <td>{{$demo->slot_1}}</td>
                                    <td>{{$demo->slot_2}}</td>
                                    <td>{{$demo->slot_3}}</td>
                                    <td>{{ $demo->slot_confirmed ? \App\Helpers\TimezoneHelper::formatInUserTz($demo->slot_confirmed, 'd-m-Y h:i A', 'UTC') : '' }}</td>
                                    <td>{{$demo->remarks}}</td>
                                    {{-- <td><a href="{{$demo->demo_link}}">{{$demo->demo_link}}</a></td> --}}

                                    @if ($demo->status == 1)
                                    <td>
                                        {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
                                        <a href="#"><button class="badge bg-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button></a></td>
                                    @elseif ($demo->status == 2)
                                    <td>
                                        {{-- <a href="demoreschedule"><button class="btn btn-sm mr-1 btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button></a> --}}
                                        <a href="#"><button class="badge bg-danger"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button></a></td>
                                    @elseif ($demo->status == 3)
                                    <td>
                                        {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
                                        <a href="#"><button class="badge bg-success"><i class="fa fa-play-circle-o" aria-hidden="true"></i> Join Class</button></a></td>
                                    @else
                                    <td>
                                        {{-- <button class="btn btn-sm mr-1 btn-primary" disabled><i class="fa fa-calendar" aria-hidden="true"></i> Reschedule</button> --}}
                                        <button class="badge" style="background-color: grey"  disabled><i class="fa fa-times" aria-hidden="true"></i> Cancelled</button></td>

                                        @endif


                                </tr>
                                @endforeach
                            </tbody>
                        </table>



                    </div>
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {!! $demos->links() !!}
                    </div>



                </div>
                <!-- content-wrapper ends -->
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
                            // alert('test');
                            const page = 1;
                            const ajaxUrl = '{{ route("student.demolist-search") }}'
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
                            url: '{{ route("student.demolist-search") }}', // Define your route here
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
