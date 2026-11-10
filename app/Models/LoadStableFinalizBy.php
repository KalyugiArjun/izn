<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadStableFinalizBy extends Model
{
    use HasFactory;

    protected $table = 'load_stable_finaliz_bies';

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

