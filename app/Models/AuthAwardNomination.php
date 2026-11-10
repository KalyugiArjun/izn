<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthAwardNomination extends Model
{
    use HasFactory;

    protected $table = 'auth_award_nominations';

    protected $fillable = [
        'department_id',
        'designation_id',
        'employee_id',
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

    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }
}
