<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'designation',
        'image',
        'message',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    /* ── Image URL Accessor ── */

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    /* ── Short message strip tags accessor ── */

    public function getShortMessageAttribute(): string
    {
        return Str::limit(strip_tags($this->message), 100);
    }
}
