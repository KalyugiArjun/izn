<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSubCategory extends Model
{
    use HasFactory;

    public function category() {
        return $this->hasOne(ExpenseCategory::class, 'id', 'category_id');
    }
}
