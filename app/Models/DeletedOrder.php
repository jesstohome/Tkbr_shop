<?php

namespace App\Models;

class DeletedOrder extends Order
{
    // protected $table = 'deleted_orders as orders';

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class, 'order_id');
    }
}
