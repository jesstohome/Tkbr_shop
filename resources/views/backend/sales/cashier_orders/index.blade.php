@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
                <h1 class="h3">{{translate('All Orders')}} ({{translate('Total')}}: {{$total_customers}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h1>
            </div>
            <div class="col text-right"></div>
        </div>
    </div>

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" data-time-picker="true" data-format="YYYY-MM-DD HH:mm:ss" id="search1" name="order_time_range" @isset($order_time_range) value="{{ $order_time_range }}" @endisset placeholder="{{ translate('Order Time') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" data-time-picker="true" data-format="YYYY-MM-DD HH:mm:ss"  id="search2" name="pickup_time_range" @isset($pickup_time_range) value="{{ $pickup_time_range }}" @endisset placeholder="{{ translate('Pickup Time') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" data-time-picker="true" data-format="YYYY-MM-DD HH:mm:ss"  id="search3" name="freeze_time_range" @isset($freeze_time_range) value="{{ $freeze_time_range }}" @endisset placeholder="{{ translate('Freeze Time') }}">
                </div>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                    <option value="">{{translate('Mailing Status')}}</option>
                    @foreach(get_express_status()  as $express_status => $express_text)
                    <option value="{{$express_status}}" @if ($delivery_status == $express_status) selected @endif>{{$express_text}}</option>
                    @endforeach
                </select>
            </div>

            @include('backend.partials.filters.seller')
            @include('backend.partials.filters.payment_code')
        </div>
        <div class="card-header row gutters-5">
            @include('backend.sales.filter')
        </div>
        <div class="card-header row gutters-5" style="justify-content: flex-start;">
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
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
                </div>
            </div>
        </div>

        @include("backend.sales.order-table")
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
    @include('backend.sales.script')
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
    @include("backend.sales.script")
@endsection
