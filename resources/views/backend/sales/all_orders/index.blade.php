@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12"><h5 class="mb-md-0 h6">({{translate('Total')}}: {{$total_customers}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}} {{translate('Pickup Amount')}}:{{single_price($sum_product_storehouse_total)}})</h5></div>
    </div>

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                @include('backend.partials.filters.seller')

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
            </div>
            <div class="card-header row gutters-5">
                @include('backend.sales.filter')
            </div>
            <div class="card-header row gutters-5">
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




