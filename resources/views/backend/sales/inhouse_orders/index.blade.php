@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-12"><h5 class="mb-md-0 h6">({{translate('Total')}}: {{$total_customers}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h5></div>
    </div>
<div class="card">
    <form class="" id="sort_orders" action="" method="GET">
      <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
          <h5 class="mb-md-0 h6">{{ translate('Inhouse Orders') }}</h5>
        </div>
      @include('backend.sales.filter')

      </div>
        <div class="card-header row gutters-5">
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="payment_type" id="payment_type" onchange="sort_orders()">
                    <option value="">{{translate('Filter by Payment Status')}}</option>
                    <option value="paid"  @isset($payment_status) @if($payment_status == 'paid') selected @endif @endisset>{{translate('Paid')}}</option>
                    <option value="unpaid"  @isset($payment_status) @if($payment_status == 'unpaid') selected @endif @endisset>{{translate('Un-Paid')}}</option>
                </select>
            </div>

            <div class="col-lg-3 ml-auto">
                <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status" onchange="sort_orders()">
                    <option value="">{{translate('Filter by Delivery Status')}}</option>
                    <option value="pending" @isset($delivery_status) @if($delivery_status == 'pending') selected @endif @endisset>{{translate('Pending')}}</option>
                    <option value="confirmed"   @isset($delivery_status) @if($delivery_status == 'confirmed') selected @endif @endisset>{{translate('Confirmed')}}</option>
                    <option value="on_delivery"   @isset($delivery_status) @if($delivery_status == 'on_delivery') selected @endif @endisset>{{translate('On delivery')}}</option>
                    <option value="delivered"   @isset($delivery_status) @if($delivery_status == 'delivered') selected @endif @endisset>{{translate('Delivered')}}</option>
                </select>
            </div>
            <div class="col-lg-auto">
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
