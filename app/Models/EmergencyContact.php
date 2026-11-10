<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'mobile_number',
        'email',
        'address',
        'relation',
        'other_relation',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }
}
