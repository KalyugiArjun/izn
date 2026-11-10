<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Executionend extends Model
{
    protected $table = 'executionends';

    protected $fillable = [
        'identify_new_work_id',
        'milestone',
        'date',
        'execution_start_date',
        'execution_completion_date',
        'days_taken',
        'supporting_document',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function identifyNewWork(): BelongsTo
    {
        return $this->belongsTo(IdentifyNewWork::class, 'identify_new_work_id');
    }
}
