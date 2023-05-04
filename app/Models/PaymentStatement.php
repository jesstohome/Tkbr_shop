<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatement extends Model
{
    //
    public function seller() {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
