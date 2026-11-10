<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionLog extends Model
{
    use HasFactory;

    protected $table = 'inspection_logs';

    protected $fillable = [
        'identify_new_work_id',
        'date_of_inspection',
        'department_inspected',
        'inspecting_authority',
        'name_of_inspecting_official',
        'work_done_detail',
    ];

    protected $casts = [
        'date_of_inspection' => 'date',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(IdentifyNewWork::class, 'identify_new_work_id');
    }

    public function photos()
    {
        return $this->hasMany(InspectionLogPhoto::class, 'inspection_log_id');
    }
}
