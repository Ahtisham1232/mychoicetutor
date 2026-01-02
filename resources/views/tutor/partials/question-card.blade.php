<!-- Question Card Component -->
<div class="question-card" data-question-id="{{ $question->id }}" data-question-type="{{ $question->type ?? 1 }}">
    <div class="question-card-header">
        <div class="form-check question-checkbox-wrapper">
            <input class="form-check-input question-checkbox" type="checkbox" 
                   value="{{ $question->id }}" 
                   id="question_{{ $question->id }}">
            <label class="form-check-label" for="question_{{ $question->id }}">
                <span class="question-number">Q{{ $loop->iteration ?? '' }}</span>
            </label>
        </div>
        <div class="question-badges">
            <span class="badge badge-{{ ($question->type ?? 1) == 1 ? 'primary' : 'info' }}">
                {{ ($question->type ?? 1) == 1 ? 'Objective' : 'Subjective' }}
            </span>
            @if(isset($question->topic_name) && $question->topic_name)
                <span class="badge badge-secondary">{{ $question->topic_name }}</span>
            @endif
        </div>
    </div>
    
    <div class="question-card-body">
        <div class="question-text">
            {!! strip_tags($question->question ?? '') !!}
        </div>
        
        @if(($question->type ?? 1) == 1 && isset($question->option1))
            <div class="question-options mt-3">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">A)</span>
                            <span class="option-text">{{ $question->option1 ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">B)</span>
                            <span class="option-text">{{ $question->option2 ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">C)</span>
                            <span class="option-text">{{ $question->option3 ?? '' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="option-item">
                            <span class="option-label">D)</span>
                            <span class="option-text">{{ $question->option4 ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @if(isset($question->correct_option))
                    <div class="correct-answer-badge mt-2">
                        <i class="fas fa-check-circle text-success"></i> 
                        <strong>Correct Answer:</strong> 
                        @if($question->correct_option == $question->option1) A
                        @elseif($question->correct_option == $question->option2) B
                        @elseif($question->correct_option == $question->option3) C
                        @elseif($question->correct_option == $question->option4) D
                        @endif
                    </div>
                @endif
            </div>
        @endif
        
        @if(isset($question->remarks) && $question->remarks)
            <div class="question-remarks mt-2">
                <small class="text-muted"><i class="fas fa-info-circle"></i> {{ $question->remarks }}</small>
            </div>
        @endif
    </div>
    
    <div class="question-card-footer">
        <small class="text-muted">
            <i class="fas fa-tag"></i> ID: {{ $question->id }}
        </small>
    </div>
</div>

