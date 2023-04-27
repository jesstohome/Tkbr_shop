@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
            </div>

            <!-- Change Status Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{translate('Choose an order status')}}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <select class="form-control aiz-selectpicker" onchange="change_status()" data-minimum-results-for-search="Infinity" id="update_delivery_status">
                                <option value="pending">{{translate('Pending')}}</option>
                                <option value="confirmed">{{translate('Confirmed')}}</option>
                                <option value="picked_up">{{translate('Picked Up')}}</option>
                                <option value="on_the_way">{{translate('On The Way')}}</option>
                                <option value="delivered">{{translate('Delivered')}}</option>
                                <option value="cancelled">{{translate('Cancel')}}</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{translate('Pending')}}</option>
                    <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>{{translate('Confirmed')}}</option>
                    <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>{{translate('Picked Up')}}</option>
                    <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>{{translate('On The Way')}}</option>
                    <option value="arrived" @if ($delivery_status == 'arrived') selected @endif>{{ translate('Arrived') }}</option>
                    <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>{{translate('Delivered')}}</option>
                    <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>{{translate('Cancel')}}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="seller_id" name="seller_id" data-live-search="true">
                        <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (App\Models\User::where('user_type', '=', 'seller')->get() as $key => $seller)
                            <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                                {{ $seller->shop->name }} ({{ $seller->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="customer_id" name="customer_id" data-live-search="true">
                        <option value="">{{ translate('All Customers') }}</option>
                        @foreach (App\Models\User::where('user_type', '=', 'customer')->get() as $key => $customer)
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
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <!--<th>#</th>-->
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
                        <th>{{ translate('Shop') }}</th>
                        <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Amount') }}</th>
                        <th data-breakpoints="md">{{ translate('Profit') }}</th>
                        <th data-breakpoints="md">{{ translate('Pick Up Status') }}</th>
                        <th>{{ translate('Pickup Time') }}</th>
                        <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
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
                    @foreach ($orders as $key => $order)
                    <tr>
    <!--                    <td>
                            {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                        </td>-->
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
                        </td>

                        <td>
                            @php
             $shop = App\Models\User::where('id',$order->seller_id)->first();
             echo $shop['email'];
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
                        <td style="width: 80px;">
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
                        </td>
        <td>{{$order->pickup_time ? date('Y-m-d H:i:s', $order->pickup_time) : ''}}</td>
                        <td>
                            @php
             $status = $order->delivery_status;
             if($order->delivery_status == 'cancelled') {
                 $status = '<span class="badge badge-inline badge-danger">'.translate('Cancel').'</span>';
             }

             @endphp
                            {!! $status !!}
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
                        <td>
                            @if ($order->product_storehouse_status && !$order->freeze_expired_at)
                            {{translate('Yes')}}
                            @else
                            {{translate('No')}}
                            @endif
                        </td>
                        @endif

        <td>{{$order->unfreeze_time ? date('Y-m-d H:i:s', $order->unfreeze_time) : ''}}</td>
                        <td class="text-right">
                            @if(count($order->orderDetails) == 1)
                                @if($order->orderDetails[0]->reviewed)
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="product_review_detail('{{ $order->orderDetails[0]->product_id }}', '{{$order->user_id}}', '{{$order->id}}')">
                                        {{translate('Reviewed')}}
                                    </a>
                                @else
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="product_review('{{ $order->orderDetails[0]->product_id }}', '{{$order->user_id}}', '{{$order->id}}')" title="{{ translate('Review') }}">
                                    {{ translate('Review') }}
                                </a>
                                @endif
                            @endif
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                <i class="las la-download"></i>
                            </a>

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

    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">

    </div>

    <div class="modal fade" id="product-review-detail-modal">

    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function product_review(product_id, user_id, order_id) {
            $.post('{{ route('product_review_modal.show') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id,
                user_id: user_id,
                order_id: order_id,
            }, function(data) {
                $('#product-review-modal').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        }

        function product_review_detail(product_id, user_id, order_id) {
            $.post('{{ route('product_review_detail.show') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id,
                user_id: user_id,
                order_id: order_id,
            }, function(data) {
                $('#product-review-detail-modal').html(data);
                $('#product-review-detail-modal').modal('show', {
                    backdrop: 'static'
                });
            });
        }

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
