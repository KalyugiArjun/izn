<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingSafetyAlert extends Model
{
    protected $table = 'operating_safety_alerts';

    protected $fillable = [
        'date',
        'alert_no',
        'subject',
        'pdf_file',
        'status',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'date' => 'date',
    ];
}
