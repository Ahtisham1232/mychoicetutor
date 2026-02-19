@php
    use App\Models\payments\paymentstudents;
    use App\Models\students\studentattendance;
@endphp
@extends('student.layouts.main')
@section('main-section')
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            @keyframes blink {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0;
                }

                100% {
                    opacity: 1;
                }
            }

            .blinking-icon {
                animation: blink 1s infinite;
            }

            .red{
                color:#f06548;
            }
            .green{
                color: #0ab39c;
            }

            .blue{
                color: #405189
            }
            .lightblue{
                color:#3577f1;
            }

            .avalability i{
                padding-left: 10px;
            }
            /* New style for slots booked by the current user */
            .booked-by-me {
                background-color: rgb(169, 1, 1);
                /* color: #fff; Set text color to white for better visibility */
            }

        </style>

        <div class="page-content">
            <div class="container-fluid">
                @if (Session::has('success'))
                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('fail'))
                    <div class="alert alert-danger">{{ Session::get('fail') }}</div>
                @endif

                <div id="" class="mb-3 listHeader page-title-box">
                    <h3>Booked Slots</h3>
                    {{-- <a href="completed-classes"> <button class="btn btn-primary">Completed Classes</button></a> --}}

                </div>
                <div class="availability d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa fa-square red" aria-hidden="true"></i><span>&nbsp;Not Available</span>
                        <i class="fa fa-square green" aria-hidden="true"></i><span>&nbsp;Available</span>
                        <i class="fa fa-square lightblue" aria-hidden="true"></i><span>&nbsp;Your Bookings</span>
                        <i class="fa fa-square blue" aria-hidden="true"></i><span>&nbsp;Current Selection</span>
                    </div>
                    <div>
                        <a href="/student/enrollnow/{{$tutor_profile_id}}"><button class="btn btn-sm btn-primary">Book More Slots</button></a>
                    </div>
                </div>

                <form id="payment-search" action="{{ route('student.updateslots') }}" method="POST">
                    @csrf
                    <div class="row ">


                        <div class="col-md-3 mt-4">

                            <label for="">Tutor</label>
                            <input type="hidden" id="tutorenrollid" name="tutorenrollid"
                                value="{{ $enrollment->tutor_id }}">
                            <input type="text" class="form-control readonly" name="tutorenroll" id="tutorenroll" readonly
                                value="{{ $enrollment->tutor_name }}">
                            <span class="text-danger">
                                @error('tutorenrollid')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-3 mt-4">
                            <label for="">Subject</label>
                            <input type="hidden" id="subjectenrollid" name="subjectenrollid"
                                value="{{ $enrollment->subject_id }}">
                            <input type="text" class="form-control readonly" name="subjectenroll" id="subjectenroll"
                                readonly value="{{ $enrollment->subject_name }}">
                            <span class="text-danger">
                                @error('subjectenrollid')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <?php
                            $class_purchased = paymentstudents::where('student_id', session('userid')->id)
                            ->where('class_id', $enrollment->class_id)
                            ->where('tutor_id', $enrollment->tutor_id)
                            ->where('subject_id', $enrollment->subject_id)
                            ->sum('classes_purchased');
                        ?>


                        <div class="col-md-2 mt-4">
                            <label for="">Total Classes</label>
                            <input type="number" class="form-control readonly" name="classpurchased" id="classpurchased" value="{{$class_purchased}}" readonly>
                            <span class="text-danger">
                                @error('classpurchased')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>


                        <div class="col-md-2 mt-4">
                            <label for="">Slots Booked</label>
                            <input type="text" class="form-control readonly" name="bookedSlotsCount"
                                id="bookedSlotsCount" readonly value="{{$bookedSlotsCount}}">
                            <span class="text-danger">
                                @error('bookedSlotsCount')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="col-md-2 mt-4">
                            <label for="">Slots Pending</label>
                            <input type="text" class="form-control readonly" name="slotspending" value="{{$class_purchased - $bookedSlotsCount}}"
                                id="slotspending" readonly>
                            <span class="text-danger">
                                @error('slotspending')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>


                    </div>

                <hr>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table" style="display:block; overflow-Y:scroll !important;">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Slots</th>
                            </tr>
                        </thead>
                        <tbody>
                                <!-- ... Existing HTML code ... -->
                            @if(isset($groupedSlots) && count($groupedSlots)>0)
                                @foreach($groupedSlots as $date => $slots)
                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td>
                                            @foreach($slots as $slot)
                                                @php
                                                    $buttonClass = 'btn ';
                                                    if ($slot['bookedbyme']) {
                                                        $buttonClass .= 'btn-secondary booked-by-me';
                                                    } else {
                                                        $buttonClass .= $slot['is_available'] ? 'btn-success' : 'btn-danger';
                                                        $buttonClass .= $slot['is_available'] == 1 ? ' btn-primary' : '';
                                                    }
                                                @endphp
                                                <button type="button" class="slot-btn btn btn-sm {{ $buttonClass }}"
                                                    data-date="{{ $date }}" data-time="{{ $slot['time'] }}" data-slot-id="{{ $slot['id'] }}"
                                                    {{ $slot['is_available'] ? '' : 'disabled' }}>
                                                    {{ $slot['time'] }}
                                                </button>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            @else  
                                <div class="alert alert-danger mt-3">
                                    <i class="fa fa-lock"></i> 
                                    <strong>Update Booking Unavailable:</strong> This tutor has not set their availability slots yet.Update Enrollment is disabled until slots are created.
                                </div>
                            @endif

                        </tbody>

                    </table>
                </div>

            </div>
                @if (session('usertype') == 'Parent')
                @else
                <div style="display: flex; justify-content:space-between" class="my-3">
                   <div>
                    <input type="hidden" id="slotids" name="slotids">
                    <input type="checkbox" id="contactadmin" name="contactadmin"> <span><label for="contactadmin" > Please select to contact Admin, if you are not able to select slots.</label></span>
                   </div>
                 @if(!isset($groupedSlots) || count($groupedSlots) == 0)
                        <!-- Do NOT show the button, maybe show alert instead -->
                    @else
                        <button class="btn btn-sm btn-success">Update Slots</button>
                    @endif

                @endif
            </form>
                </div>
            </div>
        </div>

    </div>
    <!-- content-wrapper ends -->

 <!-- ... Existing HTML code ... -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize an array to store selected slots
        var selectedSlots = [];

        // Event listener for the "requiredclasses" input field change
        $('#requiredclassenroll').on('keyup', function () {
            // Clear the selected slots and remove the 'selected' class from buttons
            selectedSlots = [];
            $('.slot-btn').removeClass('selected').removeClass('btn-primary').addClass('btn-success');
            // Update the slotids input field
            updateSlotIdsInput();
            // Log the cleared selected slots (you can customize this part based on your requirements)
            console.log('Selected Slots Cleared:', selectedSlots);
        });

        // Button click event handler
        $('.slot-btn').on('click', function () {
            if (parseInt(document.getElementById('slotspending').value) < 1 || document.getElementById('slotspending').value == '') {
                alert('Please enter required classes before slot selection');
                return false;
            }
            if (selectedSlots.length >= parseInt(document.getElementById('slotspending').value)) {
                alert('You already selected ' + selectedSlots.length + ' as per your required classes.');
                return false;
            }
            var $button = $(this);
            var date = $button.data('date');
            var time = $button.data('time');
            var slotId = $button.data('slot-id'); // Added to get the slot ID

            // Check if the slot is not selected
            if (!$button.hasClass('selected')) {
                // Add the slot to the selectedSlots array
                selectedSlots.push({ date: date, time: time, slotId: slotId });
                // Toggle the selected class
                $button.addClass('selected');
                // Change the button color to blue
                $button.removeClass('btn-success').addClass('btn-primary');
            } else {
                // Remove the slot from the selectedSlots array
                selectedSlots = selectedSlots.filter(function (slot) {
                    return !(slot.date === date && slot.time === time && slot.slotId === slotId);
                });
                // Toggle the selected class
                $button.removeClass('selected');
                // Change the button color back to its original color
                $button.removeClass('btn-primary').addClass('btn-success');
            }

            // Update the slotids input field
            updateSlotIdsInput();

            // Log the selected slots (you can customize this part based on your requirements)
            console.log('Selected Slots:', selectedSlots);
        });

        // Function to update the slotids input field
        function updateSlotIdsInput() {
            var slotIds = selectedSlots.map(function (slot) {
                return slot.slotId;
            }).join(',');
            $('#slotids').val(slotIds);
        }

        // Additional logic can be added here based on your requirements
    });
</script>
    <script>
        function calculate() {
            $('#totalamountenroll').val($('#rateperhourenroll').val() * $('#requiredclassenroll').val())
        }
    </script>


@endsection
