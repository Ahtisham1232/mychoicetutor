<!-- Quiz Preview Modal -->
<div class="modal fade" id="quizPreviewModal" tabindex="-1" role="dialog" aria-labelledby="quizPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="quizPreviewModalLabel">
                    <i class="fas fa-eye"></i> Quiz Preview
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="quizPreviewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="btnEditQuestionsFromPreview">
                    <i class="fas fa-edit"></i> Edit Questions
                </button>
            </div>
        </div>
    </div>
</div>

