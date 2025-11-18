@if ($type == 'student-classes' && isset($classes))
    @foreach ($classes as $class)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $class->meeting_id }}</td>
            <td>{{ $class->status }}</td>
            <td>{{ $class->subjects }}</td>
            <td>{{ $class->batch }}</td>
            <td>{{ $class->topics }}</td>
            <td>{{ $class->start_time }}</td>
            <td>{{ $class->duration }}</td>
            <td>
                @if ($class->is_completed == 0)

                    <a href="{{ $class->join_url }}" target="_blank"><button
                            class="btn btn-sm btn-success"><span class="fa fa-play-circle "></span> Join
                            Class</button></a>
                @else
                    <button
                            class="btn btn-sm btn-warning" data-toggle="modal" data-target="#openreviewsmodal" onclick="openfeedbackmodal('{{$class->class_id}}','{{$class->subject_id}}','{{$class->tutor_id}}')"><span class="fa fa-check "></span> Give Feedback</button>
                @endforelse

            </td>
            {{-- <td><button class="btn btn-sm btn-primary"
                onclick="openstudentmodal({{ $liveclass->batch_id }});"><span
                    class="fa fa-search"></span> Start Class</button>
            </td> --}}
        </tr>
    @endforeach
@endif
@if ($type == 'student-assignments' && isset($assignments))
    @foreach ($assignments as $assignment)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $assignment->class }}</td>
            <td>{{ $assignment->subject }}</td>
            <td>{{ $assignment->topic }}</td>
            <td>{{ $assignment->assignment_name }}</td>
            <td>{{ $assignment->assignment_description }}</td>
            <td><a href="{{ url('uploads/documents/assignments') }}/{{ $assignment->assignment_link }}"
                    target="_blank"><button class="badge bg-primary">View
                        Assignment</button></td>
            <td>{{ $assignment->assignment_start_date }}</td>
            <td>{{ $assignment->assignment_end_date }}</td>
            <td>
                @php
                    $isSubmitted = false;
                    $submissionData = null;
                    foreach ($submissions as $submission) {
                        if ($submission->assignment_id == $assignment->assignment_id) {
                            $isSubmitted = true;
                            $submissionData = $submission;
                            break;
                        }
                    }
                @endphp

                @if ($isSubmitted)
                    <a class="btn btn-sm btn-success"
                        ><span class="fa fa-check"></span> Assignment Submitted</a>
                @else
                    <button class="btn btn-sm btn-primary"
                        onclick="openmodal('{{ $assignment->assignment_id }}');" data-toggle="modal"
                        data-target="#openmodal"><span class="fa fa-cloud-upload"></span> Submit Assignment</button>
                @endif
            </td>
            <td>
                @if ($isSubmitted && $submissionData && $submissionData->results !== null)
                    <span class="badge bg-info" style="font-size: 14px; padding: 8px;">
                        {{ number_format($submissionData->results, 2) }}
                    </span>
                @elseif ($isSubmitted)
                    <span class="text-muted">Not Graded Yet</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                @if ($isSubmitted && $submissionData && $submissionData->remarks)
                    <span style="max-width: 200px; display: inline-block;">{{ $submissionData->remarks }}</span>
                @elseif ($isSubmitted)
                    <span class="text-muted">No remarks</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
        </tr>
    @endforeach
@endif
@if ($type == 'student-exams' && isset($exams))
    @php
    $i=1;
    @endphp
    @foreach ($exams as $exam)
        @if ($exam->attemptsRemaining > 0)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $exam->class }}</td>
                <td>{{ $exam->subject }}</td>
                <td>{{ $exam->topic }}</td>
                <td>{{ $exam->name }}</td>
                <td>{{ $exam->description }}</td>
                <td>{{ $exam->attemptsRemaining }}</td>
                <td>{{ $exam->test_duration }} min</td>
                <td>{{ $exam->test_start_date }}</td>
                <td>{{ $exam->test_end_date }}</td>
                <td><a href="{{ url('student/taketest') }}/{{ $exam->id }}"
                        class="badge bg-success">Start Test</a></td>
            </tr>
            @php
                $i++;
            @endphp
        @endif
    @endforeach
@endif
@if ($type == 'students-payments' && isset($payments))
    @php
    $totalAmount = 0; // Initialize the total amount variable
    @endphp
    @foreach ($payments as $payment)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td><a href="#" class="" onclick="showinvoice('{{$payment->paymentdetails_id}}')"> {{$payment->transaction_id}}</a></td>
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
@endif
@if ($type == 'tutor-attendances' && isset($studentAttendances))
    @foreach ($studentAttendances as $attendance)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $attendance->class_name ?? '' }}</td>
            <td>{{ $attendance->subject_name ?? '' }}</td>
            <td>{{ $attendance->student_name ?? '' }}</td>
            <td>{{ $attendance->batch_name ?? '' }}</td>
            <td>{{ $attendance->class_starts_at ?  date('Y-m-d H:i:s', strtotime($attendance->class_starts_at)) : '-' }}</td>
            @php
                if($attendance->status == 1){
                    $status = 'Present';
                }elseif($attendance->status == 0){
                    $status = 'Absent';
                }
            @endphp
            <td>{{ $status ?? '' }}</td>
        </tr>
    @endforeach
@endif
