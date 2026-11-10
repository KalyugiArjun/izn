<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'button_text',
        'button_url',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    /* ── Image URL Accessor ── */

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}
