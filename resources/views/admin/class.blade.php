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

                <!-- <h3 class="text-center"></h3> -->
                <div class="mb-3 listHeader page-title-box">
                    <h3>List Of Classes</h3>
                    <button class="btn btn-sm btn-primary" onclick="openmodal();"> <span class="fa fa-plus"></span> New
                        Class</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Grade</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update</th>
                                {{-- <th scope="col">Status</th>
                                    <th scope="col">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classes as $class)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $class->name }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            @if ($class->is_active == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="changestatus('{{ $class->id }}','{{ $class->is_active }}');"
                                                @if ($class->is_active == 1) then checked @endif>
                                        </div>
                                    </td>


                                    <td><button type="button" class="btn btn-sm btn-primary"
                                            onclick="edit('{{ $class->id }}','{{ $class->name }}');">Edit
                                            Record</button></td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>


            </div>
            <!-- content-wrapper ends -->
            <div class="d-flex justify-content-center">
                {!! $classes->links() !!}
            </div>

            <!--demo modal -->
            <div class="modal fade" id="newClassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">


                        <div class="modal-body">

                            <header>
                                <h3 class="text-center mb-4">Add New Class</h3>
                            </header>

                            <form action="{{ route('admin.class.create') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" class="form-control" id="id" name="id">
                                    <div class="col-12 col-md-12 col-ms-12 mb-3">
                                        <label>Class<i style="color: red">*</i></label>
                                        <input type="text" class="form-control" id="classname" name="classname"
                                            placeholder="Enter class/grade name" required maxlength="200">
                                        <small class="form-text text-muted">Maximum 200 characters allowed.</small>
                                    </div>
                                </div>

                                <button type="submit" id="" class="btn btn-sm btn-success float-right">
                                    Submit</button>
                                <button type="button" class="btn btn-sm btn-danger mr-1 moveRight"
                                    data-bs-dismiss="modal">Close</button>


                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openmodal() {

                    $('#id').val('');
                    $('#classname').val('');
                    $('#newClassModal').modal('show');
                }

                function edit(id, classname) {
                    $('#id').val(id);
                    $('#classname').val(classname);
                    $('#newClassModal').modal('show');
                }

                function changestatus(id, status) {

                    var url = "{{ URL('admin/class/status') }}";
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

                                toastr.success('status changed')
                                window.location = "{{ URL('admin/class') }}";
                            } else {
                                alert("Something went wrong. Please try again later");
                            }

                        }
                    });

                }
            </script>
        @endsection
