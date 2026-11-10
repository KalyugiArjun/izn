<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    public $fillable=[
        'title',
        'amount',
        'receipt',
        'notes',
        'parent_id',
        'expense_id',
        'category_id',
        'sub_category_id',
    ];

    public function category() {
        return $this->hasOne(ExpenseCategory::class, 'id', 'category_id');
    }
    public function subCategory() {
        return $this->hasOne(ExpenseSubCategory::class, 'id', 'sub_category_id');
    }
}
