<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RectificationHistory extends Model
{
    protected $table = 'rectification_histories';

    protected $fillable = [
        'complaint_id',
        'action',
        'old_status',
        'new_status',

        // ✅ Transfer fields
        'old_department_id',
        'new_department_id',
        'old_assigned_to',
        'new_assigned_to',

        'rectifier_name',
        'rectifier_designation',
        'rectification_date',
        'rectification_remark',
        'rectification_images',

        'reopen_reason',
        'remarks',
        'action_by',
    ];

    protected $casts = [
        'rectification_images' => 'array',
    ];
}
