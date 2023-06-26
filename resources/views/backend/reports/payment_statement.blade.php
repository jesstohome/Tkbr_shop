@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class=" align-items-center">
        <h1 class="h3">{{translate('Payment Statement')}} ({{translate('Total')}}: {{$total_seller}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h1>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <form action="{{ route('payment-statement.index') }}" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="seller_id" name="seller_id" data-live-search="true">
                                <option value="">{{ translate('All Sellers') }}</option>
                                @foreach (filter_by_bloc(App\Models\User::where('user_type', '=', 'seller'))->get() as $key => $seller)
                                    <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                                        {{ $seller->shop->name }} ({{ $seller->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" id="order_code" name="order_code" @isset($order_code) value="{{ $order_code }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control" id="inner_order_code" name="inner_order_code" @isset($inner_order_code) value="{{ $inner_order_code }}" @endisset placeholder="{{ translate('Type Inner Order code & hit Enter') }}">
                        </div>
                    </div>

                    <div class="col-lg-2 ml-auto">
                        <select class="form-control aiz-selectpicker" name="status" id="status">
                            <option value="">{{translate('Filter by Status')}}</option>
                            <option value="0" @if ($status != '' && $status == 0) selected @endif>{{translate('Pending')}}</option>
                            <option value="1" @if ($status != '' && $status == 1) selected @endif>{{translate('Success')}}</option>
                            <option value="2" @if ($status != '' && $status == 2) selected @endif>{{translate('Failed')}}</option>
                        </select>
                    </div>
                    <div class="col-lg-2 ml-auto">
                        <select class="form-control aiz-selectpicker" name="payment_type" id="payment_type">
                            <option value="">{{translate('Filter by Payment method')}}</option>
                            <option value="artificial" @if ($payment_type == 'artificial') selected @endif>人工付款</option>
                            <option value="htpay" @if ($payment_type == 'htpay') selected @endif>Htpay</option>
                            <option value="india_htpay" @if ($payment_type == 'india_htpay') selected @endif>印度Htpay</option>
                            <option value="qepay" @if ($payment_type == 'qepay') selected @endif>Qepay</option>
                            <option value="work_order" @if ($payment_type == 'work_order') selected @endif>{{translate('Work Order')}}</option>
                        </select>
                    </div>

                    <div class="col-lg-2 ml-auto">
                        <select class="form-control aiz-selectpicker" name="business_type" id="business_type">
                            <option value="">{{translate('Business type')}}</option>
                            <option value="pick_up" @if ($business_type == 'pick_up') selected @endif>{{translate('Pick Up')}}</option>
                            <option value="withdraw" @if ($business_type == 'withdraw') selected @endif>{{translate('Withdraw')}}</option>
                        </select>
                    </div>

                    @include('backend.partials.filters.bloc_staff')

                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-md btn-primary" type="submit">
                            {{ translate('Filter') }}
                        </button>
                        <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
                    </div>
                </div>
            </form>
            <div class="card-body payment-statement" style="overflow-x: auto">

                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Seller')}}</th>
                            <th data-breakpoints="lg">{{  translate('Date') }}</th>
                            @if (isSupperAdmin())<th>{{ translate('Bloc')}}</th> @endif
                            <th>{{ translate('Person Responsible')}}</th>
                            <th>{{ translate('Order No')}}</th>
                            <th>{{ translate('Inner Order No')}}</th>
                            <th>{{ translate('Outer Order No')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th>{{ translate('Amount after exchanged')}}</th>
                            <th>{{ translate('Exchange Rate')}}</th>
                            <th>{{ translate('Business Type')}}</th>
                            <th data-breakpoints="lg">{{ translate('Payment Method')}}</th>
                            <th data-breakpoints="lg" class="text-right">{{ translate('Status')}}</th>
                            <th data-breakpoints="lg">{{ translate('Remark')}}</th>
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
                                @if (isSupperAdmin()) <td>{{ $value->bloc->name }}</td> @endif
                                <td>{{ $value->staff->user->name }}</td>
                                <td>{{ $value->order ? $value->order->code : '' }}</td>
                                <td>{{ $value->order_no }}</td>
                                <td>{{ $value->out_order_no ?: $value->transaction_id}}</td>
                                <td>{{ single_price($value->amount) }}</td>
                                <td>{{ number_format($value->amount_exchanged, 2) }}</td>
                                <td>{{ number_format($value->exchange_rate, 2) }}</td>
                                <td>{{ translate($value->business_type) }}</td>
                                <td>{{ 'artificial' == $value ->payment_type ? '人工付款' : ucfirst(str_replace('_', ' ', $value ->payment_type)) }}</td>
                                <td class="text-right">
                                    @if ($value->status == 1)
                                        <span class="badge badge-inline badge-success">{{translate('Success')}}</span>
                                    @elseif ($value->status == 2)
                                        <span class="badge badge-inline badge-danger">{{translate('Failed')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                    @endif
                                </td>
                                <td class="remark" data-id="{{$value->id}}">{{$value->remark}}</td>
                                <td class="text-right">{{$value->failure_reason}}</td>
                                <td class="text-right" style="min-width: 100px">
                                    @if($value->payment_type != 'artificial')
                                    <a class="btn btn-soft-warning btn-icon btn-circle btn-sm comfirm-link" style="width: auto"  href="javascript:void(0);" onclick="showCallbackModal('{{ $value->out_order_no }}', '{{ $value->transaction_id }}', '{{ $value->amount }}', '{{ $value->payment_type }}')" title="{{ translate('Manual callback') }}">
                                        {{ translate('Manual callback') }}
                                    </a>
                                    @endif
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

@section('modal')
    <div id="callback-modal" class="modal fade">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">回调确认</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">确认手动回调该订单?</p>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <a href="javascript:void(0)" class="btn btn-primary mt-2 comfirm-link" onclick="manual_callback()">确定</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var out_order_no, transaction_id, money, pay_type;
        function showCallbackModal(p1, p2, p3, p4) {
            out_order_no = p1;
            transaction_id = p2;
            money = p3;
            pay_type = p4;
            $("#callback-modal").modal("show");
        }
        function manual_callback() {
            var url, postData;
            console.log(out_order_no, transaction_id, money, pay_type);
            if (pay_type === 'htpay' || pay_type === 'india_htpay') {
                if (pay_type === 'htpay') {
                    url = "{{route('htpay.notify')}}";
                } else {
                    url = "{{route('india_htpay.notify')}}";
                }

                postData = {
                    returncode: '00',
                    orderid: out_order_no,
                    transaction_id: transaction_id,
                    money: money,
                }
            } else if (pay_type === 'qepay') {
                url = "{{route('qepay.notify')}}";
                postData = {
                    tradeResult: 1,
                    orderNo: out_order_no,
                    amount: money,
                    oriAmount: money,
                }
            } else if (pay_type === 'winpay') {
                url = "{{route('winpay.notify')}}";
                postData = {
                    params: JSON.stringify({
                        system_ref: out_order_no,
                        amount: money,
                        status: 1,
                    }),
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
                    } else {
                        AIZ.plugins.notify('danger', response);
                    }
                }
            });
        }

        function reset_form() {
            $('.aiz-selectpicker').selectpicker('val', '');
            $('.aiz-selectpicker').each((k, it) => {
                $(it).find("option").first().attr("selected", true).siblings().attr("selected", false);
            });
            setTimeout(function () {
                $("form input[type=text]").val('');
            }, 200);
        }

        $(document).ready(function () {
            $("td.remark").on("click", function () {
                if ($(this).hasClass('editing')) return;

                $(this).addClass('editing');
                var that = $(this);
                var edittd = "<input id='remark-name' type='text' value='" + $(this).html() + "' />";
                $(this).html(edittd);
            });

            $("body").on("keypress", "#remark-name", function (event) {
                if (event.keyCode != 13) return;

                var curTd = $(this).parent("td");
                var remark = $("#remark-name").val() || '';
                curTd.removeClass('editing').html(remark);

                $.ajax( {
                    headers: {
                        'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                    },
                    url: "{{route('payment_statement.update_remark')}}",
                    type: 'POST',
                    data: {
                        id: curTd.data("id"),
                        remark: remark,
                    },
                    success: function (response)
                    {
                        if ( response.success) {
                            AIZ.plugins.notify('success', '{{ translate('Successfully edited') }}');
                        }
                    }
                } );
            });
        });
    </script>
@endsection
