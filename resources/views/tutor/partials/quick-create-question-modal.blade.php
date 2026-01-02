<!-- Quick Create Question Modal -->
<div class="modal fade" id="quickCreateQuestionModal" tabindex="-1" role="dialog" aria-labelledby="quickCreateQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="quickCreateQuestionModalLabel">
                    <i class="fas fa-plus-circle"></i> Create New Question
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickCreateQuestionForm">
                <div class="modal-body">
                    <input type="hidden" id="quickCreateSubjectId" name="subject_id">
                    <input type="hidden" id="quickCreateClassId" name="class_id">
                    <input type="hidden" id="quickCreateTestType" name="type">

                    <div class="quick-question-form">
                        <div class="form-group">
                            <label>Topic <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quickCreateTopic" name="topic" required placeholder="e.g., Basic Math, Photosynthesis">
                        </div>

                        <div class="form-group">
                            <label>Question <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="quickQuestionText" name="question" rows="4" required placeholder="Enter your question here..."></textarea>
                            <small class="text-muted">You can use simple text formatting</small>
                        </div>

                        <!-- Objective Question Options -->
                        <div id="objectiveOptions" style="display: none;">
                            <div class="quick-options-grid">
                                <div class="form-group">
                                    <label>Option A <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="quickOptionA" name="optiona" placeholder="Enter option A">
                                </div>
                                <div class="form-group">
                                    <label>Option B <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="quickOptionB" name="optionb" placeholder="Enter option B">
                                </div>
                                <div class="form-group">
                                    <label>Option C <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="quickOptionC" name="optionc" placeholder="Enter option C">
                                </div>
                                <div class="form-group">
                                    <label>Option D <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="quickOptionD" name="optiond" placeholder="Enter option D">
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label>Correct Answer <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="quickCorrectAnswer" id="quickCorrectA" value="A">
                                            <label class="form-check-label" for="quickCorrectA">Option A</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="quickCorrectAnswer" id="quickCorrectB" value="B">
                                            <label class="form-check-label" for="quickCorrectB">Option B</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="quickCorrectAnswer" id="quickCorrectC" value="C">
                                            <label class="form-check-label" for="quickCorrectC">Option C</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="quickCorrectAnswer" id="quickCorrectD" value="D">
                                            <label class="form-check-label" for="quickCorrectD">Option D</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Remarks (Optional)</label>
                            <textarea class="form-control" id="quickCreateRemarks" name="remarks" rows="2" placeholder="Any additional notes or instructions..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="saveQuickQuestion">
                        <i class="fas fa-save"></i> Create & Add to Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Wait for jQuery before using it
    (function() {
        function initQuickCreateModal() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initQuickCreateModal, 100);
                return;
            }
            
            var $ = jQuery;
            
            // Show/hide objective options based on test type
            $(document).ready(function() {
                $('#quickCreateTestType').on('change', function() {
                    if ($(this).val() == '1') {
                        $('#objectiveOptions').show();
                        $('#quickOptionA, #quickOptionB, #quickOptionC, #quickOptionD').prop('required', true);
                    } else {
                        $('#objectiveOptions').hide();
                        $('#quickOptionA, #quickOptionB, #quickOptionC, #quickOptionD').prop('required', false);
                    }
                });

                // Trigger on modal show
                $('#quickCreateQuestionModal').on('show.bs.modal', function() {
                    const testType = $('#quickCreateTestType').val();
                    if (testType == '1') {
                        $('#objectiveOptions').show();
                    } else {
                        $('#objectiveOptions').hide();
                    }
                });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initQuickCreateModal);
        } else {
            initQuickCreateModal();
        }
    })();
</script>

