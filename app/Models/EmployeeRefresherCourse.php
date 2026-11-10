<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRefresherCourse extends Model
{
    protected $table = 'employee_refresher_courses';

    protected $fillable = [
        'master_employee_id',
        'staff_id',
        'last_refresher_date',
        'certificate_no',
        'institution_name',
        'next_due_date',
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
