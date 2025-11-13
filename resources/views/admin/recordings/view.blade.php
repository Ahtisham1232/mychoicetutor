@extends('admin.layouts.main')
@section('main-section')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif

            <div class="page-title-box">
                <h3 class="mb-3">Recording Preview</h3>
                <a href="{{ route('admin.recordings') }}" class="btn btn-secondary">Back to Recordings</a>
            </div>

            <div class="row">
                <!-- Recording Details -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recording Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Tutor:</th>
                                    <td>{{ $recording->tutor_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Student:</th>
                                    <td>{{ $recording->student_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subject:</th>
                                    <td>{{ $recording->subject_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Class Date:</th>
                                    <td>
                                        @if ($recording->start_time)
                                            {{ \Carbon\Carbon::parse($recording->start_time)->format('d M Y, h:i A') }}
                                        @else
                                            {{ $recording->created_at->format('d M Y, h:i A') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>
                                        @if ($recording->duration)
                                            {{ $recording->duration }} minutes
                                        @elseif ($recording->recording_duration)
                                            {{ round($recording->recording_duration / 60, 2) }} minutes
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>File Size:</th>
                                    <td>
                                        @if ($recording->recording_file_size)
                                            {{ number_format($recording->recording_file_size / 1024 / 1024, 2) }} MB
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if ($recording->recording_status)
                                            <span class="badge bg-{{ $recording->recording_status == 'completed' ? 'success' : ($recording->recording_status == 'processing' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($recording->recording_status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-success">Available</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                <a href="{{ route('admin.recordings.download', $recording->id) }}" class="btn btn-success btn-block">
                                    <i class="ri-download-line"></i> Download Recording
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Player -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recording Preview</h5>
                        </div>
                        <div class="card-body">
                            @if ($recordingUrl)
                                <div class="ratio ratio-16x9">
                                    <video controls class="w-100" style="background: #000;">
                                        <source src="{{ $recordingUrl }}" type="video/mp4">
                                        <source src="{{ $recordingUrl }}" type="video/webm">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                <div class="mt-3">
                                    <p class="text-muted">
                                        <small>If video doesn't play, <a href="{{ $recordingUrl }}" target="_blank">open in new tab</a> or <a href="{{ route('admin.recordings.download', $recording->id) }}">download</a></small>
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    Recording URL not available. Please check the recording link.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection









