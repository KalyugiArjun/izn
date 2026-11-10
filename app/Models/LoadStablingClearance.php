<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadStablingClearance extends Model
{
    protected $guarded = [];
    protected $casts = [
    'clearance_check_data' => 'array',];
}