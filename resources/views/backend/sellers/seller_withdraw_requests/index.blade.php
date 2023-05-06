@extends('backend.layouts.app')
@section('content')
    <script src='/My97DatePicker/WdatePicker.js'></script>
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
                <h1 class="h3">{{translate('Seller Withdraw Request')}} ({{translate('Total')}}: {{$total_seller}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h1>
            </div>
            <div class="col text-left">
            </div>
        </div>
    </div>
    <div class="card">
        <form class="" id="sort_withdraw_request" action="" method="GET">

        <div class="card-header">
                <div class="col-md-3 ml-auto">
                    <div class="form-group mb-0">
                        <input  onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" type="text" class="form-control" placeholder="{{translate('Start Time')}}" value="{{$start_time ?? ''}}" name="start_time" />

                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    <div class="form-group mb-0">

                        <input  onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" type="text" class="form-control" placeholder="{{translate('End Time')}}" value="{{$end_time ?? ''}}" name="end_time" />
                    </div>
                </div>
                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" data-live-search="true" name="seller_id" id="seller_id">
                        <option value="">{{translate('All')}}</option>
                        @foreach (filter_by_bloc(\App\Models\User::where('user_type', 'seller'))->get() as $key => $seller)
                            <option value="{{ $seller->id }}" @if($seller_id == $seller->id) selected @endif data-contact="{{ $seller->email }}">
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="status" id="status">
                        <option value="">{{translate('All')}}</option>
                        <option value="1"  @if($status == 1) selected @endif >{{translate('Paid')}}</option>
                        <option value="2"  @if($status == 2) selected @endif >{{translate('Refuse')}}</option>
                        <option value="0"  @if($status != '' && $status == 0) selected @endif >{{translate('Pending')}}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-styled">{{ translate('Search') }}</button>
        </div>
        </form>

        <div class="card-body" style="overflow-x: auto">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th data-breakpoints="lg">{{translate('Date')}}</th>
                        <th>{{translate('Seller')}}</th>
                        <th data-breakpoints="lg">{{translate('Outstanding Balance')}}</th>
                        <th data-breakpoints="lg">{{translate('Balance')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to buyer pay')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to pickup pay')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to profit')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to wallet recharge')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to wallet withdraw')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to commission')}}</th>
                        <th>{{translate('Requested Amount')}}</th>
                        <th>{{translate('Type')}}</th>
                        <th data-breakpoints="lg">{{ translate('Withdraw type') }}</th>
                        <th data-breakpoints="lg" width="20%">{{ translate('Message') }}</th>
                        <th data-breakpoints="lg">{{ translate('Payment Channel') }}</th>
                        <th data-breakpoints="lg">{{ translate('Status') }}</th>
                        <th data-breakpoints="lg">{{ translate('Pass Time') }}</th>
                        <th data-breakpoints="lg" width="15%" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seller_withdraw_requests as $key => $seller_withdraw_request)
                        @php $user = \App\Models\User::find($seller_withdraw_request->user_id); @endphp
                        @if ($user && $user->shop)
                            @php
                            $total_buyer_pay = (float) \App\Models\Order::query()->where(['seller_id' => $user->id])->sum('grand_total');
                            $total_pickup_pay = (float) \App\Models\Order::query()->where(['seller_id' => $user->id, 'product_storehouse_status' => 1])->sum('product_storehouse_total');
                            $total_storehouse = (float) \App\Models\Order::query()->where(['seller_id' => $user->id])->sum('product_storehouse_total');
                            $total_wallet_recharge = (float) \App\Models\Wallet::query()->where('offline_payment', 1)->where('user_id', $user->id)->where('approval', 1)->sum('amount');
                            $total_wallet_withdraw = (float) \App\Models\SellerWithdrawRequest::query()->where('type', 1)->where('user_id', $user->id)->where('status', 1)->sum('amount');
                            $total_commission = (float) \App\Models\AffiliateLog::query()->where('referred_by_user', $user->id)->sum('amount');
                            @endphp
                            <tr>
                                <td>{{ ($key+1) + ($seller_withdraw_requests->currentPage() - 1)*$seller_withdraw_requests->perPage() }}</td>
                                <td>{{ $seller_withdraw_request->created_at }}</td>
                                <td>{{ $user->name }} ({{ $user->shop->name }})</td>
                                <td>{{ single_price($user->shop->admin_to_pay) }}</td>
                                <td>{{ single_price($user->balance) }}</td>
                                <td>{{ single_price($total_buyer_pay) }}</td>
                                <td>{{ single_price($total_pickup_pay) }}</td>
                                <td>{{ single_price($total_buyer_pay - $total_storehouse) }}</td>
                                <td>{{ single_price($total_wallet_recharge) }}</td>
                                <td>{{ single_price($total_wallet_withdraw) }}</td>
                                <td>{{ single_price($total_commission) }}</td>
                                <td>{{ single_price($seller_withdraw_request->amount) }}</td>

                                  <td>
                            @if( $seller_withdraw_request->type == 1)

                            {{translate('User Balance')}}
                            @else

                              {{translate('Guarantee')}}
                            @endif
                        </td>
                                         <td>
                                    @if ($seller_withdraw_request->w_type == 1)
                                    {{translate('Cash')}}
                                    @elseif ($seller_withdraw_request->w_type == 2)
                                    {{translate('Bank')}}
                                    @elseif  ($seller_withdraw_request->w_type == 3)
                                     {{translate('USDT')}}
                                    @endif
                                </td>
                                <td>
                                    {{ $seller_withdraw_request->message }}
                                </td>
                                <td>{{$seller_withdraw_request->payment_channel ?? ''}}</td>
                                <td>
                                    @if ($seller_withdraw_request->status == 1)
                                    <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                    @elseif ($seller_withdraw_request->status == 2)
                                    <span class="badge badge-inline badge-error">{{translate('Refuse')}}</span>
                                    @elseif ($seller_withdraw_request->status == 3)
                                        <span class="badge badge-inline badge-info">{{translate('Approved')}}</span>
                                    @elseif ($seller_withdraw_request->status == 4)
                                        <span class="badge badge-inline badge-error">{{translate('Failed')}}</span>
                                    @else
                                    <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $seller_withdraw_request->status == 1 ? $seller_withdraw_request->updated_at : ''}}
                                </td>
                                <td class="text-right" width="300">
                                    <div style="display: flex;justify-content: flex-end;">
                                        @if ($seller_withdraw_request->status == 0)
                                            <a onclick="show_seller_payment_modal('{{$seller_withdraw_request->user_id}}','{{ $seller_withdraw_request->id }}');" class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Pay Now') }}">
                                                <i class="las la-money-bill"></i>
                                            </a>
                                            <a onclick="show_refuse_modal('{{$seller_withdraw_request->user_id}}','{{ $seller_withdraw_request->id }}');" class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Refuse') }}">
                                                <i class="las la-money-bill"></i>
                                            </a>
                                            @endif
                                            <a onclick="show_message_modal('{{ $seller_withdraw_request->id }}');" class="btn btn-soft-success btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Message View') }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a onclick="show_history_modal('{{ $seller_withdraw_request->id }}');" href="javascript:void(0);" class="btn btn-soft-primary btn-icon btn-circle btn-sm"  title="{{ translate('Payment History') }}">
                                                <i class="las la-history"></i>
                                            </a>
                                    </div>

                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $seller_withdraw_requests->links() }}
            </div>
        </div>
    </div>

