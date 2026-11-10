<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationWorkingRuleOriginal extends Model
{
    protected $table = 'station_working_rule_originals';

    protected $fillable = [
        'station_id',
        'original_name',
        'original_pdf',
        'status',
        'created_by',
        'updated_by',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function corrections()
    {
        return $this->hasMany(
            StationWorkingRuleCorrection::class,
            'original_id'
        );
    }
}