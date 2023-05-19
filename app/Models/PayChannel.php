<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayChannel extends Model
{
    use HasFactory;

    public function fields() {
        return $this->hasMany(PayChannelField::class);
    }

    public function bank_fields() {
        return $this->hasMany(PayChannelField::class)->where('field_type', 'bank');
    }

    public function wallet_fields() {
        return $this->hasMany(PayChannelField::class)->where('field_type', 'wallet');
    }
}
