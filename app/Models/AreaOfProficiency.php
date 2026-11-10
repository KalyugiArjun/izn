<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaOfProficiency extends Model
{
    protected $table = 'area_of_proficiencies';

    protected $fillable = [
        'name',
        'status',
    ];
}
