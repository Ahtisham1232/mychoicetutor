@extends('tutor.layouts.main')
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

    .btns button {
        float: right;
        margin: 2px;
    }
    </style>

    <div class="page-content">
        <div class="container-fluid">
            @if (Session::has('success'))
            <div class="alert alert-success">{{Session::get('success')}}</div>
            @endif
            @if (Session::has('fail'))
            <div class="alert alert-danger">{{Session::get('fail')}}</div>
            @endif
            <div class=" listHeader mb-3 page-title-box">
                <h3>Payouts </h3>
                <!-- <button class="btn btn-sm btn-primary" onclick="openmodal();"> <span class="fa fa-plus"></span> Make
                    Payment</button> -->
            </div>

            <form id="payment-search">
                <div class="row">
                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                            <label>Transaction No.</label>
                            <input type="text" class="form-control" name="tansaction_no" id="tranx" placeholder="Transaction No.">
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="smob" placeholder="Student Mobile">

                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">

                        </div>
                    </div>

                    <div class="col-md-3 mt-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" id="byststus">
                                <option value="">--Status--</option>
                                @foreach ($statuses as $status)
                                <option value="{{$status->id}}">{{$status->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <button class="btn btn-primary" style="float:right"> <span class="fa fa-search"></span>
                                Search</button>
                        </div>
                    </div> --}}

                    <div class="col-md-12 mt-3">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ url()->current() }}" class="btn btn-secondary rounded-pill px-4 me-2">
                                <span class="fa fa-refresh"></span> Reset
                            </a>
                            
                            <button type="submit" class="btn rounded-pill px-4 text-white" style="background-color: #43518c; border: none;">
                                <span class="fa fa-search"></span> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>


            <hr>

            <div class="mt-4 table-responsive">
            <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">

                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Tutor Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Total Amount</th>
                            <th>Net Amount Received</th>
                            <th>Admin Commission(%)</th>
                            <th>Admin Commission(&pound;)</th>
                            <th>Account No.</th>
                            <th>Transaction No.</th>
                            <th>Transaction Date</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tutorpayouts as $payout )
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$payout->name}}</td>
                                <td>{{$payout->mobile}}</td>
                                <td>{{$payout->email}}</td>
                                <td>&pound;{{$payout->total_amount}}</td>
                                <td>&pound;{{$payout->net_amount_received}}</td>
                                <td>{{$payout->admin_commission_percentage}}%</td>
                                <td>&pound;{{$payout->admin_commission_amount}}</td>
                                <td>{{$payout->account_no}}</td>
                                <td>{{$payout->transaction_no}}</td>
                                <td>{{ \Carbon\Carbon::parse($payout->transaction_date)->format('d M Y') }}</td>
                                <td class="text-success">{{$payout->status_name}}</td>
                            </tr>
                        @endforeach
                    </tbody>

            </table>
            </div>
            <!-- content-wrapper ends -->

        </div>

    </div>
    <!-- content-wrapper ends -->
    <div class="d-flex justify-content-center" id="paginationContainer">
        {!! $tutorpayouts->links() !!}
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateTableAndPagination(data) {
            // $('#tableContainer').html(data.table);
            $('.users-table tbody').html(data.table);
            $('#paginationContainer').html(data.pagination);
        }

        $(document).ready(function() {
            $('#payment-search').submit(function(e) {
                e.preventDefault();
                const page = 1;
                const ajaxUrl = "{{ route('tutor.payouts-search') }}";
                var formData = $(this).serialize();

                formData += `&page=${page}`;

                $.ajax({
                    type: 'post',
                    url: ajaxUrl, // Define your route here
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function(data) {
                        // console.log(data)
                        updateTableAndPagination(data);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

            });


            $(document).on('click', '#paginationContainer .pagination a', function(e) {
                e.preventDefault();
                var formData = $('#payment-search').serialize();
                const page = $(this).attr('href').split('page=')[1];
                formData += `&page=${page}`;
                $.ajax({
                    type: 'post',
                    url: '{{ route("tutor.payouts-search") }}', // Define your route here
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        updateTableAndPagination(data);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>


@endsection
