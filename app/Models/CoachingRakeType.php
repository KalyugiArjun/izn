<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingRakeType extends Model
{
    protected $table = 'coaching_rake_types';

    protected $fillable = [
        'name',
        'status',
    ];
}
