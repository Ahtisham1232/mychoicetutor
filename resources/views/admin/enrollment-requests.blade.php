@extends('admin.layouts.main')
@section('main-section')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div class="mb-3 page-title-box">
                    <h3>Enrollment Requests</h3>
                    <p class="text-muted">Manage student enrollment requests that require admin approval</p>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle table-nowrap mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Student</th>
                                        <th>Tutor</th>
                                        <th>Subject</th>
                                        <th>Class</th>
                                        <th>Classes</th>
                                        <th>Amount</th>
                                        <th>Created Slot Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($enrollmentRequests as $request)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $request->student_name }}</strong><br>
                                                    <small class="text-muted">{{ $request->student_email }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $request->tutor_name }}</td>
                                            <td>{{ $request->subject_name }}</td>
                                            <td>{{ $request->class_name }}</td>
                                            <td>{{ $request->classes_purchased }}</td>
                                            <td>£{{ $request->amount }}</td>
                                            <td>
                                                {{ \App\Helpers\TimezoneHelper::formatInUserTz($request->created_at, 'd M Y, h:i A') }}
                                            </td>

                                            <td>
                                                @if ($request->status == 0)
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($request->status == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($request->status == -1)
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($request->status == 0)
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="approveRequest('{{ $request->transaction_id }}')">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="rejectRequest('{{ $request->transaction_id }}')">
                                                            <i class="fa fa-times"></i> Reject
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Processed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No enrollment requests found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($enrollmentRequests->hasPages())
                            <div class="mt-3">
                                {{ $enrollmentRequests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Enrollment Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="approvalForm" method="POST" action="{{ route('admin.approve-enrollment') }}">
                    @csrf
                    <input type="hidden" name="transaction_id" id="approvalTransactionId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Payment Verification</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_verified" id="paymentVerified"
                                    value="1" required>
                                <label class="form-check-label" for="paymentVerified">
                                    Payment has been verified
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_verified" id="paymentPending"
                                    value="0">
                                <label class="form-check-label" for="paymentPending">
                                    Payment pending verification
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Note:</strong> If payment is verified, the enrollment will be approved and slots will be
                            confirmed. If payment is pending, the request will remain in pending status.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Enrollment Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>
                <form id="rejectionForm" method="POST" action="{{ route('admin.reject-enrollment') }}">
                    @csrf
                    <input type="hidden" name="transaction_id" id="rejectionTransactionId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejectionReason">Reason for Rejection</label>
                            <textarea class="form-control" name="rejection_reason" id="rejectionReason" rows="3" required
                                placeholder="Please provide a reason for rejecting this enrollment request..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> Rejecting this request will release the booked slots and notify the
                            student.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function approveRequest(transactionId) {
            document.getElementById('approvalTransactionId').value = transactionId;
            $('#approvalModal').modal('show');
        }

        function rejectRequest(transactionId) {
            document.getElementById('rejectionTransactionId').value = transactionId;
            $('#rejectionModal').modal('show');
        }
    </script>
@endsection
