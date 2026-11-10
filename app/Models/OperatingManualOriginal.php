<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingManualOriginal extends Model
{
    protected $table = 'operating_manual_originals';

    protected $fillable = [
        'original_name',
        'original_pdf',
        'status',
        'created_by',
        'updated_by',
    ];

    public function corrections()
    {
        return $this->hasMany(
            OperatingManualCorrection::class,
            'original_id'
        );
    }
}