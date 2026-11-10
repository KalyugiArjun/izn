<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'created_by',
        'updated_by'
    ];

    public function employees()
    {
        return $this->belongsToMany(
            MasterEmployee::class,
            'employee_station',
            'station_id',
            'master_employee_id'
        );
    }
    public function loadStablings()
    {
        return $this->hasMany(LoadStabling::class, 'station', 'id');
    }

    public function workingRuleOriginals()
    {
        return $this->hasMany(StationWorkingRuleOriginal::class, 'station_id')->where('status', 1);
    }
}
