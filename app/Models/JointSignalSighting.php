<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JointSignalSighting extends Model
{
    use HasFactory;

    protected $table = 'joint_signal_sightings';

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
            'joint_signal_sighting_id',
            'master_employee_id'
        );
    }

}
