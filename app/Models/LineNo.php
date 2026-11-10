<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineNo extends Model
{
    use HasFactory;

    protected $table = 'line_nos';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];
}
