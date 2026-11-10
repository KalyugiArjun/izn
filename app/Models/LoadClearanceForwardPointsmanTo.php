<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadClearanceForwardPointsmanTo extends Model
{
    use HasFactory;

    protected $table = 'load_clearance_forward_pointsman_tos';

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

