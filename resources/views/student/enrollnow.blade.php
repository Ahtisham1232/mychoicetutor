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

            .red {
                color: #f06548;
            }

            .green {
                color: #0ab39c;
            }

            .blue {
                color: #405189
            }

            .avalability i {
                padding-left: 10px;
            }
        </style>
        
        <style>
            .active-slot {
                outline: 2px solid #000;
                box-shadow: 0 0 5px #000;
            }
            
            #requestEnrollmentBtn:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
            
            #requestEnrollmentBtn:not(:disabled) {
                opacity: 1;
                cursor: pointer;
            }
            
            .contact-admin-section {
                background-color: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                border: 1px solid #dee2e6;
                margin-top: 10px;
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

                <div id="" class="mb-3 listHeader page-title-box row">
                    <div class="col-12 col-md-6">
                        <h3>Request Enrollment</h3>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="alert alert-info">
                            <strong>Note:</strong> You are requesting enrollment for classes. Admin will review your request and approve it after payment verification. You will be notified once approved.
                        </div>
                    </div>
                </div>
                <div class="avalability">
                    <i class="fa fa-square red" aria-hidden="true"></i><span>&nbsp;Not Available</span>
                    <i class="fa fa-square green" aria-hidden="true"></i><span>&nbsp;Available</span>
                    <i class="fa fa-square blue" aria-hidden="true"></i><span>&nbsp;Selected</span>

                </div>
                <form id="payment-search" action="{{ route('student.purchaseclass') }}" method="POST">
                    @csrf
                    <div class="row ">
                        <div class="col-md-3 mt-4">
                            <label for="">Tutor</label>
                            <input type="hidden" id="tutorenrollid" name="tutorenrollid" value="{{$enrollment->tutor_id }}">
                            <input type="text" class="form-control readonly" name="tutorenroll" id="tutorenroll" readonly
                                value="{{ $enrollment->tutor_name }}">
                            <span class="text-danger">
                                @error('tutorenrollid')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="col-md-2 mt-4">
                            <label for="">Rate/Hr(£)</label>
                            <input type="text" class="form-control readonly" name="rateperhourenroll"
                                id="rateperhourenroll" readonly value="{{ $enrollment->rate }}">
                            <span class="text-danger">
                                @error('rateperhourenroll')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="col-md-3 mt-4">
                            <label for="">Subject</label>
                            <Select type="text" class="form-control" name="subjectenrollid" id="subjectenrollid">
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </Select>
                            <span class="text-danger">
                                @error('subjectenrollid')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <div class="col-md-2 mt-4">
                            <label for="">Required Class<i style="color: red">*</i></label>
                            <input type="number" class="form-control" name="requiredclassenroll" id="requiredclassenroll"
                                onkeyup="calculate();valuechanged()" required>
                            <span class="text-danger">
                                @error('requiredclassenroll')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <div class="col-md-2 mt-4">
                            <label for="">Total Amount(£)</label>
                            <input type="text" class="form-control readonly" name="totalamountenroll"
                                id="totalamountenroll" readonly>
                            <span class="text-danger">
                                @error('totalamountenroll')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                    </div>

                    <hr>

                    <div class="full-width-table-responsive">
                        @if(count($groupedSlots) > 0)
                        <table class="table table-hover table-striped align-middle table-nowrap mb-0 users-table"
                            style="height: 260px;">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Slots</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupedSlots as $date => $slots)
                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td>
                                            @foreach ($slots as $slot)
                                                @php
                                                    // Convert the time string to a Carbon instance for formatting
                                                    $formattedTime = \Carbon\Carbon::parse($slot['time'])->format(
                                                        'h:i A',
                                                    );
                                                @endphp
                                                <input type="hidden" name="selected_slot_id" id="selectedSlotId">

                                                <button type="button"
                                                    class="slot-btn btn btn-sm btn-{{ $slot['is_available'] ? 'success' : 'danger' }}"
                                                    data-date="{{ $date }}" data-time="{{ $formattedTime }}"
                                                    data-slot-id="{{ $slot['id'] }}"
                                                    {{ $slot['is_available'] ? '' : 'disabled' }}>
                                                    {{ $formattedTime }}
                                                </button>
                                            @endforeach

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        @else
                            <div class="alert alert-danger mt-3">
                                <i class="fa fa-lock"></i> 
                                <strong>Booking Unavailable:</strong> This tutor has not set their availability slots yet. Enrollment is disabled until slots are created.
                            </div>
                         @endif
                    </div>



                    </table>
                     </div>

                        <div style="display: flex; justify-content:space-between" class="my-3">
                            <div class="contact-admin-section">
                                <input type="hidden" id="slotids" name="slotids">
                                <div class="form-check">
                                    {{-- Disable checkbox if no slots --}}
                                    <input type="checkbox" class="form-check-input" id="contactadmin" name="contactadmin" {{ count($groupedSlots) == 0 ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="contactadmin">
                                        <strong>Checked this checkbox for the enrollment</strong>
                                    </label>
                                </div>
                            </div>
                            
                            {{-- Ensure button is strictly disabled if count is 0 --}}
                            <button id="requestEnrollmentBtn" class="btn btn-sm btn-success" 
                                {{ count($groupedSlots) == 0 ? 'disabled' : '' }}>
                                Request Enrollment
                            </button>
                        </div>
                </form>
            <div>
                <p style="color: #99A7AE">Subject selection is a formality—you can take any subject by mutual agreement with
                    your tutor.</p>
            </div>
        </div>
    </div>

    <!-- content-wrapper ends -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            var slotCount = {{ count($groupedSlots) }};
            // Initialize an array to store selected slots
            var selectedSlots = [];
            // Function to check if button should be enabled
            function checkButtonState() {
                // 1. If no slots exist for this tutor at all, keep button disabled
                if (slotCount === 0) {
                    $('#requestEnrollmentBtn').prop('disabled', true);
                    return;
                }

                // 2. Get the value of required classes
                var requiredClassValue = parseInt($('#requiredclassenroll').val());
                var hasRequiredClasses = !isNaN(requiredClassValue) && requiredClassValue > 0;

                // 3. Check selection criteria
                var hasSelectedSlots = selectedSlots.length > 0;
                var isContactAdminChecked = $('#contactadmin').is(':checked');

                // CONDITION: Must have Required Classes AND (Selected Slots OR Contact Admin)
                if (hasRequiredClasses && (hasSelectedSlots || isContactAdminChecked)) {
                    $('#requestEnrollmentBtn').prop('disabled', false);
                } else {
                    $('#requestEnrollmentBtn').prop('disabled', true);
                }
            }
            
            // Add event listener to checkbox
            $('#contactadmin').on('change', checkButtonState);

            // Event listener for the "requiredclasses" input field change
            $('#requiredclassenroll').on('keyup', function() {
                // Clear the selected slots and remove the 'selected' class from buttons
                selectedSlots = [];
                $('.slot-btn').removeClass('selected').removeClass('btn-primary').addClass('btn-success');
                // Update the slotids input field
                updateSlotIdsInput();
                // Check button state after clearing slots
                checkButtonState();
                // Log the cleared selected slots (you can customize this part based on your requirements)
                console.log('Selected Slots Cleared:', selectedSlots);
            });

            // Button click event handler
            $('.slot-btn').on('click', function() {
                var $button = $(this);
                var date = $button.data('date');
                var time = $button.data('time');
                var slotId = $button.data('slot-id'); // Added to get the slot ID

                // Get the required number of classes from the input field
                var requiredClasses = parseInt(document.getElementById('requiredclassenroll').value);

                // Check if the input value is valid
                if (isNaN(requiredClasses) || requiredClasses < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Input',
                        text: 'Please enter a valid number of required classes.',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Check if the slot is not selected
                if (!$button.hasClass('selected')) {
                    // Check if the number of selected slots has reached the required number
                    if (selectedSlots.length >= requiredClasses) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Limit Reached',
                            text: 'You already selected ' + selectedSlots.length +
                                ' slots as per your required classes.',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Add the slot to the selectedSlots array
                    selectedSlots.push({
                        date: date,
                        time: time,
                        slotId: slotId
                    });
                    // Toggle the selected class
                    $button.addClass('selected');
                    // Change the button color to blue
                    $button.removeClass('btn-success').addClass('btn-primary');
                } else {
                    // Remove the slot from the selectedSlots array
                    selectedSlots = selectedSlots.filter(function(slot) {
                        return !(slot.date === date && slot.time === time && slot.slotId ===
                            slotId);
                    });
                    // Toggle the selected class
                    $button.removeClass('selected');
                    // Change the button color back to its original color
                    $button.removeClass('btn-primary').addClass('btn-success');
                }

                // Update the slotids input field
                updateSlotIdsInput();
                
                // Check button state after slot selection
                checkButtonState();

                // Log the selected slots (you can customize this part based on your requirements)
                console.log('Selected Slots:', selectedSlots);
            });


            // Function to update the slotids input field
            function updateSlotIdsInput() {
                var slotIds = selectedSlots.map(function(slot) {
                    return slot.slotId;
                }).join(',');
                $('#slotids').val(slotIds);
            }

            // Initial check for button state
            checkButtonState();
            
            // Additional logic can be added here based on your requirements
        });
    </script>
    <script>
        function calculate() {
            $('#totalamountenroll').val($('#rateperhourenroll').val() * $('#requiredclassenroll').val())
        }
    </script>
@endsection
