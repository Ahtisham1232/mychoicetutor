@extends('student.layouts.main')
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
            </style>

            <div class="page-content">
                <div class="container-fluid">


                    <div id="" class="mb-3 listHeader page-title-box">
                        <h3>Payment Details</h3>
                    </div>

                    <form action="{{route('student.payments-search')}}" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-3 mt-4">
                                <label for="transaction_id">Transaction ID</label>
                                <input type="text" class="form-control" name="transaction_id" placeholder="Transaction Id">
                            </div>
                            {{-- <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="smob" placeholder="Student Mobile">
                            </div>

                            <div class="col-md-3">
                                <label>End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="smob" placeholder="Student Mobile">
                            </div> --}}

                            <div class="col-md-4">
                                <label>&nbsp;</label> <div class="d-flex align-items-center">
                                    <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill px-4 me-3">
                                        <span class="fa fa-refresh"></span> Reset
                                    </a>

                                    <button type="submit" class="btn rounded-pill px-4 text-white" style="background-color: #43518c;">
                                        <span class="fa fa-search"></span> Search
                                    </button>
                                </div>
                            </div>

                            {{-- <div class="col-md-3 mt-4">
                                <button class="btn  btn-primary" type="submit" style="float:right"> <span
                                    class="fa fa-search"></span> Search</button>
                            </div> --}}
                        </div>

                    </form>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table">
                        <thead class="">
                            <tr>
                                <th scope="col">S.No.</th>
                                <th scope="col">Trans.No.</th>
                                <th scope="col">Trans. Date</th>
                                <th scope="col">Class</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Tutor</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalAmount = 0; // Initialize the total amount variable
                                @endphp
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td><a href="#" class="" onclick="showinvoice('{{$payment->paymentdetails_id}}')"> {{$payment->transaction_id}}</a></td>
                                        {{-- <td>{{$payment->created_at}}</td> --}}
                                        <td>@userTz($payment->created_at)</td>
                                        <td>{{$payment->class}}</td>
                                        <td>{{$payment->subject}}</td>
                                        <td>{{$payment->tutor}}</td>
                                        <td>£{{$payment->amount}}</td>
                                    </tr>
                                    @php
                                    $totalAmount += $payment->amount; // Add the current payment amount to the total
                                    @endphp
                                @endforeach
                                <!-- Display the total amount after the loop -->
                                <tr>
                                    <td colspan="4"></td>
                                    <td><strong>Total:</strong></td>
                                    <td><strong>£{{$totalAmount}}</strong></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center" id="paginationContainer">
                        {!! $payments->links() !!}
                    </div>






                </div>
            </div>
        </div>
                <!-- content-wrapper ends -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                function updateTableAndPagination(data) {
                    // $('#tableContainer').html(data.table);
                     $('.users-table tbody').html(data.table);
                     $('#paginationContainer').html(data.pagination);
                }
            </script>
@endsection
