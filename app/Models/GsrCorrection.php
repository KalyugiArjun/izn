<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GsrCorrection extends Model
{
    protected $table = 'gsr_corrections';

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
            GsrOriginal::class,
            'original_id'
        );
    }
}