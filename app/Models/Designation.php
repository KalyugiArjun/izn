<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'division_id',
        'department_id',
        'name',
        'position', // ✅ ADD THIS
        'mobile',
        'email',
        'status',
        'created_by',
        'updated_by'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