@endsection

@section('modal')
<!-- payment Modal -->
<div class="modal fade" id="payment_modal">
  <div class="modal-dialog">
    <div class="modal-content" id="payment-modal-content">

    </div>
  </div>
</div>

<div class="modal fade" id="refuse_modal">
  <div class="modal-dialog">
    <div class="modal-content" id="refuse_modal-content">

    </div>
  </div>
</div>


<!-- Message View Modal -->
<div class="modal fade" id="message_modal">
  <div class="modal-dialog">
    <div class="modal-content" id="message-modal-content">

    </div>
  </div>
</div>

<!-- History View Modal -->
<div class="modal fade" id="history_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="history-modal-content">

        </div>
    </div>
</div>

@endsection



@section('script')
  <script type="text/javascript">
      function show_seller_payment_modal(id, seller_withdraw_request_id){
          $.post('{{ route('withdraw_request.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id, seller_withdraw_request_id:seller_withdraw_request_id}, function(data){
              $('#payment-modal-content').html(data);
              $('#payment_modal').modal('show', {backdrop: 'static'});
              $('.demo-select2-placeholder').select2();
          });
      }
      function show_refuse_modal(id, seller_withdraw_request_id){
          $.post('{{ route('withdraw_request.refuse_modal') }}',{_token:'{{ @csrf_token() }}', id:id, seller_withdraw_request_id:seller_withdraw_request_id}, function(data){
              $('#refuse_modal-content').html(data);
              $('#refuse_modal').modal('show', {backdrop: 'static'});
          });
      }

      function show_message_modal(id){
          $.post('{{ route('withdraw_request.message_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
              $('#message-modal-content').html(data);
              $('#message_modal').modal('show', {backdrop: 'static'});
          });
      }

      function show_history_modal(id){
          $.get('{{ route('withdraw_request.history_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
              $('#history-modal-content').html(data);
              $('#history_modal').modal('show', {backdrop: 'static'});
          });
      }
  </script>

@endsection
