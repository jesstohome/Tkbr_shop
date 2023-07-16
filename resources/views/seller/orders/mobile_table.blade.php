<link rel="stylesheet" href="{{ static_asset('assets/css/font-awesome.min.css') }}">
<style>
    .card {
        border:0;
    }
    .card form {
        border:1px solid #eee;
    }
    .card .card-header {
        border:0;
    }
    .card .card-body {
        padding: 5px 0!important;
    }
    .order-item {
        background: #eee;
        border-radius: 15px;
        margin-bottom: 15px;
        padding: 10px 10px 5px;
    }
    .order-info {
        border-top: 1px solid #eee;
        padding-top: 5px;
    }
    .order-code {
        border-bottom: 1px solid red;
        margin-bottom: 5px;
    }
    .order-btns {
        margin-bottom: 5px;
    }

    .bts {
        margin-top: 15px;
        display: flex;
        justify-content: flex-end;
    }
    .bts a {
        margin-right: 5px;
    }

    .show-more {
        padding: 15px 0 0;
        text-align: center;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
    }
</style>
<div>
    @foreach($orders as $order_id)
    @php
        $order = \App\Models\Order::find($order_id->id);
    @endphp
    <div class="order-item">
        <div class="order-code">
            {{translate('Order Code')}} {{ $order->code}}
        </div>
        <div class="order-btns">
            <div class="status">
                @if($order->delivery_status == 'cancelled')
                    <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                @else
                    @if ($order->product_storehouse_status)
                        <span class="badge badge-inline badge-success">{{translate('Picked Up')}}</span>
                    @else
                        @if($order->paymentStatement)
                            <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                        @else
                            @if ($order->product_storehouse_total)
                                <span class="badge badge-inline badge-danger">{{translate('Unpicked Up')}}</span>
                            @endif
                        @endif
                    @endif
                @endif

                @if($order->delivery_status == 'cancelled')
                    <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                @else
                    @if ($order->payment_status == 'paid')
                        <span class="badge badge-inline badge-success">{{ translate('Buyer has paid')}}</span>
                    @else
                        <span class="badge badge-inline badge-danger">{{ translate('Unpaid')}}</span>
                    @endif
                @endif
            </div>
            <div class="bts">
                <a href="{{ route('seller.orders.show', encrypt($order->id)) }}" class="btn btn-outline-info btn-sm" title="{{ translate('Order Details') }}" @if($order->delivery_status == 'cancelled') onclick="AIZ.plugins.notify('warning', '{{translate('The order has been cancelled')}}');return false;" @endif>
                    查看详情
                </a>
                <a href="{{ is_android() ? 'javascript:void(0);' : route('seller.invoice.download', $order->id) }}" class="btn btn-outline-warning btn-sm" title="{{ translate('Download Invoice') }}" @if (is_android()) onclick="downloadInvoicePdf('{{route('seller.invoice.download', $order->id)}}');" @endif>
                    下载订单
                </a>
                <a href="javascript:void(0);" class="btn btn-outline-info btn-sm" title="{{ translate('Order Details') }}" onclick="order_reply({{!empty($order->orderDetails[0]) ? $order->orderDetails[0]->product_id : 0}}, '{{!empty($order->orderDetails[0]->product->slug) ? route('product', $order->orderDetails[0]->product->slug) : ''}}', {{$order->seller_id}}, {{$order->user_id}}, '{{$order->user->name}}');">
                    联系客服
                </a>
            </div>
        </div>

        <div class="order-info" style="display: none">
            <div class="info-item">
                <div>{{ translate('Order Type') }}</div>
                <div>
                    @if ($order->order_type == 6)
                        <span class="badge badge-inline badge-danger">{{ translate('Urgent') }}</span>
                    @elseif ($order->order_type == 24)
                        {{ translate('ordinary') }}
                    @else
                        {{ translate('ordinary') }}
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Num. of Products') }}</div>
                <div>
                    {{ $order->orderDetails->where('seller_id', Auth::user()->id)->sum("quantity") }}
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Customer') }}</div>
                <div>
                    @if ($order->user_id != null)
                        {{ optional($order->user)->name }}
                    @else
                        {{ translate('Guest') }} ({{ $order->guest_id }})
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Pick Up Price') }}</div>
                <div>
                    {{ single_price($order->product_storehouse_total) }}
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Amount') }}</div>
                <div>
                    {{ single_price($order->grand_total) }}
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Profit') }}</div>
                <div>
                    @if ($order->product_storehouse_total > 0)
                        {{ single_price($order->grand_total - $order->product_storehouse_total) }}
                    @else
                        {{ translate('None') }}
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div>{{ translate('Delivery Status') }}</div>
                <div>
                    @php
                        $status = $order->delivery_status;
                    @endphp
                    {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                </div>
            </div>


        </div>
        <div class="show-more">
            <span>{{translate('Show More')}} <i class="icon-angle-down"></i></span>
        </div>
    </div>
    @endforeach
</div>
