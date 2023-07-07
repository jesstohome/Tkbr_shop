@extends('backend.layouts.app')
<style type="text/css">
    .card-body {
        overflow-x: auto;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-12"><h5 class="mb-md-0 h6">({{translate('Total')}}: {{$total_customers}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h5></div>
    </div>
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    @foreach(get_express_status() as $status_key => $status_text)
                        <option value="{{$status_key}}" @isset($delivery_status) @if($delivery_status == $status_key) selected @endif @endisset>{{ $status_text}}</option>
                    @endforeach
                    <option value="cancelled" @isset($delivery_status) @if($delivery_status == 'cancelled') selected @endif @endisset>取消的</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off" data-advanced-range="true">
                </div>
            </div>

            @include('backend.partials.filters.seller')
            @include('backend.partials.filters.payment_code')

            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="customer_id" name="customer_id" data-live-search="true">
                        <option value="">{{ translate('All Customers') }}</option>
                        @foreach (filter_by_bloc(App\Models\User::where('user_type', '=', 'customer'))->get() as $key => $customer)
                            <option value="{{ $customer->id }}" @if ($customer->id == $customer_id) selected @endif>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            @include('backend.sales.filter')
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{ translate('Order Code') }}</th>
                        <th>{{ translate('Order Type') }}</th>
                        @if (isSupperAdmin())<th>{{ translate('Bloc') }}</th>@endif
                        <th>{{ translate('Staffs') }}</th>
                        <th>{{ translate('Shop') }}</th>
                        <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Profit') }}</th>
                        <th data-breakpoints="md">{{ translate('Pick Up Status') }}</th>
                        <th>{{ translate('Order Time') }}</th>
                        <th>{{ translate('Pickup Time') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Code') }}</th>
                        <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                        @if (addon_is_activated('refund_request'))
                        <th>{{ translate('Refund') }}</th>
                        @endif
                        <th>{{ translate('Has the loan been released') }}</th>
                        <th>{{ translate('Unfreeze Time') }}</th>

                        <th class="text-right" width="15%">{{translate('options')}}</th>
                    </tr>
                </thead>
                <tbody>
                @php
                $isSupperAdmin = isSupperAdmin();
                @endphp
                    @foreach ($orders as $key => $order)
                    <tr>
                        <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$order->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $order->code }}
                            @if(hget_plus('orders_pick_up_tip', $order->id))
                                <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                            @endif
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
                        @if (isSupperAdmin())<td>{{$order->shop->bloc ? $order->shop->bloc->name : ''}}</td>@endif
                        <td>{{$order->shop->staff ? $order->shop->staff->user->email : ''}}</td>

                        <td>
                            @php
                            $shop = App\Models\User::where('id',$order->seller_id)->first();
                            echo $shop['name'];
                            @endphp
                        </td>
                        <td>
                            {{ count($order->orderDetails) }}
                        </td>
                        <td>
                            @if ($order->user != null)
                            {{ $order->user->name }}
                            @else
                            Guest ({{ $order->guest_id }})
                            @endif
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
                        <td style="width:80px">
                            @if($order->delivery_status == 'cancelled')
                                <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                            @else
                            @if ($order->product_storehouse_total > 0)
                                @if ($order->product_storehouse_status)
                                    <span class="badge badge-inline badge-success">{{translate('Picked Up')}}</span>
                                    @if(hget_plus('orders_pick_up_tip', $order->id))
                                        <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                                    @endif
                                @else
                                    <span class="badge badge-inline badge-danger">{{translate('Unpicked Up')}}</span>
                                @endif
                            @endif
                            @endif
                        </td>
                        <td>{{$order->created_at}}</td>
                        <td>{{$order->pickup_time ? date('Y-m-d H:i:s', $order->pickup_time) : ''}}</td>
                        <td>
                            @php
                                $status = $order->delivery_status;
                                if($order->delivery_status == 'cancelled') {
                                    $status = '<span class="badge badge-inline badge-danger">'.translate('Cancel').'</span>';
                                }

                            @endphp
                            @if($order->delivery_status == 'cancelled')
                                {!! $status !!}
                            @else
                                {{translate(str_replace('_', ' ', $status))}}
                            @endif
                        </td>
                        <td>{{$order->payment_record ? translate(str_replace('_', ' ', $order->payment_record->payment_code)) : ''}}</td>
                        <td>
                            @if ($order->payment_status == 'paid')
                            <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                            @else
                            <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                            @endif
                        </td>
                        @if (addon_is_activated('refund_request'))
                        <td>
                            @if (count($order->refund_requests) > 0)
                            {{ count($order->refund_requests) }} {{ translate('Refund') }}
                            @else
                            {{ translate('No Refund') }}
                            @endif
                        </td>
                        @endif
                        <td>
                            @if ($order->product_storehouse_status && !$order->freeze_expired_at)
                                {{translate('Yes')}}
                            @else
                                {{translate('No')}}
                            @endif
                        </td>
                        <td>{{$order->unfreeze_time ? date('Y-m-d H:i:s', $order->unfreeze_time) : ''}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>

                            @include('backend.sales.btns')
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

//        function change_status() {
//            var data = new FormData($('#order_form')[0]);
//            $.ajax({
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                url: "{{route('bulk-order-status')}}",
//                type: 'POST',
//                data: data,
//                cache: false,
//                contentType: false,
//                processData: false,
//                success: function (response) {
//                    if(response == 1) {
//                        location.reload();
//                    }
//                }
//            });
//        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-order-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

    </script>
@endsection
