<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccidentManualOriginal extends Model
{
    protected $table = 'accident_manual_originals';

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
            AccidentManualCorrection::class,
            'original_id'
        );
    }
}