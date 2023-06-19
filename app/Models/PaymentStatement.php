<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatement extends Model
{
    //
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'target_id');
    }

    public function bloc() {
        return $this->belongsTo(Bloc::class);
    }

    public function staff() {
        return $this->belongsTo(Staff::class);
    }
}
