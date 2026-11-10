<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    public static function addPayment($data)
    {
        $InvoicePayment = new InvoicePayment();
        $InvoicePayment->invoice_id = $data['invoice_id'] ?? 0;
        $InvoicePayment->transaction_id = $data['transaction_id'] ?? '';
        $InvoicePayment->invoice_transactions_id = $data['transaction_id'] ?? '';
        $InvoicePayment->payment_type = $data['payment_type'] ?? '';
        $InvoicePayment->amount = $data['amount'] ?? 0;
        $InvoicePayment->notes = $data['notes'] ?? '';
        $InvoicePayment->parent_id = parentId();
        $InvoicePayment->payment_date = now();
        $InvoicePayment->payment_status = $data['payment_status'] ?? '';
        $InvoicePayment->receipt = $data['receipt'] ?? '';
        $InvoicePayment->save();
    }
    protected $fillable=[
        'invoice_id',
        'transaction_id',
        'payment_type',
        'payment_date',
        'notes',
        'parent_id',
    ];


    public function Invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }
}
