<style type="text/css">
    .card-body {
        overflow-x: auto;
    }
</style>

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
            <th data-breakpoints="md">{{ translate('Pickup Amount') }}</th>
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
                                <input type="checkbox" class="check-one item-row-id" name="id[]" value="{{$order->id}}">
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
                    {{ single_price($order->product_storehouse_total) }}
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

    <div class="aiz-pagination" style="display: flex">
        {{ $orders->appends(request()->input())->links() }}

        @if($orders->lastPage() > 1)
            <select name="perPage" class="form-control" style="flex: 0.1" onchange="changeFormPerPage(this)">
                @foreach([15, 50, 100, 200] as $page_num)
                    <option value="{{$page_num}}" {{$perPage == $page_num ? 'selected' : ''}}>{{$page_num}}</option>
                @endforeach
            </select>
        @endif
    </div>

</div>
