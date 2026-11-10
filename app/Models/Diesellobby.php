<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diesellobby extends Model
{
    use HasFactory;

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
            'diesellobby_id',
            'master_employee_id'
        );
    }

}
