@extends('admin.layouts.main')

@section('main-section')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <div class="mb-3 page-title-box">
                <h3>Contact Messages</h3>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">

                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>IP Address</th>
                            <th>Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($messages as $message)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td><strong>{{ $message->name }}</strong></td>

                                <td>{{ $message->email }}</td>

                                <td>
                                    <span class="text-truncate-custom">
                                        {{ \Illuminate\Support\Str::limit($message->message, 60) }}
                                    </span>
                                </td>

                                <td>{{ $message->ip_address }}</td>

                                <td>{{ $message->created_at->format('d M Y H:i') }}</td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewMessage{{ $message->id }}">
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- VIEW MODAL -->
                            <div class="modal fade" id="viewMessage{{ $message->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Contact Message</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <p><strong>Name:</strong> {{ $message->name }}</p>
                                            <p><strong>Email:</strong> {{ $message->email }}</p>
                                            <p><strong>IP:</strong> {{ $message->ip_address }}</p>
                                            <p><strong>Message:</strong></p>
                                            <p>{{ $message->message }}</p>
                                            <p><strong>Date:</strong> {{ $message->created_at }}</p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $messages->links() }}
            </div>

        </div>
    </div>

</div>

@endsection