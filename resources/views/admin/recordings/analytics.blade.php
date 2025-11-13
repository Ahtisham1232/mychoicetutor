@extends('admin.layouts.main')
@section('main-section')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-title-box">
                <h3 class="mb-3">Recording Analytics</h3>
                <a href="{{ route('admin.recordings') }}" class="btn btn-secondary">Back to Recordings</a>
            </div>

            <!-- Overall Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary bg-soft">
                                        <span class="avatar-title bg-primary rounded-circle font-size-24">
                                            <i class="ri-video-line text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Recordings</p>
                                    <h4 class="mb-0">{{ number_format($totalRecordings) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success bg-soft">
                                        <span class="avatar-title bg-success rounded-circle font-size-24">
                                            <i class="ri-folder-line text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Total Storage</p>
                                    <h4 class="mb-0">{{ number_format($totalSize / 1024 / 1024 / 1024, 2) }} GB</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-info bg-soft">
                                        <span class="avatar-title bg-info rounded-circle font-size-24">
                                            <i class="ri-time-line text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-1">Avg Duration</p>
                                    <h4 class="mb-0">{{ $averageDuration ? round($averageDuration / 60, 1) : 0 }} min</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics per Tutor -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recordings by Tutor</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tutor Name</th>
                                            <th>Total Recordings</th>
                                            <th>Total Duration</th>
                                            <th>Storage Used</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tutorStats as $stat)
                                            <tr>
                                                <td><strong>{{ $stat->tutor_name ?? 'N/A' }}</strong></td>
                                                <td>{{ $stat->total_recordings }}</td>
                                                <td>{{ $stat->total_duration ? round($stat->total_duration / 60, 1) : 0 }} min</td>
                                                <td>{{ $stat->total_size ? number_format($stat->total_size / 1024 / 1024, 2) : 0 }} MB</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No data available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics per Subject -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recordings by Subject</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject Name</th>
                                            <th>Total Recordings</th>
                                            <th>Avg Duration</th>
                                            <th>Storage Used</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subjectStats as $stat)
                                            <tr>
                                                <td><strong>{{ $stat->subject_name ?? 'N/A' }}</strong></td>
                                                <td>{{ $stat->total_recordings }}</td>
                                                <td>{{ $stat->avg_duration ? round($stat->avg_duration / 60, 1) : 0 }} min</td>
                                                <td>{{ $stat->total_size ? number_format($stat->total_size / 1024 / 1024, 2) : 0 }} MB</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No data available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Recordings -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Recordings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tutor</th>
                                            <th>Student</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentRecordings as $recording)
                                            <tr>
                                                <td>{{ $recording->tutor_name ?? 'N/A' }}</td>
                                                <td>{{ $recording->student_name ?? 'N/A' }}</td>
                                                <td>{{ $recording->subject_name ?? 'N/A' }}</td>
                                                <td>{{ $recording->created_at->format('d M Y, h:i A') }}</td>
                                                <td>{{ $recording->duration ?? 0 }} min</td>
                                                <td>
                                                    <a href="{{ route('admin.recordings.view', $recording->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="ri-eye-line"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No recent recordings</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection









