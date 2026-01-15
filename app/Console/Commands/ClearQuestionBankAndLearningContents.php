<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\questionbank;
use App\Models\learningcontents;

class ClearQuestionBankAndLearningContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:clear-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all questions from question bank and all learning contents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to clear all question bank and learning contents...');

        // Count before deletion
        $questionCount = questionbank::count();
        $learningContentCount = learningcontents::count();

        $this->info("Found {$questionCount} questions and {$learningContentCount} learning contents");

        // Confirm before deletion
        if (!$this->option('force') && !$this->confirm('Are you sure you want to delete all questions and learning contents? This action cannot be undone.')) {
            $this->info('Operation cancelled.');
            return;
        }

        // Clear all questions
        questionbank::truncate();
        $this->info('✓ All questions cleared from question bank');

        // Clear all learning contents
        learningcontents::truncate();
        $this->info('✓ All learning contents cleared');

        $this->info('Operation completed successfully!');
    }
}
