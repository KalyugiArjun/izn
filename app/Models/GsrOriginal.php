<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GsrOriginal extends Model
{
    protected $table = 'gsr_originals';

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
            GsrCorrection::class,
            'original_id'
        );
    }
}