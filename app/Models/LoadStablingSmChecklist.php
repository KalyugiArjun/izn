<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class LoadStablingSmChecklist extends Model
{
    protected $guarded = [];

    protected $casts = [
        'checklist_data' => 'array',
    ];
}