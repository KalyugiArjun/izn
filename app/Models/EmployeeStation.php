<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeStation extends Model
{
    protected $table = 'employee_station';

    protected $fillable = [
        'master_employee_id',
        'station_id'
    ];

    public $timestamps = false;
}