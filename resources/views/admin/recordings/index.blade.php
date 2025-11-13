@extends('admin.layouts.main')
@section('main-section')
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-3">Class Recordings</h3>
                        <a href="{{ route('admin.recordings.analytics') }}" class="btn btn-info">
                            <i class="ri-bar-chart-line"></i> View Analytics
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <form action="{{ route('admin.recordings') }}" method="GET">
                    <div class="row py-3 mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="tutor_name" value="{{ request('tutor_name') }}"
                                    placeholder="Tutor Name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" name="student_name" value="{{ request('student_name') }}"
                                    placeholder="Student Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="subject_id" class="form-control">
                                    <option value="">All Subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}"
                                    placeholder="From Date">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}"
                                    placeholder="To Date">
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('admin.recordings') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Recordings Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tutor Name</th>
                                        <th>Student Name</th>
                                        <th>Subject</th>
                                        <th>Class Date</th>
                                        <th>Duration</th>
                                        <th>File Size</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recordings as $recording)
                                        <tr>
                                            <td>{{ $loop->iteration + ($recordings->currentPage() - 1) * $recordings->perPage() }}</td>
                                            <td>
                                                <strong>{{ $recording->tutor_name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $recording->tutor_mobile ?? '' }}</small>
                                            </td>
                                            <td>
                                                {{ $recording->student_name ?? 'N/A' }}<br>
                                                <small class="text-muted">{{ $recording->student_mobile ?? '' }}</small>
                                            </td>
                                            <td>{{ $recording->subject_name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($recording->start_time)
                                                    {{ \Carbon\Carbon::parse($recording->start_time)->format('d M Y, h:i A') }}
                                                @else
                                                    {{ $recording->created_at->format('d M Y, h:i A') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($recording->duration)
                                                    {{ $recording->duration }} min
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($recording->recording_file_size)
                                                    {{ number_format($recording->recording_file_size / 1024 / 1024, 2) }} MB
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($recording->recording_status)
                                                    <span class="badge bg-{{ $recording->recording_status == 'completed' ? 'success' : ($recording->recording_status == 'processing' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($recording->recording_status) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">Available</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if ($recording->recording_link)
                                                        @if (filter_var($recording->recording_link, FILTER_VALIDATE_URL))
                                                            <a href="{{ $recording->recording_link }}" target="_blank" class="btn btn-sm btn-primary" title="View Recording">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.recordings.view', $recording->id) }}" class="btn btn-sm btn-primary" title="Preview Recording">
                                                                <i class="ri-play-line"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('admin.recordings.download', $recording->id) }}" class="btn btn-sm btn-success" title="Download">
                                                            <i class="ri-download-line"></i>
                                                        </a>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteRecording({{ $recording->id }})" title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No recordings found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $recordings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this recording? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteRecording(id) {
            if (confirm('Are you sure you want to delete this recording? This action cannot be undone.')) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/recordings/' + id;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection

