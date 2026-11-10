<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    protected $table = 'estimates';

    protected $fillable = [
        'identify_new_work_id',
        'department',
        'is_completed',
        'remark',
        'cost',
        'estimate_called',
        'call_letter',
        'estimate_received',
        'received_estimate',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'cost' => 'decimal:2',
        'estimate_called' => 'date',
        'estimate_received' => 'date',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(
            IdentifyNewWork::class,
            'identify_new_work_id'
        );
    }
}
