<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LoadStablingSmChecklist — Station Master Stage 2 (checklist + forward)
 */
class LoadStablingSmChecklist extends Model
{
    protected $fillable = [
        'load_stabling_id',
        'interlocking_type',
        'checklist_data',
        'sm_private_number',
        'tnl_private_number',
        'load_stable_datetime',
        'reversal_taken_datetime',
        'total_load_cleared',
        'loco_reversal_received',
        'engine_reversal_handed_over',
        'release_type',
    ];

    protected $casts = [
        'checklist_data' => 'array',
    ];

    public function loadStabling(): BelongsTo
    {
        return $this->belongsTo(LoadStabling::class, 'load_stabling_id');
    }
}


/**
 * LoadStablingClearance — Points-man Stage 3 (release brakes / safety)
 */
class LoadStablingClearance extends Model
{
    protected $fillable = [
        'load_stabling_id',
        'load_type',
        'load_sub_type',
        'release_type',
        'total_loads_cleared',
        'release_time',
        'clearance_check_data',
        'photo_wedges_removed',
        'photo_chains_removed',
    ];

    protected $casts = [
        'clearance_check_data' => 'array',
    ];

    public function loadStabling(): BelongsTo
    {
        return $this->belongsTo(LoadStabling::class, 'load_stabling_id');
    }
}


/**
 * LoadStablingFinalSubmission — Station Master Stage 4 (dispatch confirmation)
 */
class LoadStablingFinalSubmission extends Model
{
    protected $fillable = [
        'load_stabling_id',
        'release_time',
        'released_completely',
        'sm_private_number',
        'tnl_private_number',
        'train_ready_for_dispatch',
    ];

    public function loadStabling(): BelongsTo
    {
        return $this->belongsTo(LoadStabling::class, 'load_stabling_id');
    }
}
