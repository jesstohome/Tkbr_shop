<div class="col-lg-2 ml-auto">
    <select class="form-control aiz-selectpicker" name="payment_code" id="payment_code">
        <option value="">{{translate('Filter by Payment method')}}</option>
        <option value="artificial" @if ($payment_code == 'artificial') selected @endif>人工付款</option>
        <option value="htpay" @if ($payment_code == 'htpay') selected @endif>Htpay</option>
        <option value="india_htpay" @if ($payment_code == 'india_htpay') selected @endif>印度Htpay</option>
        <option value="qepay" @if ($payment_code == 'qepay') selected @endif>Qepay</option>
        <option value="work_order" @if ($payment_code == 'work_order') selected @endif>{{translate('Work Order')}}</option>
        <option value="wallet" @if ($payment_code == 'wallet') selected @endif>{{translate('Wallet')}}</option>
    </select>
</div>
