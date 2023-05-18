<!-- payments Modal -->
<div id="payments-modal" class="modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-md-12">
                        <a href="javascript:void(0);" id="wallet-link" onclick="$('#payment_for_storehouse_modal').modal('show')" class="btn btn-primary mt-2">Wallet</a>

                        @if(count(\App\Models\ManualPaymentMethod::listByBloc(0, $order->shop->bloc_id)))
                        <a href="javascript:void(0);" id="Manual-link" onclick="show_make_wallet_recharge_modal(3)" class="btn btn-primary mt-2">{{translate('Manual transfer')}}</a>
                        @endif

                        @if(env('PAYPAL_CLIENT_ID'))
                        <a href="{{ route('seller.orders.payment_for_storehouse_product_online', ['payment_code' => 'paypal', 'order_id' => $order->id ?? 0]) }}" id="paypal-link" class="btn btn-primary mt-2">Paypal</a>
                        @endif

                        @if(get_setting('htpay_collection_behalf') == 1 && is_open_this_payment('htpay', $order))
                            <a href="javascript:void(0)" onclick="toPay(1)" id="htpay-link" class="btn btn-primary mt-2">Htpay</a>
                        @endif

                        @if(get_setting('in_htpay_collection_behalf') == 1 && is_open_this_payment('india_htpay', $order))
                            <a href="javascript:void(0)" onclick="toPay(3)" id="india_htpay-link" class="btn btn-primary mt-2">{{translate('India')}} Htpay</a>
                        @endif

                        @if(get_setting('qepay_collection_behalf') == 1 && is_open_this_payment('qepay', $order))
                            <a href="javascript:void(0)" onclick="toPay(2)" id="qepay-link" class="btn btn-primary mt-2">Qepay</a>
                        @endif
                    </div>
                </div>
                <div class="row text-left mt-5">
                    <div class="col-md-12">
                        @php
                        $jump2 = "<a href='" . route('seller.support_ticket.index') . "'>" . translate('Submit Work Order') . "</a>";
                        @endphp
                        <p>
                            {{translate('Explain')}}:<br />
                        </p>

                        <p>1. {{translate('The manufacturer has passed platform certification and paid a $50000 deposit')}}.</p>

                        <p>2. @php echo sprintf(translate('If you need help, please [%s] and provide us with feedback on your issue'), $jump2) @endphp.</p>

                        <p>3. @php echo sprintf(translate("If your product wants to enter the product warehouse, please [%s] and contact the platform"), $jump2) @endphp.</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div><!-- /.modal -->

<div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="offline_wallet_recharge_modal_body"></div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function show_make_wallet_recharge_modal(type){
        $.post('{{ route('offline_wallet_recharge_modal') }}', {type:type, order_id: @if ($order) {{$order->id}} @else 0 @endif, _token:'{{ csrf_token() }}'}, function(data){
            $('#offline_wallet_recharge_modal_body').html(data);
            $('#offline_wallet_recharge_modal').modal('show');
        });
    }

    function toPay(pay_type) {
        var url = '';
        if (1 === pay_type) {
            url = "{{ route('seller.orders.payment_for_storehouse_product_online', ['payment_code' => 'htpay', 'order_id' => $order->id ?? 0]) }}"
        } else if (2 === pay_type) {
            url = "{{ route('seller.orders.payment_for_storehouse_product_online', ['payment_code' => 'qepay', 'order_id' => $order->id ?? 0]) }}"
        } else if (3 === pay_type) {
            url = "{{ route('seller.orders.payment_for_storehouse_product_online', ['payment_code' => 'india_htpay', 'order_id' => $order->id ?? 0]) }}"
        }
        window.open(url.replace('&amp;', '&'), '_target');
        setTimeout(function () {
            location.href = "{{ route('seller.money_withdraw_requests.index') }}";
        }, 2e3);

        return false;
    }

</script>
