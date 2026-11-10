<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferredComplaint extends Model
{
    protected $fillable = [
        'complaint_id',
        'from_department_id',
        'to_department_id',
        'from_assigned_to',
        'to_assigned_to',
        'transferred_by',
        'transfer_date',
    ];

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function transferredByUser()
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
