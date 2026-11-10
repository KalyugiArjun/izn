<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WagonNumber extends Model
{
    protected $table = 'wagon_numbers';

    protected $fillable = [
        'load_stabling_id',
        'wagon_no'
    ];
}