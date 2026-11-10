<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackgroundLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'background_image',
        'login_image',
        'status',
        'created_by',
        'updated_by',
    ];
}
