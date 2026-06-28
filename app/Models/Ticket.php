<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function ticketreplies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('id', 'ASC');
    }

    public function bloc() {
        return $this->belongsTo(Bloc::class);
    }

    public function staff() {
        return $this->belongsTo(Staff::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
