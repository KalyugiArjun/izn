<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Popup extends Model
{
    protected $table = 'popups';

    protected $fillable = [
        'title',
        'subtitle',
        'message',
        'note',
        'image',
        'button_text',
        'button_url',
        'starts_at',
        'ends_at',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => 'boolean',
    ];

 public function getImageUrlAttribute(): ?string
{
    if (!$this->image) {
        return null;
    }

    $path = ltrim($this->image, '/');

    return asset('storage/' . $path);
}
}
