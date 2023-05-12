@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Payment Statement')}}</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <form action="{{ route('payment-statement.index') }}" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Payment Statement') }}</h5>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-md btn-primary" type="submit">
                            {{ translate('Filter') }}
                        </button>
                    </div>
                </div>
            </form>
            <div class="card-body">

                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Seller')}}</th>
                            <th data-breakpoints="lg">{{  translate('Date') }}</th>
                            <th>{{ translate('Transaction ID')}}</th>
                            <th>{{ translate('Inner Order No')}}</th>
                            <th>{{ translate('Outer Order No')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>{{ translate('Amount after exchanged')}}</th>
                            <th>{{ translate('Business Type')}}</th>
                            <th data-breakpoints="lg">{{ translate('Payment Method')}}</th>
                            <th data-breakpoints="lg" class="text-right">{{ translate('Status')}}</th>
                            <th data-breakpoints="lg" class="text-right">{{ translate('Reason')}}</th>
                            <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payment_statements as $key => $value)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                @if ($value->seller != null)
                                    <td>{{ $value->seller->name }}</td>
                                @else
                                    <td>{{ translate('User Not found') }}</td>
                                @endif
                                <td>{{ $value->created_at }}</td>
                                <td>{{ $value->transaction_id }}</td>
                                <td>{{ $value->order_no }}</td>
                                <td>{{ $value->out_order_no }}</td>
                                <td>{{ single_price($value->amount) }}</td>
                                <td>{{ number_format($value->amount_exchanged ?: $value->amount * getExchangeRate(), 2) }}</td>
                                <td>{{ translate($value->business_type) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $value ->payment_type)) }}</td>
                                <td class="text-right">
                                    @if ($value->status == 1)
                                        <span class="badge badge-inline badge-success">{{translate('Success')}}</span>
                                    @elseif ($value->status == 2)
                                        <span class="badge badge-inline badge-danger">{{translate('Failed')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">{{$value->failure_reason}}</td>
                                <td class="text-right">
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="manual_callback('{{ $value->out_order_no }}', '{{ $value->transaction_id }}', '{{ $value->amount }}', '{{ $value->payment_type }}')" title="{{ translate('Manual callback') }}">
                                        {{ translate('Manual callback') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination mt-4">
                    {{ $payment_statements->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        function manual_callback(orderid, transaction_id, money, pay_type) {
            var url, postData;
            if (pay_type == 'htpay') {
                url = "{{route('htpay.notify')}}";
                postData = {
                    returncode: '00',
                    orderid: orderid,
                    transaction_id: transaction_id,
                    money: money,
                }
            } else if (pay_type == 'qepay') {
                url = "{{route('qepay.notify')}}";
                postData = {
                    tradeResult: 1,
                    orderNo: orderid,
                    amount: money,
                    oriAmount: money,
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'POST',
                data: postData,
                success: function (response) {
                    if(response == 'ok' || response == 'success') {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
