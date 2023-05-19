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
     * @param int $seller_id
     * @param int $bloc_id
     * @param array $exclude_type
     * @return ManualPaymentMethod[]|array|\Illuminate\Database\Eloquent\Collection
     */
    public function listByBloc($seller_id = 0, $bloc_id = 0, $exclude_type = ['work_order_payment']) {
        if (is_string($exclude_type)) $exclude_type = [$exclude_type];

        if (!$bloc_id) {
            if (!$seller_id) {
                $user = \Auth::user();
            } else {
                $user = User::find($seller_id);
            }
            $bloc_id = (int) $user->bloc_id;
            if (empty($bloc_id)) {
                return [];
            }
        }

        $allowPayments = [];
        $payments = ManualPaymentMethod::query()->whereNotIn('type', $exclude_type)->where('status', 1)->get();
        foreach ($payments as $payment) {
            if (in_array($bloc_id, explode(",", $payment->bloc_ids))) {
                $allowPayments[] = $payment;
            }
        }

        return $allowPayments;
    }
}
