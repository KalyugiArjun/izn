<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLoadStableClearance extends Model
{
    use HasFactory;

    protected $table = 'auth_load_stable_clearances';

    protected $fillable = [
        'department_id',
        'designation_id',
        'created_by',
        'updated_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
