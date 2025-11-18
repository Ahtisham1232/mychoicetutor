<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\learningcontents;

class DeleteAllLearningContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'learningcontents:delete-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all learning contents from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Are you sure you want to delete ALL learning contents? This action cannot be undone!')) {
            $count = learningcontents::count();
            
            if ($count > 0) {
                learningcontents::truncate();
                $this->info("Successfully deleted {$count} learning content(s).");
            } else {
                $this->info('No learning contents found to delete.');
            }
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
