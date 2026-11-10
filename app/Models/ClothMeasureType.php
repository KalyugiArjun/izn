<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothMeasureType extends Model
{
    use HasFactory;
    protected $fillable=[
        'cloth_type_id',
        'title',
        'unit',
        'order',
    ];

    public function units(){
        return $this->hasOne('App\Models\MeasurementUnit', 'id', 'unit');
    }
}
