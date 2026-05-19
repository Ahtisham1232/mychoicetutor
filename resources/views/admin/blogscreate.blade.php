@extends('admin.layouts.main')
@section('main-section')
    <style>
        .selectAns {
            border: 1px solid lightgrey;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        input[type='radio'] {
            accent-color: green;
        }

        .ck-powered-by {
            display: none;
        }

        .ck-balloon-panel,
        .ck-powered-by-balloon,
        .ck-balloon-panel_position_border-side_right {
            border: none !important;
        }
    </style>
    <script src="https://cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>

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
                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>{{ $label ?? 'Add New Blog ' }} </h3>
                    <a href="{{ route('admin.blogs.list') }}" class="btn btn-primary">Back To List</a>
                </div>

                <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" id="id" name="id" value="{{ $blog->id ?? '' }}"
                            class="form-group">

                        <div class="col-md-8">
                            <label for="title">Title <i style="color:red">*</i></label>
                            <input type="text" class="form-control" id="title" name="title" required
                                value="{{ $blog->name ?? '' }}">
                            <span class="text-danger">
                                @error('title')
                                    {{ 'Please enter title' }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <label for="description">Description <i style="color:red">*</i></label>
                            <textarea name="editor1" id="editor1">{{ $blog->description ?? '' }}</textarea><br />
                            <span class="text-danger">
                                @error('editor1')
                                    {{ 'Please enter description' }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label for="image">Image <i style="color:red">*</i></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                onchange="previewImage(event)">
                            <div id="imagePreview" class="mt-3" style="max-width: 200px; max-height: 200px;">
                                @if (isset($blog->image))
                                    <img id="preview" src="{{ asset('images/blogs/' . $blog->image) }}"
                                        alt="Image Preview" style="width: 100%; height: auto;" />
                                @else
                                    <img id="preview" src="#" alt="Image Preview"
                                        style="display: none; width: 100%; height: auto;" />
                                @endif
                            </div>
                            <span class="text-danger">
                                @error('image')
                                    {{ 'Please upload an image' }}
                                @enderror
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label for="image">Banner Image <i style="color:red">*</i></label>
                            <input type="file" class="form-control" id="banner" name="banner" accept="image/*"
                                onchange="previewBannerImage(event)">
                            <div id="imageBannerPreview" class="mt-3" style="max-width: 200px; max-height: 200px;">
                                @if (isset($blog->banner))
                                    <img id="bannerpreview" src="{{ asset('images/blogs/' . $blog->banner) }}"
                                        alt="Image Preview" style="width: 100%; height: auto;" />
                                @else
                                    <img id="bannerpreview" src="#" alt="Image Preview"
                                        style="display: none; width: 100%; height: auto;" />
                                @endif
                            </div>
                            <span class="text-danger">
                                @error('image')
                                    {{ 'Please upload an banner' }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 col-sm-12 col-12">
                            <div style="display:flex; justify-content: end;">
                                <button type="submit" id="" class="btn btn-success btn-sm float-right">Save
                                    Blog</button>
                            </div>
                        </div>
                    </div>
                </form>

                <script>
                    function previewImage(event) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var output = document.getElementById('preview');
                            output.src = reader.result;
                            output.style.display = 'block';
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    }

                    function previewBannerImage(event) {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var output = document.getElementById('bannerpreview');
                            output.src = reader.result;
                            output.style.display = 'block';
                        }
                        reader.readAsDataURL(event.target.files[0]);
                    }
                </script>

            </div>
            <!-- content-wrapper ends -->

            <script>
                CKEDITOR.replace('editor1');
            </script>
        @endsection
