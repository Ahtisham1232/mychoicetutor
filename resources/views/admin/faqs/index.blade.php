@extends('admin.layouts.main')
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

            /* Ensures long answers don't break the table layout */
            .text-truncate-custom {
                max-width: 250px;
                display: inline-block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        </style>

        <div class="page-content">
            <div class="container-fluid">
                <div class="mb-3 listHeader page-title-box">
                    <h3>FAQ Listing</h3>
                    <div>
                        <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                            Add New FAQ
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Question</th>
                                <th scope="col">Answer</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($faqs as $faq)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ Str::limit($faq->question, 50) }}</strong></td>
                                    <td>
                                        <span class="text-truncate-custom">
                                            {{ strip_tags($faq->answer) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch text-nowrap">
                                            <label class="form-check-label" for="faqStatus{{ $faq->id }}">
                                                @if ($faq->is_active)
                                                    <i class="ri-checkbox-circle-line align-middle text-success"></i> Active
                                                @else
                                                    <i class="ri-close-circle-line align-middle text-danger"></i> Inactive
                                                @endif
                                            </label>

                                            <input type="checkbox" class="form-check-input" role="switch"
                                                id="faqStatus{{ $faq->id }}" {{ $faq->is_active ? 'checked' : '' }}
                                                onclick="changestatus({{ $faq->id }}, {{ $faq->is_active }})">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <a class="btn btn-sm bg-primary text-white"
                                                href="{{ route('admin.faqs.edit', $faq->id) }}">
                                                <i class="ri-edit-line"></i> View/Update
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $faqs->links() }}
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            function changestatus(id, status) {
                $.ajax({
                    url: "{{ route('admin.faqs.status') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        status: status
                    },
                    success: function(response) {
                        if (response.statusCode === 200) {
                            // Using a simple reload to update the UI icons
                            location.reload();
                        } else {
                            alert("Something went wrong");
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert("Error updating status");
                    }
                });
            }
        </script>

    </div>
@endsection
