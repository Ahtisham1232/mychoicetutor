@extends('admin.layouts.main')
@section('main-section')
    <!-- partial -->
    <div class="main-content">
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            .subject-text {
                max-width: 220px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                display: inline-block;
            }
        </style>
        <div class="page-content">
            <div class="container-fluid">
                @if (Session::has('success'))
                    <div class="alert alert-success mb-5">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger mb-5">{{ Session::get('fail') }}</div>
                @endif

                <form method="GET" action="{{ url('admin/subject') }}">
                    <div class="row mb-3">

                        {{-- Grade Filter --}}
                        <div class="col-md-3">
                            <select name="class_id" class="form-control">
                                <option value="">-- Select Grade --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subject Search --}}
                        <div class="col-md-3">
                            <input type="text" name="subject_name" class="form-control" placeholder="Search Subject..."
                                value="{{ request('subject_name') }}">
                        </div>

                        {{-- Status Filter --}}
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">-- Select Status --</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>

                            <a href="{{ route ('admin.subject') }}" class="btn btn-danger">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

                <div class="mb-3 listHeader page-title-box mt-4">
                    <h3>List Of Subjects</h3>
                    <button class="btn btn-sm btn-primary" onclick="openmodal();"> <span class="fa fa-plus"></span> New
                        Subject</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Grade</th>
                                <th scope="col">Subject</th>
                                {{-- <th scope="col">Category</th> --}}
                                <th scope="col">Image</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update</th>
                                {{-- <th scope="col">Status</th>
                            <th scope="col">Action</th> --}}
                            </tr>
                        </thead>

                        <tbody name="classbody">
                            @foreach ($subjects as $subject)
                                <tr>

                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $subject->class_name }}</td>
                                    <td title="{{ $subject->subject_name }}">
                                        <span class="subject-text">
                                            {{ $subject->subject_name }}
                                        </span>
                                    </td>
                                    {{-- <td>{{$subject->category}}</td> --}}
                                    <td>
                                        <img src="{{ asset('/images/subjects/' . $subject->subject_image) }}"
                                            width= '50' height='50' class="img img-responsive" />

                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            @if ($subject->subject_status == 1)
                                                <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                            @else
                                                <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                            @endif
                                            <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck1"
                                                onclick="changestatus('{{ $subject->subject_id }}','{{ $subject->subject_status }}');"
                                                class="checkbox" @if ($subject->subject_status == 1) then checked @endif>
                                        </div>
                                    </td>


                                    <td><button type="button" class="btn btn-sm btn-primary"
                                            onclick="edit('{{ $subject->subject_id }}','{{ $subject->class_id }}','{{ $subject->subject_name }}');">Edit
                                            Record</button></td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- content-wrapper ends -->
            <div class="d-flex justify-content-center">
                {!! $subjects->links() !!}
            </div>
        </div>
    </div>


    <!-- modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-body">

                    <h3 class="text-center mb-3"><u>Add New Subject</u></h3>
                    <form action="{{ route('admin.subject.create') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" row">
                            <input type="hidden" id="id" name="id" class="form-group">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="">Class<i style="color:red">*</i></label>
                                    <select type="text" class="form-control" id="classname" name="classname" required>
                                        <option value="">--Select--</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Subject</label>
                                    <input type="text" class="form-control" placeholder="Enter Subject Name"
                                        id="subject" name="subject" required maxlength="200">
                                        <small class="form-text text-muted">Maximum 200 characters allowed.</small>

                                </div>
                            </div>
                            <div class="row">
                                <div class=" col-md-6 col-12 col-sm-6 form-group">
                                    <label class="ml-2">Upload Image<i style="color:red">*</i></label>
                                    <input type="file" class="form-control" id="uploadimage" value="" accept=".jpg,.jpeg,.png,.webp"
                                        name="uploadimage">
                                </div>
                            </div>
                            <span class="text-danger">
                                @error('uploadimage')
                                    {{ $message }}
                                @enderror
                            </span>
                            <div class="img-holder"></div>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-sm btn-danger mr-1 float-right mt-2"
                                data-bs-dismiss="modal"> Close</button>
                            <button type="submit" id="" class="btn btn-sm btn-success float-right mt-2">
                                Submit</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    <script>
        function openmodal() {

            $('#id').val('');
            $('#classname').val('');
            $('#subject').val('');
            $('#addSubjectModal').modal('show');
        }

        function edit(id, classname, subjectname) {
            $('#id').val(id);
            $('#classname').val(classname);
            $('#subject').val(subjectname)
            $('#addSubjectModal').modal('show');
        }

        function changestatus(id, status) {

            var url = "{{ URL('admin/subject/status') }}";
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
                        // window.location = "/admin/subject";
                        toastr.success('status changed')
                        window.location = "{{ URL('admin/subject') }}";
                    } else {
                        alert("Something went wrong. Please try again later");
                    }

                }
            });

        }

        //Reset input file
        $('input[type="file"][name="uploadimage"]').val('');
        //Image preview
        $('input[type="file"][name="uploadimage"]').on('change', function() {

            var img_path = $(this)[0].value;
            var img_holder = $('.img-holder');
            var extension = img_path.substring(img_path.lastIndexOf('.') + 1).toLowerCase();
            if (extension == 'jpeg' || extension == 'jpg' || extension == 'png') {
                if (typeof(FileReader) != 'undefined') {
                    img_holder.empty();
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('<img/>', {
                            'src': e.target.result,
                            'class': 'img-fluid',
                            'style': 'max-width:100px;margin-bottom:10px;'
                        }).appendTo(img_holder);
                    }
                    img_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                } else {
                    $(img_holder).html('This browser does not support FileReader');
                }
            } else {
                $(img_holder).empty();
            }
        });
    </script>
@endsection
