<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendBlock extends Model
{
    use HasFactory;

    protected $table = 'attend_blocks';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function employees()
    {
        return $this->belongsToMany(
            MasterEmployee::class,
            'employee_station',
            'attend_block_id',
            'master_employee_id'
        );
    }

}
