<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tendering extends Model
{
    use HasFactory;

    protected $table = 'tenderings';

    protected $fillable = [
        'identify_new_work_id',
        'department',
        'milestone',
        'is_completed',
        'remark',
        'date',
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
