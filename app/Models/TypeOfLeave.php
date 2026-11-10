<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfLeave extends Model
{
    use HasFactory;

    protected $table = 'type_of_leaves';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function employees()
    {
        return $this->belongsToMany(
            MasterEmployee::class,
            'employee_station',
            'type_of_leave_id',
            'master_employee_id'
        );
    }

}
