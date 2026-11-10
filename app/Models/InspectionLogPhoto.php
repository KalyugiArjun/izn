<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionLogPhoto extends Model
{
    use HasFactory;

    protected $table = 'inspection_log_photos';

    protected $fillable = [
        'inspection_log_id',
        'photo',
    ];

    public function inspectionLog()
    {
        return $this->belongsTo(InspectionLog::class, 'inspection_log_id');
    }
}
