@extends('student.layouts.main')
@section('main-section')
    <style>
        .selectAns {
            border: 1px solid lightgrey;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        input[type='radio'] {
            accent-color: green;
        }

        .ck-powered-by {
            display: none;
        }

        .ck-balloon-panel,
        .ck-powered-by-balloon,
        .ck-balloon-panel_position_border-side_right {
            border: none !important;
        }
    .cke_notification.cke_notification_warning {
        display: none !important; /* Hides the notification */
    }

    /* Submission loading styles */
    #submission-loading {
        backdrop-filter: blur(2px);
    }

    #submission-loading .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Disabled submit button styles */
    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    button:disabled:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }

    </style>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

    <!-- modal -->
    <div class="modal fade" id="openmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-body">


                    <header>
                        <h3 class="text-center mb-4" id="header">Confirm Submission</h3>
                    </header>

                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="id" name="id">
                        <div class="row">
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Questions: {{ $questions->count() }}</label>
                            </div>
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Attempted: <span id="totalAttempted">0</span></label>
                            </div>
                            <div class="col-12 col-md-12 col-ms-6 mb-3">
                                <label>Total Non Attempted: <span id="totalNonAttempted">0</span></label>
                            </div>
                        </div>


                        <button type="button" id="finalsubmit" class="btn btn-sm btn-success float-right"><span
                                class="fa fa-upload"> </span> Confirm Submit</button>
                        <button type="button" class="btn btn-sm btn-danger mr-1 moveRight" data-dismiss="modal"><span
                                class="fa fa-times"></span> Cancel</button>



                    </form>
                </div>
            </div>
        </div>
    </div>
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

                <div class="mb-4 text-center">
                    <input type="hidden" id="testid" name="testid" value="{{ $onlineTest->id }}">
                    <h3>{{ $onlineTest->name }}</h3>
                    <p>{{ $onlineTest->description }}</p>
                    <p id="duration" hidden>{{ $onlineTest->test_duration }}</p>
                    <span id="timer"></span>
                </div>



                <div class="contnr">
                    {{-- @foreach ($questions as $data) --}}
                    <div class="col">
                        <h3 id="questionBox" style="display:flex"></h3>
                    </div>
                    <div class="col box" id="optionbox">
                        <textarea name="editor1" id="subjectiveAnswer"></textarea>
                    </div>
                    <div class="btn-group">
                        <button id="back" class="pre btn btn-warning" disabled>Previous</button>
                        <button id="next" class="next btn btn-success">Next</button>
                    </div>
                </div>
                <div class="mt-5" id="answerSheet" hidden>

                    <button id="goback" onclick="showmodal()" class="pre btn btn-warning">Previous</button>
                    <button id="goback1" hidden data-toggle="modal" data-target="#openmodal"
                        class="pre btn btn-warning">Previous</button>

                    <hr>
                    <h3>Answers</h3>
                    <div class="ansSec mb-3">
                        <h4>1 &rpar; Which one of the following is not a prime number?</h4>
                        <div class="optionDiv"><input name="option" type="radio" id="" value="a"
                                disabled>&nbsp; 31</label></div>
                        <div class="optionDiv"><input name="option" type="radio" id="" value="b"
                                disabled><label>&nbsp; 61</label></div>
                        <div class="optionDiv"><input name="option" type="radio" id="" value="c"
                                disabled><label>&nbsp; 71</label></div>
                        <div class="optionDiv"><input name="option" type="radio" id="" value="d"
                                disabled><label>&nbsp; 91</label></div>
                    </div>
                    <div class="ansSec mb-3">
                        <h4>1 &rpar; "What least number must be added to 1056, so that the sum is completely divisible by 23
                            ?
                        </h4>
                        <div class="optionDiv"><input type="radio"><label>&nbsp; 2</label></div>
                        <div class="optionDiv"><input type="radio"><label>&nbsp; 3</label></div>
                        <div class="optionDiv"><input type="radio"><label>&nbsp; 18</label></div>
                        <div class="optionDiv"><input type="radio"><label>&nbsp; 21</label></div>
                    </div>
                </div>
            </div>

            <script>
                CKEDITOR.replace('editor1');
                CKEDITOR.replace('editor2');

                function getData() {
                    //Get data written in first Editor
                    var editor_data = CKEDITOR.instances['editor1'].getData();
                    //Set data in Second Editor which is written in first Editor
                    CKEDITOR.instances['editor2'].setData(editor_data);
                }
            </script>
            <script>
                // Initialize timer based on local storage or default value
                let timeLimit = localStorage.getItem('remainingTime') || (60 * parseInt(document.getElementById('duration')
                    .innerHTML));
                let timer = setInterval(updateTimer, 1000); // Update timer every second
                let remainingTime = timeLimit;

                function updateTimer() {
                    const timerDisplay = document.getElementById("timer");
                    const minutes = Math.floor(remainingTime / 60);
                    const seconds = remainingTime % 60;

                    timerDisplay.textContent = `Time Left: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                    if (remainingTime <= 0) {
                        clearInterval(timer);
                        timerDisplay.textContent = "Time's up!";
                        // Add your logic to handle time's up (e.g., submitting the quiz)
                    }

                    remainingTime--;

                    // Store remaining time in local storage
                    localStorage.setItem('remainingTime', remainingTime);
                }

                // Check if timer has already expired
                if (remainingTime <= 0) {
                    const timerDisplay = document.getElementById("timer");
                    timerDisplay.textContent = "Time's up!";
                }

                // Loading overlay functions
                function showSubmissionLoading() {
                    // Create loading overlay if it doesn't exist
                    if (!document.getElementById('submission-loading')) {
                        const loadingDiv = document.createElement('div');
                        loadingDiv.id = 'submission-loading';
                        loadingDiv.style.cssText = `
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background-color: rgba(0, 0, 0, 0.7);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            z-index: 9999;
                            color: white;
                            font-size: 18px;
                            flex-direction: column;
                        `;
                        loadingDiv.innerHTML = `
                            <div class="text-center">
                                <div class="spinner-border text-light mb-3" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <h4>Submitting your test...</h4>
                                <p>Please wait, do not close this window.</p>
                            </div>
                        `;
                        document.body.appendChild(loadingDiv);
                    }
                    document.getElementById('submission-loading').style.display = 'flex';
                }

                function hideSubmissionLoading() {
                    const loadingDiv = document.getElementById('submission-loading');
                    if (loadingDiv) {
                        loadingDiv.style.display = 'none';
                    }
                }

                let selectedAnswers = []; // Array to store selected answers for each question

                const quizData = @json($questions); // Convert PHP array to JSON for JavaScript usage

                let index = 0;
                let prevSelected = 0;
                let total = quizData.length;
                let questionBox = document.getElementById("questionBox");
                // let textArea = document.getElementById("subjectiveAnswer");
                let textArea = CKEDITOR.instances.subjectiveAnswer;


                const loadQuestion = () => {
                    if (index == total) {
                        return quizConfirmation();
                    }

                    reset();
                    const data = quizData[index];
                    console.log(data);
                    questionBox.innerHTML = `${index + 1}.  ${data.question}`;

                    // Check if there's a selected answer for this question and pre-fill the text area
                    prevSelected = selectedAnswers[index];
                    textArea.value = prevSelected || '';
                };

                document.querySelector("#next").addEventListener("click", function() {
                    // alert(index)
                    const ans = textArea.getData(); // Get textarea data from CKEditor
                    const testId = document.getElementById("testid").value;
                    const questionId = quizData[index].id;
                    const nextQuestionId = index < total - 1 ? quizData[index + 1].id : null;

                    const tempurl = "{{ url('student/storeSubjectiveDataInTemporaryTable') }}"

                    // Send an AJAX request to store the data in the temporary table
                    fetch(tempurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Replace with your CSRF token
                            },
                            body: JSON.stringify({
                                testId,
                                questionId,
                                answer: ans,
                                nextQuestionId,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {

                            //    console.log(data.message)
                            if (index < total - 1) {
                                CKEDITOR.instances.subjectiveAnswer.setData(data.nextAnswer || '');
                            }

                            selectedAnswers[index] = ans; // Store the subjective answer in the array

                            if (index > (total - 3)) {
                                document.getElementById('next').innerHTML = "Submit";
                            } else {
                                document.getElementById('next').innerHTML = "Next";
                            }

                            if (index < total) {
                                index++;
                            }
                            document.getElementById('back').disabled = false;
                            loadQuestion();

                        })
                        .catch(error => {
                            // Handle errors if any
                        });


                });

                document.querySelector("#finalsubmit").addEventListener("click", function() {
                    // Prevent multiple submissions
                    const submitBtn = this;
                    const originalText = submitBtn.innerHTML;

                    // Disable button and show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="fa fa-spinner fa-spin"></span> Submitting...';
                    submitBtn.style.pointerEvents = 'none';

                    // Hide modal and show loading overlay
                    $('#openmodal').modal('hide');
                    showSubmissionLoading();

                    const testId = document.getElementById("testid").value;

                    // responses = selectedAnswers;
                    // console.log(selectedAnswers);
                    // Make an AJAX POST request to the Laravel route
                    fetch('{{ route('student.save.subjective-responses') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Assuming you have a CSRF token
                            },
                            body: JSON.stringify({
                                testId: testId,
                                questionIds: @json($questionIds)
                            }),
                        })
                        .then(response => response.json())

                        .then(data => {
                            // Success - redirect to results
                            const successMessage = encodeURIComponent(data.message);
                            const redirectUrl = `/student/exams?message=${successMessage}`;
                            window.location.href = redirectUrl;
                        })
                        .catch(error => {
                            // Error - re-enable button and show error
                            console.error('Error saving responses:', error);
                            hideSubmissionLoading();

                            // Re-enable the button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            submitBtn.style.pointerEvents = 'auto';

                            // Show error message
                            alert('Error submitting test. Please try again.');

                            // Re-show the modal
                            $('#openmodal').modal('show');
                        });
                })

                document.querySelector("#back").addEventListener("click", function() {
                    // alert(index);
                    const testId = document.getElementById("testid").value;
                    const prevQuestionId = quizData[index - 1].id; // Get the question ID of the previous question

                    // Send an AJAX request to retrieve the answer for the previous question from the temporary table
                    fetch('{{ route('student.getAnswerFromSubjectiveTempTable') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Replace with your CSRF token
                            },
                            body: JSON.stringify({
                                testId,
                                questionId: prevQuestionId,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Set the retrieved answer in CKEditor
                            textArea.setData(data.answer || '');
                            selectedAnswers[index] = textArea.getData(); // Store the subjective answer
                            document.getElementById('next').innerHTML = "Next"
                            // if (index == total) {
                            //     index--;
                            // }
                            if (index > 0) {
                                index--;
                                loadQuestion();
                            }

                        })
                        .catch(error => {
                            // Handle errors if any
                        });




                });


                const reset = () => {
                    // Reset any relevant UI elements here
                };

                // ... (other code remains unchanged)

                function quizConfirmation() {
                    calculateAttemptedQuestions();
                    // Get a reference to the button element
                    var button = document.getElementById("goback1");

                    // Trigger a click event on the button
                    button.click();
                }

                function calculateAttemptedQuestions() {
                    if (selectedAnswers.length > total) {
                        const secondToLastIndex = selectedAnswers.length - 2; // Index of the second-to-last element
                        if (secondToLastIndex >= 0) {
                            selectedAnswers.splice(secondToLastIndex, 1); // Remove one element at the secondToLastIndex
                        }
                    }
                    const attemptedQuestions = selectedAnswers.filter(answer => answer !== null);
                    const nonAttemptedQuestions = total - attemptedQuestions.length;

                    document.querySelector("#openmodal #totalAttempted").textContent = attemptedQuestions.length;
                    document.querySelector("#openmodal #totalNonAttempted").textContent = nonAttemptedQuestions;
                }

                // Initial load of the first question
                loadQuestion();

                document.addEventListener('DOMContentLoaded', function() {
    const notifications = document.querySelectorAll('.cke_notification.cke_notification_warning');
    notifications.forEach(function(notification) {
        notification.style.display = 'none'; // Hides the notification
    });
});

            </script>
        @endsection
