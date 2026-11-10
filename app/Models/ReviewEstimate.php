<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewEstimate extends Model
{
    use HasFactory;

    protected $table = 'review_estimates';

    protected $fillable = [
        'identify_new_work_id',
        'milestone',
        'is_completed',
        'remark',
        'review_estimate_date',
        'days_taken',
        'previous_department',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'review_estimate_date' => 'date',
        'days_taken' => 'integer',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(
            IdentifyNewWork::class,
            'identify_new_work_id'
        );
    }
}
