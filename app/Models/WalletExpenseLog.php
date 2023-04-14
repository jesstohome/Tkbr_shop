<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 钱包支出明细
 * 只记录 提现，提货付款
 * Class WalletExpenseLog
 * @package App\Models
 */
class WalletExpenseLog extends Model
{
    protected $table = 'wallets_expense_logs';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
