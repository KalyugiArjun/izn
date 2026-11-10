<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunningRoomLocation extends Model
{
    use HasFactory;

    protected $table = 'running_room_locations';

    protected $fillable = [
        'code',
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
            'running_room_location_id',
            'master_employee_id'
        );
    }

}
