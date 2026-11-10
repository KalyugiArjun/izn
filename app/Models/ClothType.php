<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClothType extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'gender',
        'amount',
        'taxes',
        'note',
        'parent_id',
    ];

    public static $gender=[
        'Male'=>'Male',
        'Female'=>'Female',
    ];

    public function clothMeasureType()
    {
        return $this->hasMany('App\Models\ClothMeasureType','cloth_type_id', 'id')->orderBy('order');
    }

    public function taxs()
    {
        if(!empty($this->taxes)){
            return Tax::whereIn('id',explode(',',$this->taxes))->get();
        }
    }
}
