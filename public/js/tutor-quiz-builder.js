/**
 * Tutor Quiz Builder - Enhanced UX for Quiz Creation
 * Note: This script requires jQuery to be loaded before it
 */

// Wait for both jQuery and DOM to be ready
(function() {
    'use strict';
    
    function initQuizBuilder() {
        // Check if jQuery is available
        if (typeof jQuery === 'undefined') {
            console.error('Quiz Builder: jQuery is not loaded! Please ensure jQuery is loaded before this script.');
            // Try again after a short delay
            setTimeout(initQuizBuilder, 200);
            return;
        }
        
        var $ = jQuery;
        console.log('Quiz Builder: jQuery found, version:', $.fn.jquery);
        
        // Wait for DOM to be ready
        $(document).ready(function() {
            console.log('Quiz Builder: Document ready, initializing...');
    let selectedQuestions = [];
    let createdQuestions = []; // Questions created inline
    let currentSubjectId = null;
    let currentTestType = null;
    let currentPage = 1;
    let searchTimeout = null;
    let questionCounter = 0; // For numbering questions

    // Initialize
    initializeQuizBuilder();

    /**
     * Initialize the quiz builder
     */
    function initializeQuizBuilder() {
        // Load selected questions if editing
        loadExistingQuestions();

        // Setup event listeners
        setupEventListeners();

        // Update selected questions display
        updateSelectedQuestionsDisplay();

        // Auto-preview if editing and preview tab is active
        autoPreviewOnEdit();
    }

    /**
     * Auto-preview quiz if editing and preview tab is active
     */
    function autoPreviewOnEdit() {
        // Check if we're editing (existingQuestionsData has value) and preview tab is active
        const existingQuestions = $('#existingQuestionsData').val();
        const previewTabActive = $('#preview-tab').hasClass('active');

        if (existingQuestions && selectedQuestions.length > 0 && previewTabActive) {
            console.log('Auto-previewing quiz for editing...');
            setTimeout(function() {
                previewQuiz();
            }, 500); // Small delay to ensure everything is loaded
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Update subject ID when subject changes
        $('#subject').on('change', function() {
            currentSubjectId = $(this).val();
            $('#inlineSubjectId').val(currentSubjectId);
        });

        // Update test type when it changes
        $('#test-type').on('change', function() {
            currentTestType = $(this).val();
            $('#inlineTestType').val(currentTestType);
            toggleObjectiveOptions();
        });

        // Initialize inline form
        initializeInlineForm();

        // Add question inline
        $('#btnAddQuestionInline').on('click', function(e) {
            e.preventDefault();
            console.log('Add Question button clicked');

            // Validate question type matches test type
            var testType = $('#test-type').val();
            var questionType = $('#inlineTestType').val();

            if (testType != questionType) {
                alert('Question type must match the selected test type!');
                return;
            }

            addQuestionInline();
        });

        // Clear inline form
        $('#btnClearQuestionForm').on('click', function(e) {
            e.preventDefault();
            clearInlineForm();
        });

        // Open question selector modal
        $('#btnSelectQuestions').on('click', function(e) {
            e.preventDefault();
            console.log('Select Questions button clicked');
            openQuestionSelector();
        });

        // Question selector modal events
        $('#questionSelectorModal').on('show.bs.modal', function() {
            currentSubjectId = $('#subject').val();
            currentTestType = $('#test-type').val();
            
            if (!currentSubjectId || !currentTestType) {
                alert('Please select Class, Subject, and Test Type first!');
                $(this).modal('hide');
                return;
            }
            
            loadQuestions();
        });

        // Search questions
        $('#questionSearchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentPage = 1;
                loadQuestions();
            }, 500);
        });

        // Filter by type
        $('#questionTypeFilter').on('change', function() {
            currentPage = 1;
            loadQuestions();
        });

        // Question checkbox change
        $(document).on('change', '.question-checkbox', function() {
            const questionId = parseInt($(this).val());
            const isChecked = $(this).is(':checked');
            
            if (isChecked) {
                if (!selectedQuestions.includes(questionId)) {
                    selectedQuestions.push(questionId);
                }
                $(this).closest('.question-card').addClass('selected');
            } else {
                selectedQuestions = selectedQuestions.filter(id => id !== questionId);
                $(this).closest('.question-card').removeClass('selected');
            }
            
            updateSelectionCount();
        });

        // Add selected questions
        $('#addSelectedQuestionsBtn').on('click', function() {
            addSelectedQuestions();
        });

        // Clear selection
        $('#clearSelection').on('click', function() {
            clearSelection();
        });

        // Quick create question
        $('#btnQuickCreateQuestion').on('click', function() {
            openQuickCreateQuestion();
        });

        // Save quick question
        $('#saveQuickQuestion').on('click', function() {
            saveQuickQuestion();
        });

        // Preview quiz
        $('#btnPreviewQuiz').on('click', function(e) {
            e.preventDefault();
            console.log('Preview Quiz button clicked');
            previewQuiz();
        });

        // Edit questions from preview modal
        $('#btnEditQuestionsFromPreview').on('click', function(e) {
            e.preventDefault();
            $('#quizPreviewModal').modal('hide');
            setTimeout(function() {
                $('#btnSelectQuestions').click();
            }, 300);
        });

        // Cancel buttons - ensure they work with explicit handlers
        // Question selector modal cancel
        $(document).on('click', '#questionSelectorModal .btn-secondary[data-dismiss="modal"], #questionSelectorModal .close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#questionSelectorModal').modal('hide');
        });

        // Quick create question modal cancel
        $(document).on('click', '#quickCreateQuestionModal .btn-secondary[data-dismiss="modal"], #quickCreateQuestionModal .close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#quickCreateQuestionModal').modal('hide');
            // Clear form when canceling
            clearInlineForm();
        });

        // Quiz preview modal cancel
        $(document).on('click', '#quizPreviewModal .btn-secondary[data-dismiss="modal"], #quizPreviewModal .close', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#quizPreviewModal').modal('hide');
        });

        // Remove selected question
        $(document).on('click', '.remove-selected-question', function() {
            const questionId = parseInt($(this).data('question-id'));
            removeSelectedQuestion(questionId);
        });

        // Subject or test type change - reload questions if modal is open
        $('#subject, #test-type').on('change', function() {
            if ($('#questionSelectorModal').hasClass('show')) {
                currentSubjectId = $('#subject').val();
                currentTestType = $('#test-type').val();
                currentPage = 1;
                loadQuestions();
            }
        });
    }

    /**
     * Open question selector modal
     */
    function openQuestionSelector() {
        const subjectId = $('#subject').val();
        const testType = $('#test-type').val();
        
        if (!subjectId || !testType) {
            alert('Please select Subject and Test Type first!');
            return;
        }
        
        $('#questionSelectorModal').modal('show');
    }

    /**
     * Load questions from server
     */
    function loadQuestions() {
        const subjectId = $('#subject').val() || currentSubjectId;
        const testType = $('#test-type').val() || currentTestType;
        const search = $('#questionSearchInput').val();
        const typeFilter = $('#questionTypeFilter').val();
        
        if (!subjectId || !testType) {
            return;
        }
        
        $('#questionsLoading').show();
        $('#questionsGrid').hide();
        $('#noQuestionsMessage').hide();
        
        $.ajax({
            url: '/tutor/questions/selector',
            type: 'GET',
            data: {
                subject_id: subjectId,
                type: testType,
                search: search,
                type_filter: typeFilter,
                page: currentPage
            },
            success: function(response) {
                $('#questionsLoading').hide();
                
                if (response.questions && response.questions.length > 0) {
                    renderQuestions(response.questions);
                    renderPagination(response.pagination);
                    $('#questionsGrid').show();
                } else {
                    $('#noQuestionsMessage').show();
                    $('#questionsGrid').hide();
                }
            },
            error: function(xhr) {
                $('#questionsLoading').hide();
                alert('Error loading questions. Please try again.');
                console.error(xhr);
            }
        });
    }

    /**
     * Render questions in grid
     */
    function renderQuestions(questions) {
        let html = '';
        
        questions.forEach(function(question, index) {
            const isSelected = selectedQuestions.includes(question.id);
            const questionType = question.type == 1 ? 'Objective' : 'Subjective';
            
            html += `
                <div class="question-card ${isSelected ? 'selected' : ''}" data-question-id="${question.id}">
                    <div class="question-card-header">
                        <div class="form-check question-checkbox-wrapper">
                            <input class="form-check-input question-checkbox" type="checkbox" 
                                   value="${question.id}" 
                                   id="question_${question.id}"
                                   ${isSelected ? 'checked' : ''}>
                            <label class="form-check-label" for="question_${question.id}">
                                <span class="question-number">Q${index + 1}</span>
                            </label>
                        </div>
                        <div class="question-badges">
                            <span class="badge badge-${question.type == 1 ? 'primary' : 'info'}">${questionType}</span>
                            ${question.topic_name ? `<span class="badge badge-secondary">${question.topic_name}</span>` : ''}
                        </div>
                    </div>
                    <div class="question-card-body">
                        <div class="question-text">${stripHtml(question.question || '')}</div>
                        ${renderQuestionOptions(question)}
                        ${question.remarks ? `<div class="question-remarks mt-2"><small class="text-muted"><i class="fas fa-info-circle"></i> ${question.remarks}</small></div>` : ''}
                    </div>
                    <div class="question-card-footer">
                        <small class="text-muted"><i class="fas fa-tag"></i> ID: ${question.id}</small>
                    </div>
                </div>
            `;
        });
        
        $('#questionsGrid').html(html);
    }

    /**
     * Render question options for objective questions
     */
    function renderQuestionOptions(question) {
        if (question.type != 1 || !question.option1) {
            return '';
        }
        
        let correctAnswer = '';
        if (question.correct_option) {
            if (question.correct_option == question.option1) correctAnswer = 'A';
            else if (question.correct_option == question.option2) correctAnswer = 'B';
            else if (question.correct_option == question.option3) correctAnswer = 'C';
            else if (question.correct_option == question.option4) correctAnswer = 'D';
        }
        
        return `
            <div class="question-options mt-3">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">A)</span>
                            <span class="option-text">${question.option1 || ''}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">B)</span>
                            <span class="option-text">${question.option2 || ''}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">C)</span>
                            <span class="option-text">${question.option3 || ''}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">D)</span>
                            <span class="option-text">${question.option4 || ''}</span>
                        </div>
                    </div>
                </div>
                ${correctAnswer ? `
                    <div class="correct-answer-badge mt-2">
                        <i class="fas fa-check-circle text-success"></i> 
                        <strong>Correct Answer:</strong> ${correctAnswer}
                    </div>
                ` : ''}
            </div>
        `;
    }

    /**
     * Render pagination
     */
    function renderPagination(pagination) {
        if (!pagination || !pagination.links) {
            $('#questionsPagination').html('');
            return;
        }
        
        $('#questionsPagination').html(pagination.links);
        
        // Handle pagination clicks
        $(document).off('click', '.pagination a').on('click', '.pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            if (url) {
                const page = new URL(url).searchParams.get('page') || 1;
                currentPage = parseInt(page);
                loadQuestions();
            }
        });
    }

    /**
     * Update selection count
     */
    function updateSelectionCount() {
        const count = selectedQuestions.length;
        $('#selectedCount').text(count);
        $('#footerSelectedCount').text(count);
        $('#addSelectedQuestionsBtn').prop('disabled', count === 0);
        updateQuestionCount();
    }

    /**
     * Add selected questions to form
     */
    function addSelectedQuestions() {
        if (selectedQuestions.length === 0) {
            alert('Please select at least one question!');
            return;
        }
        
        // Update hidden input with selected question IDs
        $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
        
        // Update display
        updateSelectedQuestionsDisplay();
        
        // Close modal
        $('#questionSelectorModal').modal('hide');
    }

    /**
     * Update selected questions display
     */
    function updateSelectedQuestionsDisplay() {
        updateQuestionCount();
        
        // Only show selected questions from question bank (not inline created ones)
        const bankQuestions = selectedQuestions.filter(id => 
            !createdQuestions.some(q => q.id === id)
        );
        
        if (bankQuestions.length === 0) {
            $('#selectedQuestionsContainer').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> No questions selected from question bank yet.
                </div>
            `);
            return;
        }
        
        // Load question details for display
        loadSelectedQuestionsDetails();
    }

    /**
     * Load details of selected questions
     */
    function loadSelectedQuestionsDetails() {
        if (selectedQuestions.length === 0) return;
        
        $.ajax({
            url: '/tutor/questions/details',
            type: 'POST',
            data: {
                question_ids: selectedQuestions,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.questions) {
                    renderSelectedQuestions(response.questions);
                }
            },
            error: function() {
                // Fallback: just show IDs
                renderSelectedQuestionsByIds();
            }
        });
    }

    /**
     * Render selected questions
     */
    function renderSelectedQuestions(questions) {
        let html = `
            <div class="selected-questions-container">
                <div class="selected-questions-header">
                    <h5><i class="fas fa-check-circle text-success"></i> Selected Questions (${questions.length})</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectQuestions">
                        <i class="fas fa-plus"></i> Add More
                    </button>
                </div>
                <div class="selected-questions-list">
        `;
        
        questions.forEach(function(question, index) {
            html += `
                <div class="selected-question-chip">
                    <span>Q${index + 1}: ${stripHtml(question.question || '').substring(0, 30)}...</span>
                    <button type="button" class="remove-btn remove-selected-question" data-question-id="${question.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
        
        $('#selectedQuestionsContainer').html(html);
    }

    /**
     * Render selected questions by IDs (fallback)
     */
    function renderSelectedQuestionsByIds() {
        let html = `
            <div class="selected-questions-container">
                <div class="selected-questions-header">
                    <h5><i class="fas fa-check-circle text-success"></i> Selected Questions (${selectedQuestions.length})</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectQuestions">
                        <i class="fas fa-plus"></i> Add More
                    </button>
                </div>
                <div class="selected-questions-list">
        `;
        
        selectedQuestions.forEach(function(id, index) {
            html += `
                <div class="selected-question-chip">
                    <span>Question ${index + 1} (ID: ${id})</span>
                    <button type="button" class="remove-btn remove-selected-question" data-question-id="${id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
        
        $('#selectedQuestionsContainer').html(html);
    }

    /**
     * Remove selected question
     */
    function removeSelectedQuestion(questionId) {
        selectedQuestions = selectedQuestions.filter(id => id !== questionId);
        $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
        updateSelectedQuestionsDisplay();
    }

    /**
     * Clear all selections
     */
    function clearSelection() {
        if (confirm('Are you sure you want to clear all selected questions?')) {
            selectedQuestions = [];
            $('.question-checkbox').prop('checked', false);
            $('.question-card').removeClass('selected');
            updateSelectionCount();
        }
    }

    /**
     * Load existing questions if editing
     */
    function loadExistingQuestions() {
        const existingQuestions = $('#existingQuestionsData').val();
        if (existingQuestions) {
            try {
                const parsed = JSON.parse(existingQuestions);
                if (Array.isArray(parsed)) {
                    selectedQuestions = parsed;
                } else {
                    // If it's a string of IDs, convert to array
                    selectedQuestions = parsed.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
                }
                if (selectedQuestions.length > 0) {
                    $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
                    // Load question details to show in preview
                    loadSelectedQuestionsDetails();
                    updateQuestionCount();
                }
            } catch (e) {
                console.error('Error parsing existing questions:', e);
                // Try to parse as comma-separated string
                try {
                    selectedQuestions = existingQuestions.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
                    if (selectedQuestions.length > 0) {
                        $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
                        loadSelectedQuestionsDetails();
                        updateQuestionCount();
                    }
                } catch (e2) {
                    console.error('Error parsing existing questions as string:', e2);
                }
            }
        }
    }

    /**
     * Open quick create question modal
     */
    function openQuickCreateQuestion() {
        const subjectId = $('#subject').val();
        const classId = $('#classname').val();
        
        if (!subjectId || !classId) {
            alert('Please select Class and Subject first!');
            return;
        }
        
        // Pre-fill form
        $('#quickCreateSubjectId').val(subjectId);
        $('#quickCreateClassId').val(classId);
        $('#quickCreateTestType').val($('#test-type').val());
        
        $('#quickCreateQuestionModal').modal('show');
    }

    /**
     * Save quick question
     */
    function saveQuickQuestion() {
        const formData = {
            subject_id: $('#quickCreateSubjectId').val(),
            class_id: $('#quickCreateClassId').val(),
            topic: $('#quickCreateTopic').val(),
            question: CKEDITOR.instances.quickQuestionEditor ? CKEDITOR.instances.quickQuestionEditor.getData() : $('#quickQuestionText').val(),
            type: $('#quickCreateTestType').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        if (formData.type == 1) {
            formData.optiona = $('#quickOptionA').val();
            formData.optionb = $('#quickOptionB').val();
            formData.optionc = $('#quickOptionC').val();
            formData.optiond = $('#quickOptionD').val();
            formData.correctanswer = $('input[name="quickCorrectAnswer"]:checked').val();
        }
        
        // Validation
        if (!formData.topic || !formData.question) {
            alert('Please fill in all required fields!');
            return;
        }
        
        if (formData.type == 1 && (!formData.optiona || !formData.optionb || !formData.optionc || !formData.optiond || !formData.correctanswer)) {
            alert('Please fill in all options and select correct answer!');
            return;
        }
        
        $.ajax({
            url: '/tutor/questions/quick-create',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success && response.question) {
                    // Add to selected questions
                    selectedQuestions.push(response.question.id);
                    $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
                    
                    // Reload questions in modal
                    loadQuestions();
                    
                    // Close modal
                    $('#quickCreateQuestionModal').modal('hide');
                    
                    // Reset form
                    $('#quickCreateQuestionForm')[0].reset();
                    if (CKEDITOR.instances.quickQuestionEditor) {
                        CKEDITOR.instances.quickQuestionEditor.setData('');
                    }
                    
                    // Show success message
                    alert('Question created and added to your quiz!');
                    
                    // Update display
                    updateSelectedQuestionsDisplay();
                } else {
                    alert('Error creating question. Please try again.');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error creating question.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    }

    /**
     * Preview quiz
     */
    function previewQuiz() {
        if (selectedQuestions.length === 0) {
            alert('Please select at least one question to preview!');
            return;
        }
        
        const quizData = {
            name: $('#testname').val(),
            description: $('#testdescription').val(),
            type: $('#test-type option:selected').text(),
            duration: $('#duration').val(),
            topic: $('#topic').val(),
            question_ids: selectedQuestions
        };
        
        // Load question details for preview
        $.ajax({
            url: '/tutor/questions/details',
            type: 'POST',
            data: {
                question_ids: selectedQuestions,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.questions) {
                    showQuizPreview(quizData, response.questions);
                }
            },
            error: function() {
                alert('Error loading question details for preview.');
            }
        });
    }

    /**
     * Show quiz preview
     */
    function showQuizPreview(quizData, questions) {
        let previewHtml = `
            <div class="quiz-preview-container">
                <div class="quiz-preview-header">
                    <h4><i class="fas fa-eye"></i> Quiz Preview</h4>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="$('#quizPreviewModal').modal('hide')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
                <div class="quiz-preview-info">
                    <div class="quiz-preview-info-item">
                        <label>Quiz Name</label>
                        <span>${quizData.name || 'N/A'}</span>
                    </div>
                    <div class="quiz-preview-info-item">
                        <label>Type</label>
                        <span>${quizData.type || 'N/A'}</span>
                    </div>
                    <div class="quiz-preview-info-item">
                        <label>Duration</label>
                        <span>${quizData.duration || 'N/A'} minutes</span>
                    </div>
                    <div class="quiz-preview-info-item">
                        <label>Topic</label>
                        <span>${quizData.topic || 'N/A'}</span>
                    </div>
                    <div class="quiz-preview-info-item">
                        <label>Total Questions</label>
                        <span>${questions.length}</span>
                    </div>
                </div>
                ${quizData.description ? `
                    <div class="mt-3">
                        <label><strong>Description:</strong></label>
                        <p>${quizData.description}</p>
                    </div>
                ` : ''}
                <div class="quiz-preview-questions">
                    <h5 class="mb-3"><i class="fas fa-list"></i> Questions (${questions.length})</h5>
        `;
        
        questions.forEach(function(question, index) {
            previewHtml += `
                <div class="preview-question-item">
                    <div class="question-order">${index + 1}</div>
                    <div class="question-text">${stripHtml(question.question || '')}</div>
                    ${renderQuestionOptions(question)}
                </div>
            `;
        });
        
        previewHtml += `
                </div>
            </div>
        `;
        
        $('#quizPreviewContent').html(previewHtml);
        $('#quizPreviewModal').modal('show');
    }

    /**
     * Initialize inline question form
     */
    function initializeInlineForm() {
        // Set initial values
        const classId = $('#classname').val();
        const subjectId = $('#subject').val();
        const testType = $('#test-type').val() || '1';
        
        $('#inlineClassId').val(classId);
        $('#inlineSubjectId').val(subjectId);
        $('#inlineTestType').val(testType);
        currentTestType = testType;
        
        // Show/hide objective options based on initial test type
        toggleObjectiveOptions();
        
        // Also sync topic from main form
        const topic = $('#topic').val();
        if (topic) {
            $('#inlineTopic').val(topic);
        }
    }

    /**
     * Toggle objective options based on test type
     */
    function toggleObjectiveOptions() {
        const testType = $('#test-type').val() || $('#inlineTestType').val() || '1';
        console.log('Toggle objective options, test type:', testType);
        
        if (testType == '1' || testType == 1) {
            $('#inlineObjectiveOptions').show();
            $('#inlineOptionA, #inlineOptionB, #inlineOptionC, #inlineOptionD').prop('required', true);
        } else {
            $('#inlineObjectiveOptions').hide();
            $('#inlineOptionA, #inlineOptionB, #inlineOptionC, #inlineOptionD').prop('required', false);
        }
    }

    /**
     * Add question inline (create and add to quiz)
     */
    function addQuestionInline() {
        // Get form data
        const subjectId = $('#inlineSubjectId').val() || $('#subject').val();
        const classId = $('#inlineClassId').val() || $('#classname').val();
        const topic = $('#inlineTopic').val();
        const question = $('#inlineQuestion').val();
        const testType = $('#inlineTestType').val() || $('#test-type').val() || '1';

        // Validation
        if (!subjectId || !classId) {
            alert('Please select Class and Subject first!');
            return;
        }

        if (!topic || !question) {
            alert('Please fill in Topic and Question!');
            return;
        }

        const formData = {
            subject_id: subjectId,
            class_id: classId,
            topic: topic,
            question: question,
            type: testType,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Add objective options if test type is objective
        if (testType == '1' || testType == 1) {
            formData.optiona = $('#inlineOptionA').val();
            formData.optionb = $('#inlineOptionB').val();
            formData.optionc = $('#inlineOptionC').val();
            formData.optiond = $('#inlineOptionD').val();
            formData.correctanswer = $('input[name="inlineCorrectAnswer"]:checked').val();

            if (!formData.optiona || !formData.optionb || !formData.optionc || !formData.optiond || !formData.correctanswer) {
                alert('Please fill in all 4 options (A, B, C, D) and select the correct answer!');
                return;
            }
        }

        // Show loading
        const btn = $('#btnAddQuestionInline');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        console.log('Sending question data:', formData);

        $.ajax({
            url: '/tutor/questions/quick-create',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Question created successfully:', response);
                if (response.success && response.question) {
                    // Add to created questions
                    const question = response.question;
                    createdQuestions.push(question);
                    selectedQuestions.push(question.id);
                    
                    // Update hidden input
                    const questionIdsJson = JSON.stringify(selectedQuestions);
                    $('#selectedQuestionsInput').val(questionIdsJson);
                    console.log('Question added. Total questions:', selectedQuestions.length);
                    console.log('Hidden input updated to:', questionIdsJson);
                    
                    // Update display
                    renderCreatedQuestions();
                    updateQuestionCount();
                    
                    // Clear form
                    clearInlineForm();
                    
                    // Show success message - question is saved to question bank
                    showSuccessMessage('Question created and added to quiz! It\'s also saved in your question bank.');
                } else {
                    alert('Error: ' + (response.message || 'Failed to create question'));
                }
            },
            error: function(xhr) {
                console.error('Error creating question:', xhr);
                let errorMsg = 'Error creating question.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join('\n');
                    }
                }
                alert(errorMsg);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    }

    /**
     * Render created questions
     */
    function renderCreatedQuestions() {
        if (createdQuestions.length === 0) {
            $('#createdQuestionsContainer').html(`
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i> No questions created yet. Use the form above to add questions.
                </div>
            `);
            return;
        }

        let html = '<div class="list-group">';
        createdQuestions.forEach(function(question, index) {
            const questionType = question.type == 1 ? 'Objective' : 'Subjective';
            html += `
                <div class="list-group-item" data-question-id="${question.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <span class="badge badge-primary mr-2">Q${index + 1}</span>
                                <span class="badge badge-${question.type == 1 ? 'info' : 'secondary'}">${questionType}</span>
                            </h6>
                            <p class="mb-1">${stripHtml(question.question || '').substring(0, 100)}${stripHtml(question.question || '').length > 100 ? '...' : ''}</p>
                            ${question.type == 1 ? `
                                <small class="text-muted">
                                    Options: ${question.option1 || ''} | ${question.option2 || ''} | ${question.option3 || ''} | ${question.option4 || ''}
                                </small>
                            ` : ''}
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-created-question" data-question-id="${question.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        $('#createdQuestionsContainer').html(html);
    }

    /**
     * Remove created question
     */
    $(document).on('click', '.remove-created-question', function() {
        const questionId = parseInt($(this).data('question-id'));
        if (confirm('Remove this question from quiz?')) {
            createdQuestions = createdQuestions.filter(q => q.id !== questionId);
            selectedQuestions = selectedQuestions.filter(id => id !== questionId);
            $('#selectedQuestionsInput').val(JSON.stringify(selectedQuestions));
            renderCreatedQuestions();
            updateQuestionCount();
        }
    });

    /**
     * Clear inline form
     */
    function clearInlineForm() {
        $('#inlineTopic').val('');
        $('#inlineQuestion').val('');
        $('#inlineOptionA').val('');
        $('#inlineOptionB').val('');
        $('#inlineOptionC').val('');
        $('#inlineOptionD').val('');
        $('input[name="inlineCorrectAnswer"]').prop('checked', false);
        $('.btn-group-toggle label').removeClass('active');
    }

    /**
     * Update question count badge
     */
    function updateQuestionCount() {
        const total = selectedQuestions.length;
        $('#totalQuestionsBadge').text(total + ' question' + (total !== 1 ? 's' : ''));
        
        // Debug: Log current state
        console.log('Question count updated:', total, 'Questions:', selectedQuestions);
        console.log('Hidden input value:', $('#selectedQuestionsInput').val());
        
        // Visual feedback
        if (total > 0) {
            $('#totalQuestionsBadge').removeClass('badge-light').addClass('badge-success');
        } else {
            $('#totalQuestionsBadge').removeClass('badge-success').addClass('badge-light');
        }
    }

    /**
     * Show success message
     */
    function showSuccessMessage(message) {
        const alert = $(`
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        $('#create-questions').prepend(alert);
        setTimeout(() => alert.fadeOut(), 3000);
    }

    /**
     * Utility: Strip HTML tags
     */
    function stripHtml(html) {
        const tmp = document.createElement('DIV');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }
        }); // End document ready
    }
    
    // Start initialization - try immediately, then on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQuizBuilder);
    } else {
        initQuizBuilder();
    }
    
})(); // End wrapper



