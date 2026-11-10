<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthStaffCounselling extends Model
{
    use HasFactory;

    protected $table = 'auth_staff_counsellings';

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

