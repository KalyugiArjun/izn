<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\APIComplaint;
use Carbon\Carbon;

class AutoFeedbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';
       protected $signature = 'auto:feedback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto submit feedback after 2 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get complaints where:
        // - rectified_at exists
        // - feedback not given
        // - 2 days passed

        $complaints = APIComplaint::whereNotNull('rectified_at')
            ->whereNull('feedback_given_at')
            ->where('rectified_at', '<=', Carbon::now()->subDays(2))
            ->get();

        foreach ($complaints as $complaint) {
            $complaint->update([
                'feedback_description' => 'Auto closed without user feedback',
                'feedback_rating' => 5,
                'feedback_images' => null,
                'feedback_given_by' => null,
                'feedback_given_at' => now(),
            ]);
        }

        return "Auto feedback submitted for expired complaints.";
    }
}
