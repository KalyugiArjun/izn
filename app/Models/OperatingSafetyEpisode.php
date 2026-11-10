<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingSafetyEpisode extends Model
{
    protected $table = 'operating_safety_episodes';

    protected $fillable = [
        'date',
        'episode_no',
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
