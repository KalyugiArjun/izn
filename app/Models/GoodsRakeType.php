<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsRakeType extends Model
{
    protected $table = 'goods_rake_types';

    protected $fillable = [
        'name',
        'status',
    ];
}
