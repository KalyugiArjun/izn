<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePME extends Model
{
    protected $table = 'employee_pmes';

    protected $fillable = [
        'master_employee_id',
        'staff_id',
        'last_pme_date',
        'certificate_no',
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
