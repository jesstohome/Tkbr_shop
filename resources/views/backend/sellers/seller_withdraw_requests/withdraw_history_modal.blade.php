<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"></button>
</div>
<div class="modal-body">
    <ul id="myTab" class="nav nav-tabs nav-fill border-light">
        <li class="nav-item">
            <a class="nav-link active" href="#home" data-toggle="tab">{{translate('Detail')}}</a>
        </li>
        <li class="nav-item"><a class="nav-link" href="#ios" data-toggle="tab">{{translate('Wallet Recharge Records')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="#jmeter" data-toggle="tab">{{translate('Wallet Withdraw Records')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="#commission" data-toggle="tab">{{translate('Commission Promotion Records')}}</a></li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade show in active" id="home">
            <table class="table mb-0">
                <thead>
                    <th>{{translate('Order Code')}}</th>
                    <th>{{translate('Amount of Buyer Pay')}}</th>
                    <th>{{translate('Storehouse Price')}}</th>
                    <th>{{translate('Profit')}}</th>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{$order->code}}</td>
                        <td>{{$order->grand_total}}</td>
                        <td>{{$order->product_storehouse_total}}</td>
                        <td>{{$order->grand_total - $order->product_storehouse_total}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="ios">
            <table class="table mb-0">
                <thead>
                <th>{{translate('Amount')}}</th>
                <th>{{translate('Payment Method')}}</th>
                <th>{{translate('Payment Details')}}</th>
                <th>{{translate('Approval')}}</th>
                <th>{{ translate('Date') }}</th>
                </thead>
                <tbody>
                @foreach($wallets_recharge as $row)
                    <tr>
                        <td>{{ single_price($row->amount) }}</td>
                        <td>{{ $row->payment_method }}</td>
                        <td>{{ $row->payment_details }}</td>
                        <td>
                            @if ($row->approval == 1)
                                <span class="badge badge-inline badge-success">{{translate('yes')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('No')}}</span>
                            @endif
                        </td>
                        <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="jmeter">
            <table class="table mb-0">
                <thead>
                <th>{{translate('Amount')}}</th>
                <th>{{translate('Message')}}</th>
                <th>{{translate('Status')}}</th>
                <th>{{translate('Remark')}}</th>
                <th>{{ translate('Date') }}</th>
                </thead>
                <tbody>
                @foreach($wallets_withdraw as $row)
                    <tr>
                        <td>{{ single_price($row->amount) }}</td>
                        <td>{{ $row->message }}</td>
                        <td>
                            @if ($row->status == 1)
                                <span class="badge badge-inline badge-success">{{translate('yes')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('No')}}</span>
                            @endif
                        </td>
                        <td>{{ $row->remark }}</td>
                        <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="commission">
            <table class="table mb-0">
                <thead>
                <th>{{ translate('shop name')}}</th>
                <th data-breakpoints="lg">{{ translate('Order number')}}</th>
                <th data-breakpoints="lg">{{ translate('brokerage amount') }}</th>
                <th data-breakpoints="lg">{{ translate('level') }}</th>
                </thead>
                <tbody>
                @foreach($shops as $shop)
                    <tr>
                        <td>
                            {{ $shop['shop_name'] }}
                        </td>
                        <td>{{ $shop['order_number'] }}</td>
                        <td>
                            {{ single_price($shop['brokerage']) }}
                        </td>
                        <td>{{ $shop['level'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
</div>
