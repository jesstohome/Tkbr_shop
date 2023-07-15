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
            @include('backend.sales.filter')

        </div>
        <div class="card-header row gutters-5">
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>

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

            @include('backend.partials.filters.seller')
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
                </div>
            </div>

        </div>

    </form>

    @include("backend.sales.order-table")
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
    @include("backend.sales.script")
@endsection
