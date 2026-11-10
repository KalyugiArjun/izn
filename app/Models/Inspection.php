<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = [
        'date_of_inspection',
        'designation',
        'movement',
        'type_of_inspection',
        'day_night',
        'type_of_ambush_check',
        'station_yard',
        'level_crossing_no',
        'train_number',
        'section',
        'line_number_load_stable',
        'type_of_load_load_stable',
        't806_number',
        'shunting_observation_of',
        'major_deficiencies',
        'action_taken',
        'remark',
        'created_by',
        'employee_id',
    ];

    /**
     * Relationship with MasterEmployee
     */
    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }

    /**
     * Scope to filter inspections by employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}