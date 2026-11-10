<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceVettingSanction extends Model
{
    use HasFactory;

    protected $table = 'finance_vetting_sanctions';

    protected $fillable = [
        'identify_new_work_id',
        'milestone',
        'is_completed',
        'remark',
        'date',
        'days_taken',
        'supporting_document',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'date' => 'date',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(IdentifyNewWork::class, 'identify_new_work_id');
    }
}
