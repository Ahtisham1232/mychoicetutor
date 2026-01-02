<!-- Question Selector Modal -->
<div class="modal fade" id="questionSelectorModal" tabindex="-1" role="dialog" aria-labelledby="questionSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="questionSelectorModalLabel">
                    <i class="fas fa-question-circle"></i> Select Questions for Your Quiz
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Search and Filter Bar -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="questionSearchInput" placeholder="Search questions by text, topic, or keyword...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="questionTypeFilter">
                            <option value="">All Types</option>
                            <option value="1">Objective</option>
                            <option value="2">Subjective</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success btn-block" id="btnQuickCreateQuestion">
                            <i class="fas fa-plus-circle"></i> Create New Question
                        </button>
                    </div>
                </div>

                <!-- Selected Questions Counter -->
                <div class="alert alert-info mb-3" id="selectedQuestionsCounter">
                    <i class="fas fa-check-circle"></i> <strong id="selectedCount">0</strong> question(s) selected
                    <button type="button" class="btn btn-sm btn-link float-right" id="clearSelection">Clear All</button>
                </div>

                <!-- Loading Indicator -->
                <div id="questionsLoading" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading questions...</p>
                </div>

                <!-- No Questions Message -->
                <div id="noQuestionsMessage" class="alert alert-warning text-center" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> No questions found. Please create questions first or adjust your filters.
                </div>

                <!-- Questions Grid -->
                <div class="questions-grid-container" id="questionsGrid">
                    <!-- Question cards will be loaded here via AJAX -->
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4" id="questionsPagination">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="addSelectedQuestionsBtn" disabled>
                    <i class="fas fa-check"></i> Add Selected Questions (<span id="footerSelectedCount">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

