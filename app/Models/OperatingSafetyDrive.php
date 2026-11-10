<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingSafetyDrive extends Model
{
    protected $table = 'operating_safety_drives';

    protected $fillable = [
        'date',
        'drive_no',
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
