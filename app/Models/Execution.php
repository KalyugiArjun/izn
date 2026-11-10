<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Execution extends Model
{
    use HasFactory;

    protected $table = 'executions';

    protected $fillable = [
        'identify_new_work_id',
        'milestone',
        'date',
        'days_taken',
        'work_completion_date',
        'supporting_document',
    ];

    protected $casts = [
        'date' => 'date',
        'work_completion_date' => 'date',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(IdentifyNewWork::class, 'identify_new_work_id');
    }
}
