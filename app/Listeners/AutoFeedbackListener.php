<?php

namespace App\Listeners;

use App\Events\AutoFeedbackEvent;
use App\Models\APIComplaint;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoFeedbackListener
{
    public function handle(AutoFeedbackEvent $event)
    {
        // ✅ Run only once every 12 hours using Cache lock
        // Prevents running on every single request
        if (!Cache::add('auto_feedback_lock', true, now()->addHours(12))) {
            return; // already ran within last 12 hours, skip
        }

        try {
            $complaints = APIComplaint::whereNotNull('rectified_at')
                ->whereNull('feedback_given_at')
                ->where('rectified_at', '<=', Carbon::now()->subDays(2))
                ->get();

            foreach ($complaints as $complaint) {
                $complaint->update([
                    'feedback_description' => 'Auto closed without user feedback',
                    'feedback_rating'      => 5,
                    'feedback_images'      => null,
                    'feedback_given_by'    => null,
                    'feedback_given_at'    => now(),
                ]);
            }

            Log::info('AutoFeedback ran at ' . now() . ' | Total: ' . $complaints->count());

        } catch (\Exception $e) {
            Log::error('AutoFeedback failed: ' . $e->getMessage());
            // Release lock so it can retry
            Cache::forget('auto_feedback_lock');
        }
    }
}
