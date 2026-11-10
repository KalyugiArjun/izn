<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeGradation extends Model
{
    protected $table = 'employee_gradations';

    protected $fillable = [
        'master_employee_id',
        'staff_id',
        'gradation_date',
        'total_marks',
        'grade',
        'status',
        'created_by',
        'updated_by'
    ];

    public function staff()
    {
        return $this->belongsTo(MasterEmployee::class, 'staff_id');
    }

    public function masterEmployee()
    {
        return $this->belongsTo(MasterEmployee::class, 'master_employee_id');
    }

    public function getResolvedEmployeeAttribute()
    {
        return $this->staff ?: $this->masterEmployee;
    }
}
