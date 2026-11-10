<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscellaneousLetters extends Model
{
    protected $table = 'miscellaneous_letters';

    protected $fillable = [
        'date',
        'letter_no',
        'subject',
        'department_id',
        'pdf_file',
        'status',
        'created_by',
        'updated_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
