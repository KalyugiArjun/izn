<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingManualCorrection extends Model
{
    protected $table = 'operating_manual_corrections';

    protected $fillable = [
        'original_id',
        'correction_slip_no',
        'correction_slip_date',
        'correction_slip_pdf',
        'status',
        'created_by',
        'updated_by',
    ];

    public function original()
    {
        return $this->belongsTo(
            OperatingManualOriginal::class,
            'original_id'
        );
    }
}