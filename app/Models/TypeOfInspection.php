<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfInspection extends Model
{
    use HasFactory;

    protected $table = 'type_of_inspections';

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
            'type_of_inspection_id',
            'master_employee_id'
        );
    }

}
