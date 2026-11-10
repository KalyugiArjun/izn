<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Jpo;
use App\Models\CaseStudy;
use App\Models\SafetyCircular;
use App\Models\OperatingSafetyAlert;
use App\Models\OperatingSafetyEpisode;
use App\Models\OperatingSafetyDrive;
use App\Models\OperatingSafetyCircular;
use App\Models\OfficerInspectionNote;
use App\Models\TrafficInspectorInspectionNote;
use App\Models\StationWorkingRuleOriginal;
use App\Models\StationWorkingRuleCorrection;
use App\Models\GsrOriginal;
use App\Models\GsrCorrection;
use App\Models\OperatingManualOriginal;
use App\Models\OperatingManualCorrection;
use App\Models\AccidentManualOriginal;
use App\Models\AccidentManualCorrection;
use App\Models\MiscellaneousLetters;
use App\Models\APINotification;
use App\Models\MasterEmployee;

class NotificationController extends Controller
{
   

    public function index(Request $request)
    {
        $user = Auth::user();

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        $notificationLog = APINotification::firstOrCreate(
            [
                'employee_id' => $employee->id,
            ],
            [
                'last_notification_seen_at' => now(),
            ]
        );

        $from = $notificationLog->last_notification_seen_at
            ? Carbon::parse($notificationLog->last_notification_seen_at)
            : $employee->created_at;

        $to = now();

        $notifications = $this->buildNotificationCount($from, $to);

        // API hit time update
        $notificationLog->update([
            'last_notification_seen_at' => $to,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notifications fetched successfully',
            'data' => [
                'last_notification_seen_at' => $from->format('Y-m-d H:i:s'),
                'current_hit_at' => $to->format('Y-m-d H:i:s'),
                'notifications' => $notifications,
                'total' => collect($notifications)->sum(),
            ],
        ]);
    }

    private function buildNotificationCount($from, $to): array
    {
        $models = [
            'jpo' => Jpo::class,
            'case_study' => CaseStudy::class,
            'safety_circular' => SafetyCircular::class,
            'operating_safety_circular' => OperatingSafetyCircular::class,
            'operating_safety_alert' => OperatingSafetyAlert::class,
            'operating_safety_episode' => OperatingSafetyEpisode::class,
            'operating_safety_drive' => OperatingSafetyDrive::class,
            'officer_inspection_note' => OfficerInspectionNote::class,
            'traffic_inspection_note' => TrafficInspectorInspectionNote::class,
            'swr_original' => StationWorkingRuleOriginal::class,
            'swr_correction' => StationWorkingRuleCorrection::class,
            'gsr_original' => GsrOriginal::class,
            'gsr_correction' => GsrCorrection::class,
            'operating_manual_original' => OperatingManualOriginal::class,
            'operating_manual_correction' => OperatingManualCorrection::class,
            'accident_manual_original' => AccidentManualOriginal::class,
            'accident_manual_correction' => AccidentManualCorrection::class,
            'miscellaneous_letters' => MiscellaneousLetters::class,
        ];

        $result = [];

        foreach ($models as $key => $model) {
            $result[$key] = $model::where(
                'created_at',
                '>',
                $from
            )->where(
                'created_at',
                '<=',
                $to
            )->count();
        }

        return $result;
    }
}