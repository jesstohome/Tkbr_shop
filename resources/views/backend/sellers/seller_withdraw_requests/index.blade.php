@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Seller Withdraw Request')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th data-breakpoints="lg">{{translate('Date')}}</th>
                        <th>{{translate('Seller')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to freezing')}}</th>
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
                        <th data-breakpoints="lg">{{ translate('Status') }}</th>
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
                                <td>
                                    @if ($seller_withdraw_request->status == 1)
                                    <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                    @elseif ($seller_withdraw_request->status == 2)
                                    <span class="badge badge-inline badge-error">{{translate('Refuse')}}</span>
                                    @else
                                    <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if ($seller_withdraw_request->status == 0)
                                    <a onclick="show_seller_payment_modal('{{$seller_withdraw_request->user_id}}','{{ $seller_withdraw_request->id }}');" class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Pay Now') }}">
                                        <i class="las la-money-bill"></i>
                                    <a onclick="show_refuse_modal('{{$seller_withdraw_request->user_id}}','{{ $seller_withdraw_request->id }}');" class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Refuse') }}">
                                    <i class="las la-money-bill"></i>
                                    </a>
                                    @endif
                                    <a onclick="show_message_modal('{{ $seller_withdraw_request->id }}');" class="btn btn-soft-success btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Message View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    <a href="{{route('sellers.payment_history', encrypt($seller_withdraw_request->user_id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm"  title="{{ translate('Payment History') }}">
                                        <i class="las la-history"></i>
                                    </a>
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
  </script>

@endsection
