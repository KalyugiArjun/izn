<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $table = 'variations';

    protected $fillable = [
        'identify_new_work_id',
        'date_of_variation',
        'nature_of_variation',
        'revised_tdc',
        'file',
    ];

    protected $casts = [
        'date_of_variation' => 'date',
        'revised_tdc' => 'date',
    ];

    public function identifyNewWork()
    {
        return $this->belongsTo(IdentifyNewWork::class, 'identify_new_work_id');
    }
}
