<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $fillable=[
        'invoice_id',
        'cloth_type_id',
        'quantity',
        'amount',
        'tax',
        'note',
        'parent_id',
    ];

    public function clothTypes()
    {
        return $this->hasOne('App\Models\ClothType', 'id', 'cloth_type_id');
    }
}
