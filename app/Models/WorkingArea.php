<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingArea extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'working_areas';

    protected $fillable = [
        'division_id',
        'department_id',
        'designation_id',
        'mobile',
        'email',
        'head_station_name',
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

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function stations()
    {
        return $this->belongsToMany(Station::class, 'working_area_stations', 'working_area_id', 'station_id');
    }
}
