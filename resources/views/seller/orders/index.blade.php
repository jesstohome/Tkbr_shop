@extends('seller.layouts.app')

@section('panel_content')
    <style type="text/css">
        .aiz-table td, .aiz-table th {
            padding: 1rem 0.1rem;
        }
        table span.badge {
            word-break: break-word;
            width: 5rem;
            height: auto;
            display: block;
            white-space: normal;
        }
    </style>
    <div class="row gutters-10 justify-content-center">
        @php
            $count = DB::table('orders')->where('seller_id', Auth::user()->id)->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->count();
            $grand_total = DB::table('orders')->where('seller_id', Auth::user()->id)->where('delivery_status', '!=', 'cancelled')->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->sum('orders.grand_total');
            $product_storehouse_total = DB::table('orders')->where('seller_id', Auth::user()->id)->where('delivery_status', '!=', 'cancelled')->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->sum('orders.product_storehouse_total');
            $total_turnover = "$".sprintf('%.2f',$grand_total);
            $total_profit = "$".sprintf('%.2f',($grand_total - $product_storehouse_total));
        @endphp
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $count }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Orders') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $total_turnover }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Turnover') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $total_profit }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Profit') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <form id="sort_orders" action="" method="GET">
          <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
              <h5 class="mb-md-0 h6">{{ translate('Orders') }}</h5>
            </div>
              <div class="col-md-2 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Payment Status')}}" name="payment_status">
                      <option value="">{{ translate('Filter by Payment Status')}}</option>
                      <option value="paid" @isset($payment_status) @if($payment_status == 'paid') selected @endif @endisset>{{ translate('Buyer has paid')}}</option>
                      <option value="unpaid" @isset($payment_status) @if($payment_status == 'unpaid') selected @endif @endisset>{{ translate('Un-Paid')}}</option>
                  </select>
              </div>

              <div class="col-md-2 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Pickup Status')}}" name="product_storehouse_status">
                      <option value="">{{ translate('Filter by Pickup Status')}}</option>
                      <option value="1" @isset($product_storehouse_status) @if($product_storehouse_status) selected @endif @endisset>{{ translate('Picked up')}}</option>
                      <option value="0" @isset($product_storehouse_status) @if(!is_null($product_storehouse_status) && $product_storehouse_status == 0) selected @endif @endisset>{{ translate('Not picked up')}}</option>
                  </select>
              </div>

              <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Delivery Status')}}" name="delivery_status">
                    <option value="">{{ translate('Filter by Deliver Status')}}</option>
                    @foreach(get_express_status() as $status_key => $status_text)
                    <option value="{{$status_key}}" @isset($delivery_status) @if($delivery_status == $status_key) selected @endif @endisset>{{ $status_text}}</option>
                    @endforeach
                    <option value="cancelled" @isset($delivery_status) @if($delivery_status == 'cancelled') selected @endif @endisset>{{translate('Cancelled')}}</option>

                </select>
              </div>

              <div class="col-md-2 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Payment Type')}}" name="delivery_status">
                      <option value="">{{ translate('Filter by Payment Type')}}</option>
                      <option value="cancelled" @isset($delivery_status) @if($delivery_status == 'cancelled') selected @endif @endisset>{{translate('Cancelled')}}</option>
                  </select>
              </div>

              <div class="col-md-2">
                  <div class="form-group mb-0">
                      <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
                  </div>
              </div>

              <div class="col-md-3">
                <div class="from-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group mt-1" style="display: flex;justify-content: space-between;">
                      <button class="btn btn-sm btn-light" type="reset" onclick="reset_form()">重置</button>
                      <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                  </div>
              </div>
          </div>
        </form>

        @if (count($orders) > 0)
            <div class="card-body p-3">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th data-breakpoints="lg">#</th>
                            <th>{{ translate('Order Code')}}</th>
                            <th data-breakpoints="lg">{{ translate('Order Type') }}</th>
                            <th data-breakpoints="lg">{{ translate('Num. of Products')}}</th>
                            <th data-breakpoints="lg">{{ translate('Customer')}}</th>
                            <th data-breakpoints="md">{{ translate('Pick Up Price')}}</th>
                            <th data-breakpoints="md">{{ translate('Amount')}}</th>
                            <th data-breakpoints="md">{{ translate('Profit')}}</th>
                            <th>{{ translate('Pick Up Status') }}</th>
                            <th data-breakpoints="lg">{{ translate('Delivery Status')}}</th>
                            <th>{{ translate('Payment Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $key => $order_id)
                            @php
                                $order = \App\Models\Order::find($order_id->id);
                            @endphp
                            @if($order != null)
                                <tr>
                                    <td>
                                        {{ $key+1 }}
                                    </td>
                                    <td>
                                        <a href="#{{ $order->code }}" onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                    </td>
                                    <td>
                                         @if ($order->order_type == 6)
                                          <span class="badge badge-inline badge-danger">{{ translate('Urgent') }}</span>
                                         @elseif ($order->order_type == 24)
                                          {{ translate('ordinary') }}
                                         @else
                                          {{ translate('ordinary') }}
                                         @endif
                                    </td>
                                    <td>
                                        {{ count($order->orderDetails->where('seller_id', Auth::user()->id)) }}
                                    </td>
                                    <td>
                                        @if ($order->user_id != null)
                                            {{ optional($order->user)->name }}
                                        @else
                                            {{ translate('Guest') }} ({{ $order->guest_id }})
                                        @endif
                                    </td>
                                    <td>
                                        {{ single_price($order->product_storehouse_total) }}
                                    </td>
                                    <td>
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                    <td>
                                        @if ($order->product_storehouse_total > 0)
                                            {{ single_price($order->grand_total - $order->product_storehouse_total) }}
                                        @else
                                            {{ translate('None') }}
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>
                                        @php
                                            $status = $order->delivery_status;
                                        @endphp
                                        {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                    </td>
                                    <td>
                                        @if($order->delivery_status == 'cancelled')
                                            <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                                        @else
                                            @if ($order->payment_status == 'paid')
                                                <span class="badge badge-inline badge-success">{{ translate('Buyer has paid')}}</span>
                                            @else
                                                <span class="badge badge-inline badge-danger">{{ translate('Unpaid')}}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div style="display: flex;justify-content: flex-end;">
                                            <a href="{{ route('seller.orders.show', encrypt($order->id)) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}" @if($order->delivery_status == 'cancelled') onclick="AIZ.plugins.notify('warning', '{{translate('The order has been cancelled')}}');return false;" @endif>
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a href="{{ route('seller.invoice.download', $order->id) }}" class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="{{ translate('Download Invoice') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $orders->links() }}
              	</div>
            </div>
        @endif
    </div>

@endsection
