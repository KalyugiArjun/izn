<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['division_id', 'name', 'status', 'created_by', 'updated_by'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }
}
