@extends('student.layouts.main')
@section('main-section')
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

                <div id="listHeader">
                    <h3>Purchased Subjects</h3>
                </div>

                <div class="row mt-4">
                    @php
                        $uniqueSubjects = []; // Array to store unique subject_ids
                    @endphp

                    @foreach ($subjectlist as $subject)
                        @if (!in_array($subject->subjectid, $uniqueSubjects))
                            {{-- Display card only if subject_id is not already in the array --}}
                            <div class="col-md-3 mt-2">
                                <div class="card">
                                    <img src="{{ url('images/subjects') . '/' . $subject->image }}" class="card-img-top"
                                        alt="Fissure in Sandstone" />
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $subject->subjectname }}</h5>
                                        <p class="card-text"><b>Tutor: </b>{{ $subject->tutorname }}</p>
                                        <p class="card-text"><b>Purchased On: </b>{{ $subject->payment_date }}</p>
                                        {{-- <a href="{{ url('student/subjects/syllabus') . '/' . $subject->subject_id }}"
                                            class="btn btn-sm btn-primary">View</a> --}}
                                    </div>
                                </div>
                            </div>
                            @php
                                $uniqueSubjects[] = $subject->subjectid; // Add subject_id to the array
                            @endphp
                        @endif
                    @endforeach
                </div>





            </div>
            <!-- content-wrapper ends -->
        @endsection
