<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyCircular extends Model
{
    protected $table = 'safety_circulars';

    protected $fillable = [
        'circular_number',
        'date_of_circular',
        'subject',
        'pdf_file',
        'status',
        'created_by',
        'updated_by',
    ];
}
