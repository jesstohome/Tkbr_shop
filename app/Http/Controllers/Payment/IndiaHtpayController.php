<?php


namespace App\Http\Controllers\Payment;

/**
 * 印度 HTpay
 * Class InHtpayController
 * @package App\Http\Controllers\Payment
 */
class IndiaHtpayController extends HtpayController
{
    protected $payment_type = 'india_htpay';
    // 1:印度
    protected $payment_currency = 1;

    protected function getMchId() {
        return [env('HTPAY_MEMBERID_IN'), env('HTPAY_SECRET_IN')];
    }

    protected function getPayBankCode()
    {
        return env('HTPAY_BANK_CODE_IN');
    }
}
