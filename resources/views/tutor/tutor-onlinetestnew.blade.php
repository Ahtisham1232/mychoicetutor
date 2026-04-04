@extends('tutor.layouts.main')
@section('main-section')
    <link rel="stylesheet" href="{{ asset('css/tutor-quiz-builder.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .selectAns {
                border: 1px solid lightgrey;
                padding-top: 10px;
                padding-bottom: 10px;
            }

            input[type='radio'] {
                accent-color: green;
            }

            .testseries {
                display: inline-block;
            }

            @if(isset($tdata))
            /* Force sidebar to stay open when editing */
            .sidebar {
                display: block !important;
                width: 250px !important;
                min-width: 250px !important;
            }

            body.sidebar-icon-only .sidebar,
            body.sidebar-hidden .sidebar {
                display: block !important;
                width: 250px !important;
                min-width: 250px !important;
            }

            /* Disable hamburger menu when editing */
            #topnav-hamburger-icon {
                pointer-events: none !important;
                opacity: 0.5 !important;
                cursor: not-allowed !important;
            }

            /* Test type styling */
            .test-type-section {
                background-color: #f8f9fa;
                border: 2px solid #e9ecef;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .test-type-indicator {
                font-size: 1.1em;
                font-weight: bold;
            }

            .badge-lg {
                font-size: 0.9em;
                padding: 8px 12px;
            }

            /* Highlight test type selection */
            #test-type {
                font-weight: bold;
                font-size: 1.1em;
            }

            #test-type:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            @endif
        </style>
        <style>
            .listHeader {
                display: flex;
                justify-content: space-between;
            }

            .studentpop tr td {
                margin: 0;
                padding-top: 0 !important;
                padding-bottom: 0 !important;


            }

            .my-custom-scrollbar {
                position: relative;
                height: 200px;
                overflow: auto;
            }

            .table-wrapper-scroll-y {
                display: block;
            }

            select option:nth-child(odd) {
                background: rgb(227, 226, 226);
            }

            select option:checked {
                background-color: rgb(47, 255, 0);
                /* color:white; */
            }

            select option:hover {
                background-color: rgb(47, 255, 0);
            }

            .select-checkbox option::before {
                content: "\2610";
                width: 1.3em;
                text-align: center;
                display: inline-block;
            }

            .select-checkbox option:checked::before {
                content: "\2611";
            }

            .select-checkbox-fa option::before {
                font-family: FontAwesome;
                content: "\f096";
                width: 1.3em;
                display: inline-block;
                margin-left: 2px;
            }

            .select-checkbox-fa option:checked::before {
                content: "\f046";
            }
            .btn-outline-success{
                color: #007bff;
            }
            </style>


        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
         

            <div class="page-content">
                <div class="container-fluid">
            @if (Session::has('success'))
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            @endif
            @if (Session::has('fail'))
                <div class="alert alert-danger">{{ Session::get('fail') }}</div>
            @endif

            <div class="mb-3 page-title-box listHeader d-flex justify-content-between align-items-center">
                <div>
                    <h3>{{ isset($tdata) ? 'Edit Test Series' : 'Create Test Series' }}</h3>
                    @if(isset($tdata))
                        <span class="badge badge-lg {{ $tdata->test_type == 1 ? 'badge-primary' : 'badge-warning' }} mt-2">
                            <i class="fa {{ $tdata->test_type == 1 ? 'fa-check-circle' : 'fa-edit' }}"></i>
                            {{ $tdata->test_type == 1 ? 'Objective Test (Multiple Choice)' : 'Subjective Test (Descriptive)' }}
                        </span>
                    @else
                        <span class="badge badge-lg badge-info mt-2" id="testTypeIndicator">
                            <i class="fa fa-question-circle"></i>
                            Select Test Type Below
                        </span>
                    @endif
                </div>
                <a href="{{ route('tutor.onlinetests') }}" class="btn btn-primary float-right ">Back To List</a>
            </div>

            <form action="{{ route('tutor.onlinetests.store') }}" method="POST">
                @csrf
            
                <div class="test-type-section">
                    <h5 class="text-primary mb-3">
                        <i class="fa fa-cogs"></i> Test Configuration
                    </h5>
                    <div class="row">
                        <input type="hidden" id="id" name="id" value="{{ $tdata->id ?? '' }}" class="form-control">
                        <div class="col-md-4 col-sm-6 col-12">
                            <label for="">Test Type<i style="color: red">*</i></label>
                            <select class="form-control" id="test-type" name="test_type" onchange="onTestTypeChange()" {{ isset($tdata) && !empty($tdata->question_id) ? 'disabled' : '' }}>
                                @if ($tdata ?? '')
                                <option value="1" @if ($tdata->test_type == 1) selected @endif>Objective (Multiple Choice)</option>
                                <option value="2" @if ($tdata->test_type == 2) selected @endif>Subjective (Descriptive)</option>
                                @else
                                <option value="1">Objective (Multiple Choice)</option>
                                <option value="2">Subjective (Descriptive)</option>
                                @endif
                            </select>
                            @if(isset($tdata) && !empty($tdata->question_id))
                            <small class="text-muted">Test type cannot be changed once questions are added</small>
                            @endif
                        </div>
                        <div class="col-md-3 col-sm-3 col-12">
                            <label for="">Test Name<i style="color: red">*</i></label>
                            <input type="text" class="form-control" id="testname" name="testname"
                                value="{{ $tdata->name ?? '' }}" required>
                            <span class="text-danger">
                                @error('testname')
                                    {{ 'Please enter test name' }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-6 col-sm-3 col-12">
                            <label for="">Test Description<i style="color: red">*</i></label>
                            <textarea type="text" class="form-control" id="testdescription"  name="testdescription" required>{{ $tdata->description ?? '' }}</textarea>
                            <span class="text-danger">
                                @error('testdescription')
                                    {{ 'Please enter test description' }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-3 col-sm-3 col-12">
                            <label for="">Class<i style="color:red">*</i></label>
                            <select type="text" class="form-control" id="classname" name="classname"
                                onchange="fetchSubjects();" required>
                                <option value="">--Select--</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        @if ($tdata->class_id ?? '') @if ($class->id == $tdata->class_id)
                                        selected @endif
                                        @endif>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                @error('classname')
                                    {{ 'Please select class' }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-3 col-sm-3 col-12 mt-2">
                            <label for="">Subject<i style="color:red">*</i></label>
                            <select type="text" class="form-control" id="subject" name="subject" onchange="fetchQuestions();"
                                required>
                                @if ($tdata ?? '')
                                    @foreach ($subjects as $subject)
                                        <option
                                            value="{{ $subject->id }}"@if ($tdata->subject_id ?? '') @if ($subject->id == $tdata->subject_id)
                                        selected @endif
                                            @endif>{{ $subject->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="text-danger">
                                @error('subject')
                                    {{ 'Please select subject' }}
                                @enderror
                            </span>

                        </div>
                        <div class="col-md-3 col-sm-3 col-12 mt-2">
                            <label for="">Topic<i style="color:red">*</i></label>
                            <input type="text" class="form-control" id="topic" name="topic" value="{{$tdata->topic_name ?? ''}}">
                            <span class="text-danger">
                                @error('topic')
                                    {{ 'Please enter topic' }}
                                @enderror
                            </span>
                        </div>
                    {{-- <div class="col-md-3 col-sm-3 col-12 mt-2">
                            <label for="">Max Attempt<i style="color: red">*</i></label>
                            <input type="number" class="form-control" id="maxattempt" name="maxattempt"
                                value="{{ $tdata->max_attempt ?? '' }}" required>
                            <span class="text-danger">
                                @error('maxattempt')
                                    {{ 'Please enter max attempts' }}
                                @enderror
                            </span>
                        </div> --}}
                        <div class="col-md-3 col-sm-3 col-12 mt-2">
                            <label for="">Duration(minutes)<i style="color: red">*</i></label>
                            <input type="number" class="form-control" id="duration" name="duration"
                                value="{{ $tdata->test_duration ?? '' }}" required>
                            <span class="text-danger">
                                @error('duration')
                                    {{ 'Please enter test duration' }}
                                @enderror
                            </span>
                        </div>

                    {{-- <div class="col-md-3 col-sm-3 col-12 mt-2">
                        <label for="">Test Start Date<i style="color: red">*</i></label>
                        <input type="datetime-local" class="form-control" id="tstartdate" name="tstartdate"
                            value="{{ $tdata->test_start_date ?? '' }}" required>
                        <span class="text-danger">
                            @error('tstartdate')
                                {{ 'Please select test start date' }}
                            @enderror
                        </span>
                        </div>

                        <div class="col-md-3 col-sm-3 col-12 mt-2">
                            <label for="">Test End Date<i style="color: red">*</i></label>
                            <input type="datetime-local" class="form-control" id="testenddate" name="testenddate"
                                value="{{ $tdata->test_end_date ?? '' }}" required>
                            <span class="text-danger">
                                @error('testenddate')
                                    {{ 'Please select test end date' }}
                                @enderror
                            </span>
                        </div> --}}
                    </div>


                <!-- All-in-One Quiz Builder Section -->
                <div class="form-group row mt-4">
                    <div class="col-md-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0" style="color: black">
                                    <i class="fa fa-question-circle"></i> Build Your Quiz
                                    <span class="badge badge-light ml-2" id="totalQuestionsBadge">0 questions</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs mb-3" id="quizBuilderTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link {{ isset($tdata) ? '' : 'active' }}" id="create-questions-tab" data-toggle="tab" href="#create-questions" role="tab">
                                            <i class="fa fa-plus-circle"></i> Create Questions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="select-questions-tab" data-toggle="tab" href="#select-questions" role="tab">
                                            <i class="fa fa-list"></i> Use Existing Questions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ isset($tdata) ? 'active' : '' }}" id="preview-tab" data-toggle="tab" href="#preview" role="tab">
                                            <i class="fa fa-eye"></i> Preview Quiz
                                        </a>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content" id="quizBuilderTabContent">
                                    <!-- Tab 1: Create Questions -->
                                    <div class="tab-pane fade {{ isset($tdata) ? '' : 'show active' }}" id="create-questions" role="tabpanel">
                                        <div class="alert alert-info">
                                            <i class="fa fa-lightbulb"></i> <strong>Tip:</strong> Create questions directly here. They'll be automatically added to your quiz!
                                        </div>
                                        
                                        <!-- Quick Question Form -->
                                        <div class="card border-success mb-3">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fa fa-plus"></i> Add New Question</h6>
                                            </div>
                                            <div class="card-body">
                                                <div id="inlineQuestionForm">
                                                    <input type="hidden" id="inlineClassId" value="{{ isset($tdata) ? '' : ($tdata->class_id ?? '') }}">
                                                    <input type="hidden" id="inlineSubjectId" value="{{ isset($tdata) ? '' : ($tdata->subject_id ?? '') }}">
                                                    <input type="hidden" id="inlineTestType" value="{{ isset($tdata) ? '1' : ($tdata->test_type ?? '1') }}">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label>Topic <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="inlineTopic"
                                                                   value="{{ isset($tdata) ? '' : ($tdata->topic_name ?? '') }}"
                                                                   placeholder="e.g., Basic Math, Photosynthesis">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label>Question <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="inlineQuestion" rows="3" 
                                                                      placeholder="Enter your question here..."></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Objective Options -->
                                                    <div id="inlineObjectiveOptions" style="display: {{ ($tdata->test_type ?? '1') == '1' ? 'block' : 'none' }};">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <label>Option A <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="inlineOptionA" placeholder="Enter option A">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label>Option B <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="inlineOptionB" placeholder="Enter option B">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label>Option C <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="inlineOptionC" placeholder="Enter option C">
                                                            </div>
                                                            <div class="col-md-6 mb-2">
                                                                <label>Option D <span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control" id="inlineOptionD" placeholder="Enter option D">
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2">
                                                            <div class="col-md-12">
                                                                <label>Correct Answer <span class="text-danger">*</span></label>
                                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                                    <label class="btn btn-outline-success">
                                                                        <input type="radio" name="inlineCorrectAnswer" value="A" autocomplete="off"> A
                                                                    </label>
                                                                    <label class="btn btn-outline-success">
                                                                        <input type="radio" name="inlineCorrectAnswer" value="B" autocomplete="off"> B
                                                                    </label>
                                                                    <label class="btn btn-outline-success">
                                                                        <input type="radio" name="inlineCorrectAnswer" value="C" autocomplete="off"> C
                                                                    </label>
                                                                    <label class="btn btn-outline-success">
                                                                        <input type="radio" name="inlineCorrectAnswer" value="D" autocomplete="off"> D
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn btn-success btn-lg" id="btnAddQuestionInline">
                                                                <i class="fa fa-plus"></i> Add Question to Quiz
                                                            </button>
                                                            <button type="button" class="btn btn-primary" id="btnClearQuestionForm">
                                                                <i class="fa fa-redo"></i> Clear Form
                                                            </button>
                                                            <small class="text-muted ml-3">
                                                                <i class="fa fa-info-circle"></i> Make sure Class, Subject, and Test Type are selected above
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Created Questions List -->
                                        <div id="createdQuestionsList">
                                            <h6 class="mb-3"><i class="fa fa-list"></i> Questions Added to Quiz</h6>
                                            <div id="createdQuestionsContainer">
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-info-circle"></i> No questions created yet. Use the form above to add questions.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 2: Select Existing Questions -->
                                    <div class="tab-pane fade" id="select-questions" role="tabpanel">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> Select questions from your question bank. Make sure you've selected Class and Subject above first.
                                        </div>
                                        
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-primary btn-lg" id="btnSelectQuestions">
                                                <i class="fa fa-list"></i> Browse Question Bank
                                            </button>
                                        </div>

                                        <div id="selectedQuestionsContainer">
                                            <div class="alert alert-warning">
                                                <i class="fa fa-info-circle"></i> No questions selected from question bank yet.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 3: Preview -->
                                    <div class="tab-pane fade {{ isset($tdata) ? 'show active' : '' }}" id="preview" role="tabpanel">
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-info btn-lg" id="btnPreviewQuiz">
                                                <i class="fa fa-eye"></i> Preview Complete Quiz
                                            </button>
                                        </div>
                                        <div id="previewContent">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Click "Preview Complete Quiz" to see how your quiz will look.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden input for selected question IDs -->
                                <input type="hidden" id="selectedQuestionsInput" name="questiondata" value="">
                                <input type="hidden" id="existingQuestionsData" value="{{ isset($tdata) && $tdata->question_id ? $tdata->question_id : '' }}">
                                
                                <span class="text-danger">
                                    @error('questiondata')
                                        {{ 'Please add at least one question to your quiz' }}
                                    @enderror
                        </span>
                    </div>
                    </div>
                </div>
                    </div>
                </div>

                <!-- Form Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12 col-sm-12 col-12">
                        <div style="display:flex; justify-content: space-between; align-items: center;">
                            <a href="{{ route('tutor.onlinetests') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" id="btnSaveTest" class="btn btn-success btn-lg">
                                <i class="fa fa-save"></i> Save Test
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- content-wrapper ends -->

        <script>
            // Make functions available globally but wait for jQuery
            (function() {
                function initFunctions() {
                    if (typeof jQuery === 'undefined') {
                        setTimeout(initFunctions, 100);
                        return;
                    }
                    
                    var $ = jQuery;
                    
                    // Make functions global
                    window.fetchSubjects = function() {
                        var classId = $('#classname option:selected').val();
                        $("#subject").html('');
                        $("#topic").html('');
                        $.ajax({
                            url: "{{ url('fetchsubjects') }}",
                            type: "POST",
                            data: {
                                class_id: classId,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                $('#subject').html('<option value="">-- Select Type --</option>');
                                $.each(result.subjects, function(key, value) {
                                    $("#subject").append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                            }
                        });
                    };

                    window.fetchTopics = function() {
                        var subjectId = $('#subject option:selected').val();
                        $("#topic").html('');
                        $.ajax({
                            url: "{{ url('fetchtopics') }}",
                            type: "POST",
                            data: {
                                subject_id: subjectId,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                $('#topic').html('<option value="">-- Select Type --</option>');
                                $.each(result.topics, function(key, value) {
                                    $("#topic").append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                            }
                        });
                    };

                    window.fetchQuestions = function() {
                        var subjectId = $('#subject option:selected').val();
                        var type = $('#test-type option:selected').val();
                        $.ajax({
                            url: "{{ url('tutor/fetchquestions') }}",
                            type: "POST",
                            data: {
                                subject_id: subjectId,
                                type : type,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                console.log(result);
                                $('#questiondata').html('');
                                $.each(result, function(key, value) {
                                    $("#questiondata").append('<option value="' + value
                                        .id + '">' + value.question + '</option>');
                                });
                            }
                        });
                    };

                    window.onTestTypeChange = function() {
                        var selectedType = $('#test-type').val();
                        var currentQuestions = $('#selectedQuestionsInput').val();

                        // If there are already questions selected and type is changed, warn user
                        if (currentQuestions && currentQuestions !== '[]' && currentQuestions !== '') {
                            if (!confirm('Changing test type will clear all selected questions. Continue?')) {
                                // Revert the selection
                                $('#test-type').val($('#test-type').data('previous-value'));
                                return;
                            }
                            // Clear all selected questions
                            selectedQuestions = [];
                            $('#selectedQuestionsInput').val('[]');
                            updateSelectedQuestionsDisplay();
                            updateQuestionCount();
                            clearInlineForm();
                        }

                        // Store current value for comparison
                        $('#test-type').data('previous-value', selectedType);

                        // Update test type indicator
                        updateTestTypeIndicator(selectedType);

                        // Fetch questions for the new type
                        fetchQuestions();

                        // Update inline form visibility
                        toggleObjectiveOptions();
                    };

                    window.updateTestTypeIndicator = function(testType) {
                        var indicator = $('#testTypeIndicator');
                        if (testType == '1') {
                            indicator.removeClass('badge-warning badge-info').addClass('badge-primary');
                            indicator.html('<i class="fa fa-check-circle"></i> Objective Test (Multiple Choice)');
                        } else if (testType == '2') {
                            indicator.removeClass('badge-primary badge-info').addClass('badge-warning');
                            indicator.html('<i class="fa fa-edit"></i> Subjective Test (Descriptive)');
                        }
                    };
                }
                
                // Start initialization
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initFunctions);
                } else {
                    initFunctions();
                }
            })();
            
            // viewtestquestions function - wrapped to wait for jQuery
            (function() {
                function initViewTestQuestions() {
                    if (typeof jQuery === 'undefined') {
                        setTimeout(initViewTestQuestions, 100);
                        return;
                    }
                    
                    var $ = jQuery;
                    
                    window.viewtestquestions = function() {
                        var id = $('#id').val();
                        $.ajax({
                            url: "{{ url('tutor/onlinetestquestions') }}/" + id,
                            type: "GET",
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            success: function(result) {
                                console.log(JSON.parse(result.subjects.question_id));
                                $("#questiondata").val(JSON.parse(result.subjects.question_id));
                            }
                        });
                    };
                }
                
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initViewTestQuestions);
                } else {
                    initViewTestQuestions();
                }
            })();
        </script>

        <!-- Include Question Selector Modal -->
        @include('tutor.partials.question-selector-modal')

        <!-- Quick Create Question Modal -->
        @include('tutor.partials.quick-create-question-modal')

        <!-- Quiz Preview Modal -->
        @include('tutor.partials.quiz-preview-modal')

        <!-- Include Quiz Builder JavaScript -->
        <!-- Note: jQuery is loaded in footer.blade.php via Bootstrap bundle -->
        <!-- Add cache busting and ensure it loads after jQuery -->
        <script>
            // Ensure jQuery is loaded before our script
            (function() {
                function loadQuizBuilder() {
                    if (typeof jQuery === 'undefined') {
                        // Wait a bit more for jQuery to load
                        setTimeout(loadQuizBuilder, 100);
                        return;
                    }
                    
                    // jQuery is ready, load our script
                    var script = document.createElement('script');
                    script.src = "{{ asset('js/tutor-quiz-builder.js') }}?v={{ time() }}";
                    script.async = false;
                    document.body.appendChild(script);
                }
                
                // Start loading
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', loadQuizBuilder);
                } else {
                    loadQuizBuilder();
                }
            })();
        </script>

        <!-- Form Validation and Initialization -->
        <script>
            // Prevent sidebar from closing when editing a test
            @if(isset($tdata))
            (function() {
                // Force sidebar to stay open when editing
                function keepSidebarOpen() {
                    var body = document.body;
                    var html = document.documentElement;

                    // Remove sidebar collapse classes
                    body.classList.remove('sidebar-icon-only');
                    body.classList.remove('sidebar-hidden');
                    body.classList.remove('vertical-sidebar-enable');
                    body.classList.remove('twocolumn-panel');

                    // Ensure sidebar is visible
                    if (html.getAttribute('data-sidebar-size') === 'sm' ||
                        html.getAttribute('data-sidebar-size') === 'sm-hover') {
                        html.setAttribute('data-sidebar-size', 'lg');
                    }

                    // Ensure proper layout
                    if (html.getAttribute('data-layout') === 'semibox' &&
                        html.getAttribute('data-sidebar-visibility') === 'hidden') {
                        html.setAttribute('data-sidebar-visibility', 'show');
                    }
                }

                // Prevent hamburger menu from toggling sidebar when editing
                function preventSidebarToggle() {
                    var hamburgerBtn = document.getElementById('topnav-hamburger-icon');
                    if (hamburgerBtn) {
                        hamburgerBtn.style.pointerEvents = 'none';
                        hamburgerBtn.style.opacity = '0.5';
                    }
                }

                // Run immediately
                keepSidebarOpen();
                preventSidebarToggle();

                // Also run after delays to override any automatic behavior
                setTimeout(keepSidebarOpen, 100);
                setTimeout(keepSidebarOpen, 500);
                setTimeout(keepSidebarOpen, 1000);

                // Override any window resize events that might affect sidebar
                var originalResize = window.onresize;
                window.onresize = function() {
                    if (originalResize) {
                        originalResize.apply(this, arguments);
                    }
                    setTimeout(keepSidebarOpen, 50);
                };
            })();
            @endif

            // Wait for jQuery to be available
            (function() {
                function initFormScripts() {
                    // Check if jQuery is available
                    if (typeof jQuery === 'undefined') {
                        // Try again after a short delay
                        setTimeout(initFormScripts, 100);
                        return;
                    }

                    var $ = jQuery;

                    $(document).ready(function() {
                        // Sync topic from main form to inline form
                        $('#topic').on('input', function() {
                            $('#inlineTopic').val($(this).val());
                        });

                        // Initialize form when subject/class changes
                        $('#subject, #classname').on('change', function() {
                            $('#inlineSubjectId').val($('#subject').val());
                            $('#inlineClassId').val($('#classname').val());
                        });

                        // Sync test type and show/hide options
                        $('#test-type').on('change', function() {
                            const testType = $(this).val();
                            $('#inlineTestType').val(testType);
                            
                            // Show/hide objective options
                            if (testType == '1') {
                                $('#inlineObjectiveOptions').show();
                                $('#inlineOptionA, #inlineOptionB, #inlineOptionC, #inlineOptionD').prop('required', true);
                            } else {
                                $('#inlineObjectiveOptions').hide();
                                $('#inlineOptionA, #inlineOptionB, #inlineOptionC, #inlineOptionD').prop('required', false);
                            }
                        });

                        // Initialize on page load
                        if ($('#subject').val()) {
                            $('#inlineSubjectId').val($('#subject').val());
                        }
                        if ($('#classname').val()) {
                            $('#inlineClassId').val($('#classname').val());
                        }

                        // Initialize test type display and store initial value
                        const initialTestType = $('#test-type').val() || '1';
                        $('#test-type').data('previous-value', initialTestType);

                        // Initialize test type indicator (only for new tests)
                        @if(!isset($tdata))
                        updateTestTypeIndicator(initialTestType);
                        @endif

                        if (initialTestType == '1') {
                            $('#inlineObjectiveOptions').show();
                            $('#inlineOptionA, #inlineOptionB, #inlineOptionC, #inlineOptionD').prop('required', true);
                        }

                        // Form submission handler - use event delegation to catch all submits
                        $(document).on('submit', 'form', function(e) {
                            const formAction = $(this).attr('action');
                            console.log('=== FORM SUBMIT EVENT FIRED ===');
                            console.log('Form action:', formAction);
                            
                            // Only handle quiz form
                            if (!formAction || !formAction.includes('onlinetests')) {
                                console.log('Not quiz form, allowing normal submit');
                                return true;
                            }
                            
                            // Check topic field before checking questions
                            const topicValue = $('#topic').val();
                            console.log('Form submit - Topic value:', topicValue);
                            
                            if (!topicValue || topicValue.trim() === '') {
                                console.log('❌ No topic - preventing submission');
                                e.preventDefault();
                                e.stopPropagation();
                                alert('Please enter a topic for your quiz!');
                                $('#topic').focus();
                                return false;
                            }
                            
                            const selectedQuestions = $('#selectedQuestionsInput').val();
                            console.log('Selected questions value:', selectedQuestions);
                            
                            // Check if questions are selected
                            if (!selectedQuestions || selectedQuestions === '[]' || selectedQuestions === '' || selectedQuestions === null) {
                                console.log('❌ No questions - preventing submission');
                                e.preventDefault();
                                e.stopPropagation();
                                alert('Please add at least one question to your quiz!\n\nGo to "Create Questions" tab to add questions, or "Use Existing Questions" tab to select from question bank.');
                                $('#quizBuilderTabs a[href="#create-questions"]').tab('show');
                                return false;
                            }
                            
                            // Validate and format question data
                            try {
                                const questionIds = JSON.parse(selectedQuestions);
                                console.log('✅ Parsed question IDs:', questionIds);
                                console.log('Is array?', Array.isArray(questionIds));
                                console.log('Length:', questionIds.length);
                                
                                if (!Array.isArray(questionIds) || questionIds.length === 0) {
                                    console.log('❌ Invalid array - preventing submission');
                                    e.preventDefault();
                                    e.stopPropagation();
                                    alert('Please add at least one question to your quiz!');
                                    $('#quizBuilderTabs a[href="#create-questions"]').tab('show');
                                    return false;
                                }
                                
                                // Ensure the hidden input has the correct value
                                $('#selectedQuestionsInput').val(JSON.stringify(questionIds));
                                
                                // Ensure topic is set
                                $('#topic').val(topicValue.trim());
                                
                                console.log('✅✅✅ FORM IS VALID - SUBMITTING NOW ✅✅✅');
                                console.log('Final question data:', JSON.stringify(questionIds));
                                console.log('Final topic value:', $('#topic').val());
                                console.log('Form action URL:', formAction);
                                
                                // CRITICAL: Don't prevent default - allow form to submit!
                                // Return true to allow submission
                                return true;
                            } catch (err) {
                                console.error('❌ Error parsing questions:', err);
                                e.preventDefault();
                                e.stopPropagation();
                                alert('Error processing questions. Please try adding questions again.');
                                return false;
                            }
                        });
                        
                        // Handle Save button click - submit form programmatically
                        $(document).on('click', '#btnSaveTest', function(e) {
                            e.preventDefault(); // Always prevent default, we'll submit manually
                            console.log('=== SAVE TEST BUTTON CLICKED ===');
                            const selectedQuestions = $('#selectedQuestionsInput').val();
                            console.log('Questions in input:', selectedQuestions);
                            
                            // Validate required form fields before submitting
                            const topic = $('#topic').val();
                            const testname = $('#testname').val();
                            const testdescription = $('#testdescription').val();
                            const classname = $('#classname').val();
                            const subject = $('#subject').val();
                            const duration = $('#duration').val();
                            
                            console.log('Form field values:');
                            console.log('Topic:', topic);
                            console.log('Test name:', testname);
                            console.log('Class:', classname);
                            console.log('Subject:', subject);
                            console.log('Duration:', duration);
                            
                            // Check required fields
                            if (!topic || topic.trim() === '') {
                                alert('Please enter a topic for your quiz!');
                                $('#topic').focus();
                                return false;
                            }
                            
                            if (!testname || testname.trim() === '') {
                                alert('Please enter a test name!');
                                $('#testname').focus();
                                return false;
                            }
                            
                            if (!testdescription || testdescription.trim() === '') {
                                alert('Please enter a test description!');
                                $('#testdescription').focus();
                                return false;
                            }
                            
                            if (!classname || classname === '') {
                                alert('Please select a class!');
                                $('#classname').focus();
                                return false;
                            }
                            
                            if (!subject || subject === '') {
                                alert('Please select a subject!');
                                $('#subject').focus();
                                return false;
                            }
                            
                            if (!duration || duration === '') {
                                alert('Please enter test duration!');
                                $('#duration').focus();
                                return false;
                            }
                            
                            // Check if questions exist
                            if (!selectedQuestions || selectedQuestions === '[]' || selectedQuestions === '' || selectedQuestions === null) {
                                console.log('❌ No questions');
                                alert('Please add at least one question to your quiz!');
                                $('#quizBuilderTabs a[href="#create-questions"]').tab('show');
                                return false;
                            }
                            
                            // Validate question format
                            try {
                                const questionIds = JSON.parse(selectedQuestions);
                                if (!Array.isArray(questionIds) || questionIds.length === 0) {
                                    console.log('❌ Invalid question array');
                                    alert('Please add at least one question to your quiz!');
                                    return false;
                                }
                                
                                console.log('✅ Questions validated:', questionIds.length, 'questions');
                                
                                // Ensure data is correct
                                $('#selectedQuestionsInput').val(JSON.stringify(questionIds));
                                
                                // Find the form and submit it
                                const form = $('form[action*="onlinetests"]');
                                console.log('Form found:', form.length);
                                
                                if (form.length === 0) {
                                    // Try to find any form
                                    const anyForm = $('form').first();
                                    console.log('Trying alternative form selector, found:', anyForm.length);
                                    if (anyForm.length > 0) {
                                        console.log('✅ Submitting alternative form...');
                                        anyForm[0].submit();
                                        return false;
                                    } else {
                                        console.error('❌ No form found at all!');
                                        alert('Error: Form not found. Please refresh the page and try again.');
                                        return false;
                                    }
                                }
                                
                                console.log('✅ All validations passed - Submitting form...');
                                console.log('Form action:', form.attr('action'));
                                console.log('Form method:', form.attr('method'));
                                
                                // Double-check topic value one more time
                                const topicValue = $('#topic').val();
                                console.log('Topic value:', topicValue);
                                console.log('Topic field exists:', $('#topic').length);
                                console.log('Topic field name:', $('#topic').attr('name'));
                                console.log('Topic field is in form:', form.find('#topic').length > 0);
                                
                                if (!topicValue || topicValue.trim() === '') {
                                    alert('Topic field is empty. Please enter a topic.');
                                    $('#topic').focus();
                                    return false;
                                }
                                
                                // Ensure topic is set and trimmed in the visible field
                                const trimmedTopic = topicValue.trim();
                                $('#topic').val(trimmedTopic);
                                
                                // Verify topic is in the form
                                const topicInForm = form.find('#topic');
                                if (topicInForm.length === 0) {
                                    console.error('❌ Topic field not found in form!');
                                    alert('Error: Topic field not found in form. Please refresh the page.');
                                    return false;
                                }
                                
                                // Double-check the value is set
                                const finalTopicValue = $('#topic').val();
                                console.log('Final topic value in field:', finalTopicValue);
                                console.log('Topic field name attribute:', $('#topic').attr('name'));
                                console.log('Topic field type:', $('#topic').attr('type'));
                                
                                // Verify the field will be included in form submission
                                if (!finalTopicValue || finalTopicValue.trim() === '') {
                                    alert('Topic value is empty. Please enter a topic.');
                                    $('#topic').focus();
                                    return false;
                                }
                                
                                // Log all form fields to debug
                                console.log('All form inputs:');
                                form.find('input, select, textarea').each(function() {
                                    const $field = $(this);
                                    if ($field.attr('name')) {
                                        console.log('  - ' + $field.attr('name') + ' = ' + $field.val());
                                    }
                                });
                                
                                console.log('All form data (first 500 chars):', form.serialize().substring(0, 500));
                                
                                // Use native submit to ensure all fields are included
                                // This bypasses jQuery handlers and submits directly
                                console.log('Submitting form now...');
                                form[0].submit();
                                
                            } catch (err) {
                                console.error('❌ Error parsing questions:', err);
                                alert('Error processing questions. Please try again.');
                                return false;
                            }
                        });
                    });
                }
                
                // Start initialization
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initFormScripts);
                } else {
                    initFormScripts();
                }
            })();
        </script>
    @endsection
