@extends('seller.layouts.app')

@section('panel_content')
<style type="text/css">
    .card .card-body {
        padding: 20px 5px;
    }

    .table-withdraw-history td, .table-withdraw-history th {
        text-align: center;
        padding: 1rem 0.3rem;
    }
    .table-withdraw-history td:first-child {
        width: 2.1rem;
    }

    .table-froze-order td:first-child {
        padding: 1rem 0.3rem;
    }
    .table-withdraw-history td:first-child span,
    .table-froze-order td:first-child span{
        margin-right:0;
    }

</style>
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Money Withdraw') }}</h1>
            </div>
        </div>
    </div>

    <div class="row gutters-0">
        <div class="col-md-2 mb-3 mx-auto">
            <div class="bg-grad-3 text-white rounded-lg overflow-hidden">
              <span
                  class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                  <i class="las la-dollar-sign la-2x text-black-50"></i>
              </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->shop->admin_to_pay) }}</div>
                    <div class="opacity-50 text-center">{{ translate('Pending Balance') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3 mx-auto">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
              <span
                  class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                  <i class="las la-dollar-sign la-2x  text-black-50"></i>
              </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->balance) }}</div>
                    <div class="opacity-50 text-center">{{ translate('Wallet Money') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3 mx-auto">
            <div
                class="bg-grad-2 p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                onclick="show_request_modal()">
              <span
                  class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                  <i class="las la-plus la-3x text-white"></i>
              </span>
                <div class="fs-18 text-white">{{ translate('Send Withdraw Request') }}</div>
            </div>
        </div>
        @if (addon_is_activated('offline_payment'))

              <div class="col-md-2 mb-3 mr-auto">
                <div
                    class="bg-grad-4 p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                    onclick="show_make_wallet_recharge_modal(2)">
              <span
                  class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                  <i class="las la-plus la-3x text-white"></i>
              </span>
                    <div class="fs-18 text-white">{{ translate('Guarantee Recharge') }}</div>
                </div>
            </div>


        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Withdraw Request history')}}</h5>
        </div>
        <div class="card-body">
            <table class="table table-withdraw-history aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Amount')}}</th>
                    <th>{{ translate('Type')}}</th>

                    <th data-breakpoints="lg">{{ translate('Status')}}</th>
                    <th>{{ translate('Withdraw Type')}}</th>
                    <th>{{ translate('Remarks')}}</th>
                    <th data-breakpoints="lg" width="40%">{{ translate('Message')}}</th>


                </tr>
                </thead>
                <tbody>
                @foreach ($seller_withdraw_requests as $key => $seller_withdraw_request)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ date('d-m-Y', strtotime($seller_withdraw_request->created_at)) }}</td>
                        <td>{{ single_price($seller_withdraw_request->amount) }}</td>
                        <td>
                            @if( $seller_withdraw_request->type == 1)

                                {{translate('User Balance')}}
                            @else

                              {{translate('Guarantee')}}
                            @endif
                        </td>
                        <td>
                            @if ($seller_withdraw_request->status == 1)
                                <span class=" badge badge-inline badge-success">{{ translate('Paid')}}</span>
                             @elseif ($seller_withdraw_request->status == 2)
                                <span class=" badge badge-inline badge-danger">{{ translate('Refuse')}} </span>
                            @elseif ($seller_withdraw_request->status == 3)
                                <span class=" badge badge-inline badge-info">{{ translate('Approved')}} </span>
                            @elseif ($seller_withdraw_request->status == 4)
                                <span class=" badge badge-inline badge-danger">{{ translate('Failed')}} </span>
                            @else
                                <span class=" badge badge-inline badge-info">{{ translate('Pending')}}</span>
                            @endif
                        </td>
                        <td>

                            @if( $seller_withdraw_request->w_type == 1)

                            {{translate('Cash')}}
                            @elseif( $seller_withdraw_request->w_type == 2)

                              {{translate('Bank')}}
                            @elseif( $seller_withdraw_request->w_type == 3)
                            {{translate('USDT')}}
                            @endif
                        </td>
                             <td>
                            {{ $seller_withdraw_request->remarks }}
                        </td>
                        <td>
                            {{ $seller_withdraw_request->message }}
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $seller_withdraw_requests->links() }}
            </div>
        </div>
    </div>

    <!-- 待解冻订单 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Froze Order')}}</h5>
        </div>
        <div class="card-body">
            <table class="table table-froze-order aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Profit') }}</th>
                    <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                    <th data-breakpoints="md">{{ translate('Pick Up Status') }}</th>
                    <th>{{ translate('Date') }}</th>
                    <th>{{ translate('Unfreeze Countdown') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($freezeOrders as $key => $order)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $order->code }}</td>
                        <td>{{ single_price($order->grand_total) }}</td>
                        <td>{{ single_price($order->grand_total - $order->product_storehouse_total) }}</td>
                        <td>
                            @if ($order->payment_status == 'paid')
                                <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                            @endif
                        </td>
                        <td>
                            @if ($order->product_storehouse_status)
                                <span class="badge badge-inline badge-success">{{translate('Picked Up')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('Unpicked Up')}}</span>
                            @endif
                        </td>
                        <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                        <td>
                            @if ($order->freeze_expired_at)
                                {{ round(($order->freeze_expired_at - now()->timestamp) / 86400) }} {{translate('Days')}}
                            @else
                                {{translate('Unpicked Up')}}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $freezeOrders->links() }}
            </div>
        </div>
    </div>

    <!-- 钱包收支明细 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Wallet Recharge History')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Payment method') }}</th>
                    <th>{{ translate('Payment Details') }}</th>
                    <th data-breakpoints="md">{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Approval') }}</th>
                    <th data-breakpoints="md">{{ translate('Offline payment') }}</th>
                    <th data-breakpoints="md">{{ translate('Type') }}</th>
                    <th data-breakpoints="md">{{ translate('Receipt') }}</th>
                    <th>{{ translate('Date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rechargeList as $key => $list)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ single_price($list->amount) }}</td>
                        <td>{{ $list->payment_method }}</td>
                        <td>{{ $list->payment_details }}</td>
                        <td>{{ $list->order->code ?? ''}}</td>
                        <td>
                            @if ($list->offline_payment == 1)
                                @if ($list->approval == 1)
                                    <span class="badge badge-inline badge-success">{{translate('Pass')}}</span>
                                @elseif ($list->approval == 2)
                                    <span class="badge badge-inline badge-danger">{{translate('No Pass')}}</span>
                                @else
                                    <span class="badge badge-inline badge-info">{{translate('Unaudited')}}</span>
                                @endif
                            @elseif($list->paymentStatement)
                                @if ($list->paymentStatement->status == 0)
                                    <span class="badge badge-inline badge-info">{{translate('Unpaid')}}</span>
                                @elseif ($list->paymentStatement->status == 1)
                                    <span class="badge badge-inline badge-success">{{translate('Success')}}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{translate('Failed')}}</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($list->offline_payment == 1)
                                <span class="badge badge-inline badge-success">{{translate('yes')}}</span>
                            @else
                                {{$list->payment_method}}
                            @endif
                        </td>

                         <td>
                            @if( $list->type == 1)

                            {{translate('User Balance')}}
                             @elseif($list->type == 3)
                                 {{translate('Pick Up')}}
                            @else

                              {{translate('Guarantee')}}
                            @endif
                        </td>


                        <td>{{ $list->reciept }}</td>
                        <td>{{ date('d-m-Y', strtotime($list->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $rechargeList->links() }}
            </div>
        </div>
    </div>




      <!-- 充值记录 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Payment History')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md">{{ translate('Amount') }}</th>

                    <th>{{ translate('Payment Details') }}</th>

                    <th data-breakpoints="md">{{ translate('Payment method') }}</th>


                    <th>{{ translate('Date') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($paymentList as $key => $list)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ single_price($list->amount) }}</td>

                        <td>{{ $list->payment_details }}</td>

                        <td>
                            {{translate($list->payment_method)}}
                        </td>





                        <td>{{ date('d-m-Y', strtotime($list->created_at)) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $paymentList->links() }}
            </div>
        </div>
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

@section('modal')
    <!-- offline payment Modal -->
    <div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ translate('Offline Recharge Wallet') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="offline_wallet_recharge_modal_body"></div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Send A Withdraw Request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($balance >= (int) get_setting('minimum_seller_amount_withdraw'))
                    <form id="withdraw-form" class="" action="{{ route('seller.money_withdraw_request.store') }}" method="post">
                        @csrf
                        <div class="modal-body gry-bg px-3 pt-3">
                            <div class="row">
                                <div class="col">
                                    <div class="alert alert-success" role="alert">
                                        <h6>{{ translate('Your wallet balance :') }} ${{ $balance }}</h6>
                                    </div>

                                    <div class="alert alert-success" role="alert">
                                        <h6>{{ translate('Your guarantee balance :') }}
                                        ${{Auth::user()->shop->bzj_money}} </h6>
                                    </div>


                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Currency Selection')}}</label>
                                </div>
                                <div class="col-md-9">
                                    @php
                                        $currencies = \App\Models\Currency::query()->where('status', 1)->get();
                                        $exchange_rate = \App\Models\Currency::query()->where('code', $bloc->currency_code)->value('exchange_rate');
                                    @endphp
                                    <select class="form-control aiz-selectpicker" name="currency" id="currency" @if(!empty($bloc->currency_code)) disabled @endif>
                                        <option value="">{{translate('Currency Selection')}}</option>
                                        @foreach ($currencies as $key => $currency)
                                            <option value="{{$currency->code}}" data-exchange-rate="{{$currency->exchange_rate}}" data-currency-name="{{translate($currency->name)}}" {{$currency->code == $bloc->currency_code ? 'selected' : ''}}>{{translate($currency->name)}}</option>
                                        @endforeach
                                    </select>
                                    @if(!empty($bloc->currency_code))
                                        <input type="hidden" name="currency" class="form-control" readonly value="{{$bloc->currency_code}}" />
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-3">
                                    <label>{{ translate('Exchange Rate')}}</label>
                                </div>
                                <div class="col-md-9 {{is_mobile() ? '' : 'text-left'}}">
                                    <span id="exchange-rate" style="font-weight: 600;font-size: 14px;">{{$exchange_rate ? '1 ' . translate('dollar') . ' ≈ ' . $exchange_rate . ' ' . translate($currency_name) : ''}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Amount')}} <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" lang="en" class="form-control mb-3" name="amount" placeholder="{{ translate('Amount') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-9 text-left">
                                    <span id="exchange-rate-value" class="mt-0" style="font-weight:600;font-size: 14px;"></span>
                                </div>
                            </div>
                             <div class="row" style="margin-bottom:5px;">

                                 <div class="col-md-3">
                                    <label>{{ translate('Opera Type')}}</label>
                                </div>
                                 <div class="col-md-9">
                                     <select name="type" class="form-control">
                                         <option value="1">{{translate('User Balance')}}</option>
                                     </select>
                                </div>

                            </div>
                            <div class="row" style="margin-bottom:5px;">

                                <div class="col-md-3">
                                    <label>{{ translate('Country')}}<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <select id="country_code" name="country_code" class="form-control" onchange="changeCountry(this)">
                                        <option value="">{{translate('All')}}</option>
                                        @foreach(getPaymentCountries() as $country)
                                        <option value="{{$country->code}}" {{$shop->cur_payment_country_code == $country->code ? 'selected' : ''}}>{{translate($country->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="row" style="margin-bottom:5px;">

                                 <div class="col-md-3">
                                    <label>{{ translate('Withdraw Type')}}<span class="text-danger">*</span></label>
                                </div>
                                 <div class="col-md-9">
                                     <select name="w_type" class="form-control" id="p">
                                         @if(get_setting('withdraw_type_bank_card') == 1)
                                            <option value="2">{{translate('Bank')}}</option>
                                         @endif
                                         @if(get_setting('withdraw_type_e_wallet') == 1)
                                             <option value="5">{{translate('e-Wallet')}}</option>
                                         @endif

                                     </select>
                                </div>

                                </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label>{{ translate('Display Information')}}</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="message" rows="8" class="form-control mb-3" readonly>@if(get_setting('withdraw_type_bank_card') == 1){{$shop_payment_config->bank_name}} {{$shop_payment_config->bank_no}} {{$shop_payment_config->bank_account_name}} @elseif(get_setting('withdraw_type_e_wallet') == 1) {{$shop_payment_config->e_wallet_name}} {{$shop_payment_config->e_wallet_address}} @endif</textarea>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{translate('Send')}}</button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="p-5 heading-3 text-center">
                            <h3>{{ sprintf(translate('The minimum withdrawal amount is %s dollar'), (int) get_setting('minimum_seller_amount_withdraw')) }}</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>1
@endsection

@section('script')
    <script type="text/javascript">
        function show_request_modal() {
            $('#request_modal').modal('show');
        }

        function show_message_modal(id) {
            $.post('{{ route('withdraw_request.message_modal') }}', {
                _token: '{{ @csrf_token() }}',
                id: id
            }, function (data) {
                $('#message_modal .modal-content').html(data);
                $('#message_modal').modal('show', {backdrop: 'static'});
            });
        }
        function show_make_wallet_recharge_modal(type){
            if( type == 2 )
            {
                $("#exampleModalLabel").text('{{translate('Guarantee Recharge')}}');
            }
            $.post('{{ route('offline_wallet_recharge_modal') }}', {type:type,_token:'{{ csrf_token() }}'}, function(data){
                $('#offline_wallet_recharge_modal_body').html(data);
                $('#offline_wallet_recharge_modal').modal('show');
            });
        }

        function changeCountry(evt) {
            let country_code = $("#country_code").val().trim();
            if (country_code === '') return;

            $.post('{{ route('seller.withdraw_request.change_country') }}', {
                _token: '{{ @csrf_token() }}',
                code: country_code,
                type: $("#p").val()
            }, function (data) {
                if (data.trim() === '') {
                    AIZ.plugins.notify('danger', '{{ translate('Please bind the withdrawal information first!') }}');
                    setTimeout(function () {
                        window.location.href = "/seller/profile";
                    }, 1000);
                    return;
                }
                $("textarea[name=message]").val(data)
            });
        }

        $("#p").change(function(){
            changeCountry()
        });

        // 货币选择
        var exchange_rate = parseFloat("{{$exchange_rate ?: 1}}");
        var currency_name = "{{translate($currency_name)}}";
        $("#currency").on("change", function () {
            exchange_rate = parseFloat($(this).find("option:selected").attr('data-exchange-rate'));
            currency_name = $(this).find("option:selected").attr('data-currency-name');
            $("#exchange-rate").html('1 ' + "{{translate('dollar')}}" + ' ≈ ' + exchange_rate + ' ' + currency_name);
            if ($("input[name=amount]").val().trim() != '') {
                $("#exchange-rate-value").html(' ≈ ' + ($("input[name=amount]").val() * exchange_rate).toFixed(5) + ' ' + currency_name);
            }
        });

        $(document).ready(function(){
            // 自动打开充值弹窗
            @if(!empty($auto_show_recharge))
            show_make_wallet_recharge_modal(1);
            @endif

            $("input[name=amount]").on("input", function () {
               $("#exchange-rate-value").html(' ≈ ' + ($(this).val() * exchange_rate).toFixed(5) + ' ' + currency_name);
            });
        })
    </script>
@endsection
