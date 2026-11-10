<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyDrive extends Model
{
    use HasFactory;

    protected $table = 'safety_drives';

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
            'safety_drive_id',
            'master_employee_id'
        );
    }

}
