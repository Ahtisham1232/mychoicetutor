@extends('student.layouts.main')
@section('main-section')
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
                .timer{
                    background: #6161c9;
                    color: white;
                    padding: 11px;
                    border: none;
                    border-radius: 20px;
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
                    <h3 id="questionBox"></h3>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option1" value="1" data-question-id="" required>
                    <label for="option1"></label>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option2" value="2" data-question-id="" required>
                    <label for="option2"></label>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option3" value="3" data-question-id="" required>
                    <label for="option3"></label>
                </div>
                <div class="col box" id="optionbox">
                    <input name="option" type="radio" id="option4" value="4" data-question-id="" required>
                    <label for="option4"></label>
                </div>
                {{-- @endforeach --}}


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
                    <h4>1 &rpar; "What least number must be added to 1056, so that the sum is completely divisible by 23 ?
                    </h4>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 2</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 3</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 18</label></div>
                    <div class="optionDiv"><input type="radio"><label>&nbsp; 21</label></div>
                </div>
            </div>
        </div>


        <!-- content-wrapper ends -->
        <script>
            // jQuery.noConflict();

            // Now use jQuery instead of $
            // jQuery(document).ready(function($) {
            // Your code that uses jQuery
            // You can use $ here if you're sure it won't conflict with other libraries
            // function goback(){
            // $('#openmodal').modal('show');
            // }
            // });

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
            // ================ // =======================

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

            ///////////////

            let index = 0;
            let prevSelected = 0;
            let correct = 0,
                incorrect = 0,
                total = quizData.length;
            let questionBox = document.getElementById("questionBox");
            let allInputs = document.querySelectorAll("input[type='radio']")
            const loadQuestion = () => {
                if (index === total) {
                    return quizConfirmation();
                }

                reset();
                const data = quizData[index];
                console.log(data);
                questionBox.innerHTML = `${index + 1}.  ${data.question}`;

                // Check if there's a selected answer for this question and pre-select it
                prevSelected = selectedAnswers[index];

                if (prevSelected) {
                    const selectedInput = document.querySelector(
                        `input[name="option"][value="${prevSelected.answer}"][data-question-id="${prevSelected.questionId}"]`
                    );
                    if (selectedInput) {
                        selectedInput.checked = true;
                    }
                }
                qid = document.getElementById('testid').value;
                allInputs[0].value = `${data.id},1,${qid}`;
                allInputs[0].nextElementSibling.innerText = data.option1;

                allInputs[1].value = `${data.id},2,${qid}`;
                allInputs[1].nextElementSibling.innerText = data.option2;

                allInputs[2].value = `${data.id},3,${qid}`;
                allInputs[2].nextElementSibling.innerText = data.option3;

                allInputs[3].value = `${data.id},4,${qid}`;
                allInputs[3].nextElementSibling.innerText = data.option4;
            };


            document.querySelector("#next").addEventListener("click", function() {
                const data = quizData[index];
                var ans = getAnswer();
                if (ans == undefined) {
                    ans = null;
                }
                if (ans == "") {
                    ans = null;
                }
                if (ans !== null) {

                    selectedAnswers[index] = ans; // Store the selected answer in the array
                }

                if (index > (total - 3)) {
                    document.getElementById('next').innerHTML = "Submit"
                } else {
                    document.getElementById('next').innerHTML = "Next"
                }
                if (index < total) {

                    index++;
                }
                document.getElementById('back').disabled = false;
                loadQuestion();

                // Save responses using AJAX
                // saveResponses(selectedAnswers);
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

                responses = selectedAnswers;
                // Make an AJAX POST request to the Laravel route
                fetch('{{ route('student.save.responses') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Assuming you have a CSRF token
                        },
                        body: JSON.stringify({
                            responses
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
                const data = quizData[index];
                var ans = getAnswer();
                if (ans == undefined) {
                    ans = null;
                }
                document.getElementById('next').innerHTML = "Next"
                if (ans !== null) {
                    selectedAnswers[index] = ans; // Store the selected answer in the array
                }

                if (index == total) {
                    index--;
                }
                if (index > 0) {
                    index--;
                    loadQuestion();

                    prevSelected = selectedAnswers[index];
                    if (prevSelected) {
                        document.querySelector(`input[name="option"][value="${prevSelected}"]`).checked = true;
                    }
                }

            });


            const getAnswer = () => {
                let ans;
                let questionId;
                allInputs.forEach((inputEl) => {
                    if (inputEl.checked) {
                        ans = inputEl.value;
                    }
                });
                return ans;
                // return ans ? {
                //     questionId,
                //     answer: ans
                // } : null;
            };


            const reset = () => {
                allInputs.forEach(
                    (inputEl) => {
                        inputEl.checked = false;
                    }
                )
            }
            allInputs.forEach(inputEl => {
                inputEl.addEventListener("dblclick", function() {
                    this.checked = false; // Clear the selected response on double-click
                    selectedAnswers[index] = null; // Clear the selected answer for this question
                });
            });

            const quizEnd = () => {
                // console.log(document.getElementsByClassName("container"));
                document.getElementsByClassName("contnr")[0].innerHTML = `
        <div class="col text-center">
        
            <h3 class="w-100 "> Hii,<br> you've scored ${correct} / ${total} </h3><br><br>
            <img src="/images/icons/submitted.gif" width="130" height="100"><br><br>

             <a class="btn btn-primary" href="testPractice.html">Retake</a>
             
        </div>`

                // document.getElementById('topSec').hidden=true;
                document.getElementById('answerSheet').hidden = false;
            }

            loadQuestion(index);

            // JavaScript
            document.addEventListener("DOMContentLoaded", function() {
                const openmodalBtn = document.getElementById("openmodalBtn");
                const openmodal = document.getElementById("openmodal");
                const closeButton = openmodal.querySelector(".close");

                openmodalBtn.addEventListener("click", function() {
                    alert('rrr')
                    openmodal.style.display = "block";
                });

                closeButton.addEventListener("click", function() {
                    openmodal.style.display = "none";
                });

                window.addEventListener("click", function(event) {
                    if (event.target === openmodal) {
                        openmodal.style.display = "none";
                    }
                });
            });

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
        </script>
    @endsection
