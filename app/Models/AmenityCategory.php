<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmenityCategory extends Model
{
    use HasFactory;

    protected $table = 'amenity_categories';

    protected $fillable = [
        'name',
        'department_id', // ✅ added
        'status',
        'created_by',
        'updated_by'
    ];

    // relation
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
