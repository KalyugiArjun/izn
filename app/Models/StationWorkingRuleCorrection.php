<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationWorkingRuleCorrection extends Model
{
    protected $table = 'station_working_rule_corrections';

    protected $fillable = [
        'original_id',
        'correction_slip_no',
        'issue_date',
        'imposition_date',
        'correction_slip_pdf',
        'status',
        'created_by',
        'updated_by',
    ];

    public function original()
    {
        return $this->belongsTo(
            StationWorkingRuleOriginal::class,
            'original_id'
        );
    }
}
