<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status','created_by',
        'updated_by'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
