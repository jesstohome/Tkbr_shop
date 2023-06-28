@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12"><h5 class="mb-md-0 h6">({{translate('Total')}}: {{$total_customers}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h5></div>
    </div>
<div class="card">
    <form class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Seller Orders') }}</h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>

            @include('backend.partials.filters.seller')

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
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            @include('backend.sales.filter')
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th width="20%">{{translate('Order Code')}}</th>
                    @if (isSupperAdmin())<th>{{ translate('Bloc') }}</th>@endif
                    <th>{{ translate('Staffs') }}</th>
                    <th width="20%">{{translate('Shop')}}</th>
                    <th data-breakpoints="lg">{{translate('Num. of Products')}}</th>
                    <th data-breakpoints="lg">{{translate('Customer')}}</th>
                    <th>{{translate('Seller')}}</th>
                    <th data-breakpoints="lg">{{translate('Amount')}}</th>
                    <th data-breakpoints="md">{{ translate('Profit') }}</th>
                    <th data-breakpoints="md">{{ translate('Pick Up Status') }}</th>
                    <th data-breakpoints="lg">{{translate('Delivery Status')}}</th>
                    <th data-breakpoints="lg">{{translate('Payment Method')}}</th>
                    <th data-breakpoints="lg">{{translate('Payment Status')}}</th>
                    @if (addon_is_activated('refund_request'))
                        <th>{{translate('Refund')}}</th>
                    @endif
                    <th class="text-right" width="15%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                    <tr>
                        <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>
                        <td>
                            {{ $order->code }}@if($order->viewed == 0) <span class="badge badge-inline badge-info">{{translate('New')}}</span>@endif
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
                            {{ count($order->orderDetails->where('seller_id', '!=', $admin_user_id)) }}
                        </td>
                        <td>
                            @if ($order->user != null)
                                {{ $order->user->name }}
                            @else
                                Guest ({{ $order->guest_id }})
                            @endif
                        </td>
                        <td>
                            @if($order->shop)
                                {{ $order->shop->name }}
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
                        <td>
                            @if ($order->product_storehouse_status)
                                <span class="badge badge-inline badge-success">{{translate('Picked Up')}}</span>
                            @else
                                <span class="badge badge-inline badge-danger">{{translate('Unpicked Up')}}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $order->delivery_status;
                            @endphp
                            {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                        </td>
                        <td>
                            {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                        </td>
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

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('seller_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
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
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
@endsection
