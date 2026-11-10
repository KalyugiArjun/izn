<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jpo extends Model
{
    protected $table = 'jpos';

    protected $fillable = [
        'jpo_number',
        'date_of_jpo',
        'subject',
        'pdf_file',
        'status',
        'created_by',
        'updated_by',
    ];
}
