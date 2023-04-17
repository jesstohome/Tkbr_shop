<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualPaymentMethod extends Model
{
    protected $guarded = [];

    /**
     * 显示只符号当前集团的支付方式
     * author: Sym
     * time: 2023-04-17 19:38
     */
    public function listByBloc() {
        $bloc_id = 0;
        if (\Auth::user()->user_type != 'seller') {
            return ManualPaymentMethod::all();
        }

        $shop = \Auth::user()->shop;
        $admin_id = ShopManage::query()->where("shop_id", $shop->id)->value("admin_id");
        if ($admin_id) {
            $bloc_id = Staff::query()->where("user_id", $admin_id)->value("bloc_id");
        }

        if (empty($bloc_id)) {
            return [];
        }

        $allowPayments = [];
        $payments = ManualPaymentMethod::all();
        foreach ($payments as $payment) {
            if (in_array($bloc_id, explode(",", $payment->bloc_ids))) {
                $allowPayments[] = $payment;
            }
        }

        return $allowPayments;
    }
}
