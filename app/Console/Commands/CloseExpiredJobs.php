<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseExpiredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:close-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close expired jobs and delete their application files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired jobs...');

        // Find active jobs with past deadline
        $expiredJobs = \App\Models\Job::where('status', 'active')
            ->where('deadline', '<', now()->startOfDay())
            ->get();

        if ($expiredJobs->isEmpty()) {
            $this->info('No expired jobs found.');
            return;
        }

        foreach ($expiredJobs as $job) {
            $this->info("Closing job: {$job->title} (ID: {$job->id})");
            
            // Close the job
            $job->update(['status' => 'closed']);

            // Process applications
            $applications = $job->applications;
            $deletedCount = 0;

            foreach ($applications as $app) {
                // Delete resume
                if ($app->resume_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($app->resume_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($app->resume_path);
                    $app->resume_path = null;
                }

                // Delete cover letter
                if ($app->cover_letter_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($app->cover_letter_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($app->cover_letter_path);
                    $app->cover_letter_path = null;
                }

                $app->save();
                $deletedCount++;
            }

            $this->info("  - Closed and cleaned up {$deletedCount} applications.");
        }

        $this->info('All done!');
    }
}
