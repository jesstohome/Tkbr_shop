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
                        <a href="javascript:void(0);" id="Manual-link" onclick="show_make_wallet_recharge_modal(3)" class="btn btn-primary mt-2">人工转账</a>
                        @if(env('PAYPAL_CLIENT_ID'))
                        <a href="{{ route('seller.orders.payment_for_storehouse_product_online', ['payment_code' => 'paypal', 'order_id' => $order->id ?? 0]) }}" id="paypal-link" class="btn btn-primary mt-2">Paypal</a>
                        @endif
                    </div>
                </div>
                <div class="row text-left mt-5">
                    <div class="col-md-12">
                        <p>
                            {{translate('Explain')}}:<br />
                        </p>

                        <p>1. {{translate('The manufacturer has passed platform certification and paid a $50000 deposit')}}.</p>

                        <p>2. {{translate('If you need help, click on [Submit Work Order] to provide us with feedback on your issue')}}.</p>

                        <p>3. {{translate("If you are also a manufacturer and want your products to be placed on the platform's product warehouse for better sales, please click [Submit Work Order] to contact the platform")}}.</p>
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



</script>
