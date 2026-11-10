<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentifyNewWork extends Model
{
    use HasFactory;

    protected $table = 'identify_new_works';

    protected $fillable = [
        'work_description_identification',
        'date_of_identification_of_work',
        'concern_traffic_inspector',
        'ph16_ti_id',
        'ph16_ti_ids',
        'status',
    ];

    protected $casts = [
        'date_of_identification_of_work' => 'date',
        'ph16_ti_ids' => 'array',
        'status' => 'integer',
    ];

    public function getInspectorIdsAttribute(): array
    {
        if (!empty($this->ph16_ti_ids) && is_array($this->ph16_ti_ids)) {
            return array_values(array_map('intval', $this->ph16_ti_ids));
        }
        if (!empty($this->ph16_ti_id)) {
            return [(int) $this->ph16_ti_id];
        }
        return [];
    }

    public function getInspectorNamesAttribute(): array
    {
        if (!empty($this->concern_traffic_inspector)) {
            return array_filter(array_map('trim', explode(',', $this->concern_traffic_inspector)));
        }
        return [];
    }

    public function ph16Ti()
    {
        return $this->belongsTo(Ph16Ti::class, 'ph16_ti_id');
    }

    public function jointSurveys()
    {
        return $this->hasMany(JointSurvey::class, 'identify_new_work_id');
    }

    public function estimates()
    {
        return $this->hasMany(Estimate::class, 'identify_new_work_id');
    }

    public function reviewEstimates()
    {
        return $this->hasMany(ReviewEstimate::class, 'identify_new_work_id');
    }

    public function approvalListings()
    {
        return $this->hasMany(ApprovalListing::class, 'identify_new_work_id');
    }

    public function financeVettingSanctions()
    {
        return $this->hasMany(FinanceVettingSanction::class, 'identify_new_work_id');
    }

    public function tenderings()
    {
        return $this->hasMany(Tendering::class, 'identify_new_work_id');
    }

    public function esps()
    {
        return $this->hasMany(Esp::class, 'identify_new_work_id');
    }

    public function sips()
    {
        return $this->hasMany(Sip::class, 'identify_new_work_id');
    }

    public function executions()
    {
        return $this->hasMany(Execution::class, 'identify_new_work_id');
    }

    public function executionends()
    {
        return $this->hasMany(Executionend::class, 'identify_new_work_id');
    }

    public function variations()
    {
        return $this->hasMany(Variation::class, 'identify_new_work_id');
    }

    public function inspectionLogs()
    {
        return $this->hasMany(InspectionLog::class, 'identify_new_work_id');
    }

    /**
     * Compute current stage of the work based on paperwork milestones
     */
    public function getCurrentStageAttribute(): string
    {
        if ($this->executionends && $this->executionends->count() > 0) {
            return 'Execution Completed';
        }
        if ($this->executions && $this->executions->count() > 0) {
            return 'Execution In Progress';
        }
        if (($this->sips && $this->sips->count() > 0) || ($this->esps && $this->esps->count() > 0)) {
            return 'ESP / SIP';
        }
        if ($this->tenderings && $this->tenderings->count() > 0) {
            return 'Tendering';
        }
        if ($this->financeVettingSanctions && $this->financeVettingSanctions->count() > 0) {
            return 'Finance Vetting';
        }
        if ($this->approvalListings && $this->approvalListings->count() > 0) {
            return 'Approval & Listing';
        }
        if ($this->reviewEstimates && $this->reviewEstimates->count() > 0) {
            return 'Review Estimate';
        }
        if ($this->estimates && $this->estimates->count() > 0) {
            return 'Estimates';
        }
        if ($this->jointSurveys && $this->jointSurveys->count() > 0) {
            return 'Joint Survey';
        }
        return 'Work Identified';
    }

    /**
     * Approximate progress percentage (0 - 100%)
     */
    public function getProgressPercentAttribute(): int
    {
        $stages = [
            $this->date_of_identification_of_work ? 1 : 0,
            ($this->jointSurveys && $this->jointSurveys->count() > 0) ? 1 : 0,
            ($this->estimates && $this->estimates->count() > 0) ? 1 : 0,
            ($this->reviewEstimates && $this->reviewEstimates->count() > 0) ? 1 : 0,
            ($this->approvalListings && $this->approvalListings->count() > 0) ? 1 : 0,
            ($this->financeVettingSanctions && $this->financeVettingSanctions->count() > 0) ? 1 : 0,
            ($this->tenderings && $this->tenderings->count() > 0) ? 1 : 0,
            (($this->esps && $this->esps->count() > 0) || ($this->sips && $this->sips->count() > 0)) ? 1 : 0,
            ($this->executions && $this->executions->count() > 0) ? 1 : 0,
            ($this->executionends && $this->executionends->count() > 0) ? 1 : 0,
        ];

        $completed = array_sum($stages);
        return (int) round(($completed / count($stages)) * 100);
    }
}
