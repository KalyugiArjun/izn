<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EOEmployee extends Model
{
    protected $table = 'eo_employees';

    protected $fillable = [
        'diesel_lobby_id',
        'hrms_id',
        'name',
        'designation_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function dieselLobby()
    {
        return $this->belongsTo(Diesellobby::class, 'diesel_lobby_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
}
