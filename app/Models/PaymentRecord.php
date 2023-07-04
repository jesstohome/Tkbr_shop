<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function buyer() {
        return $this->belongsTo(User::class);
    }

    public function seller() {
        return $this->belongsTo(User::class);
    }

    public function bloc() {
        return $this->belongsTo(Bloc::class);
    }
}
