@extends('seller.layouts.app')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Payment History') }}</h5>
        </div>
        @if (count($payments) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>{{ translate('Payment Method')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $key => $payment)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>{{ date('d-m-Y', strtotime($payment->created_at)) }}</td>
                                <td>
                                    {{ single_price($payment->amount) }}
                                </td>
                                <td>
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} @if ($payment->txn_code != null) ({{  translate('TRX ID') }} : {{ $payment->txn_code }}) @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                	{{ $payments->links() }}
              	</div>
            </div>
        @endif
    </div>

    <!-- 钱包支出明细 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Wallet Expense Details')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>

                    <th>{{ translate('Type') }}</th>

                    <th>{{ translate('Date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($walletExpenseList as $key => $list)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ single_price($list->amount) }}</td>

                        <td>{{ translate($list->type) }}</td>

                        <td>{{ date('d-m-Y', strtotime($list->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $walletExpenseList->links() }}
            </div>
        </div>
    </div>

@endsection
