<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class APINotification extends Model
{
    use HasFactory;

    protected $table = 'api_notifications';

    protected $fillable = [
      'employee_id',
      'last_notification_seen_at'
       
    ];


    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }
}
