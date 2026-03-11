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
            <h3 class="text-center">Trial Class </h3>
            <div class="mt-4" id="">

                <table class="table table-hover table-striped align-middlemb-0 table-responsive">
                    <thead>
                        <tr>
                            <th scope="col">S.No</th>
                            <th scope="col">Class</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Current Status</th>
                            <th scope="col">Date & Time</th>
                            <th scope="col">Demo Link</th>
                            <th scope="col">Remarks</th>
                            {{-- <th scope="col">Change Status</th> --}}
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demos as $demo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                 <td>{{ $demo->classname }}</td>
                                  <td>{{ $demo->subject }}</td>

                                <td>{{ $demo->student_name }}</td>
                                <td>
                                    @if ($demo->status == 1)
                                        <span class="badge bg-info">{{ $demo->currentstatus }}</span>
                                    @elseif ($demo->status == 2)
                                        <span class="badge bg-primary">{{ $demo->currentstatus }}</span>
                                    @elseif ($demo->status == 3)
                                        <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                    @elseif ($demo->status == 4)
                                        <span class="badge bg-success">{{ $demo->currentstatus }}</span>
                                    @elseif ($demo->status == 5)
                                        <span class="badge bg-danger">{{ $demo->currentstatus }}</span>
                                    @endif
                                </td>
                                {{-- <td>{{ $demo->slot_1 }}</td>
                                <td>{{ $demo->slot_2 }}</td>
                                <td>{{ $demo->slot_3 }}</td> --}}
                                <td>{{ $demo->slot_confirmed ? \App\Helpers\TimezoneHelper::formatInUserTz($demo->slot_confirmed, 'd-m-Y h:i A', 'UTC') : '' }}</td>
                                <td><a href="{{ $demo->demo_link }}">{{ $demo->demo_link }}</a></td>
                                <td>{{ $demo->remarks }}</td>
                                <td>
                                    @if ($demo->status == 3)
                                    <button class="badge bg-primary"
                                    onclick="openupdatemodal('{{ $demo->demo_id }}','{{$demo->status}}','{{$demo->remarks}}')">Update</button>

                                @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{-- {!! $demos->links() !!} --}}
                </div>


            </div>
        </div>
        <!-- content-wrapper ends -->

        <!--demo update modal -->
        <div class="modal fade" id="demostatusupdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">


                    <div class="modal-body">


                        <header>
                            <h3 class="text-center mb-4">Update Status</h3>
                        </header>

                        <form action="{{ route('tutor.demo.update') }}" method="POST">
                            @csrf
                            <div class="row">


                                <div class="col-12 col-md-12 col-sm-12 mb-3 ">
                                    <input type="hidden" id="demoid" name="demoid">
                                    <label>Status<i style="color: red">*</i></label>
                                    <select class="form-control" id="statusupdate" name="statusupdate"
                                        placeholder="Select Status">
                                        <option value="">--Select--</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach

                                    </select>
                                    <span class="text-danger">
                                        @error('statusupdate')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                </div>
                                 <div class="col-12 col-md-12 col-sm-12 mb-3 ">
                                    <label>Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks"></textarea>
                                 </div>
                            </div>

                            <button type="submit" id="" class="btn btn-sm btn-success float-right"><span
                                    class="fa fa-check"></span> Update</button>
                            <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                data-dismiss="modal"><span class="fa fa-times"></span> Close</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openconfirmmodal(id) {
                $.ajax({
                    url: "{{ url('admin/demodetails') }}/" + id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log(result)
                        result = result[0]
                        $('#confirmid').val(id)
                        $('#slot_1').html(result.slot_1)
                        $('#slot_2').html(result.slot_2)
                        $('#slot_3').html(result.slot_3)
                        $('#demolink').val(result.demo_link)
                        $('#demoremarks').val(result.remarks)
                        $('#slot1').val(result.slot_1)
                        $('#slot2').val(result.slot_2)
                        $('#slot3').val(result.slot_3)

                    }
                });

                $('#confirmModal').modal('show')
            }

            function openupdatemodal(id,status,remarks) {
                $('#demoid').val(id)
                $('#statusupdate').val(status)
                $('#remarks').val(remarks)
                $('#demostatusupdate').modal('show')
            }
        </script>
    @endsection
