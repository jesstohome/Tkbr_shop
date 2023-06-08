@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" id="sort_support" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Support Desk') }}</h5>
            </div>
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="seller_id" id="seller_id" data-live-search="true" onchange="sort_support()">
                    <option value="">{{translate('All')}}</option>
                    @foreach(filter_by_bloc(\App\Models\User::query()->where('user_type', 'seller'))->get() as $seller)
                    <option value="{{$seller->id}}"  @if($seller_id == $seller->id) selected @endif>{{$seller->email}} ({{$seller->shop->name}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="pay_status" id="pay_status" onchange="sort_support()">
                    <option value="">{{translate('All')}}</option>
                    <option value="paid"  @if($pay_status == 'paid') selected @endif>{{translate('Buyer has paid')}}</option>
                    <option value="unpaid"  @if($pay_status == 'unpaid') selected @endif>{{translate('Unpaid')}}</option>
                </select>
            </div>
            <div class="col-md-2 ml-auto">
                <div class="col-sm-12">
                    <input type="text" class="form-control aiz-date-range" name="created_at" placeholder="创建时间" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-separator=" to " autocomplete="off">
                </div>
            </div>
            <div class="col-md-2 ml-auto">
                <div class="col-sm-12">
                    <input type="text" class="form-control aiz-date-range" name="updated_at" placeholder="回复时间" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-separator=" to " autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="order_no" name="order_no" @isset($order_no) value="{{ $order_no }}" @endisset placeholder="{{ translate('Type Order code & Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <table class="aiz-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th data-breakpoints="lg">{{ translate('Order No') }}</th>
                    <th data-breakpoints="lg">{{ translate('Shop') }}</th>
                    <th data-breakpoints="lg">{{ translate('Email') }}</th>
                    <th data-breakpoints="lg">{{ translate('Amount') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pay Status') }}</th>
                    <th data-breakpoints="lg">{{ translate('Create Time') }}</th>
                    <th data-breakpoints="lg">{{ translate('Latest Reply Time') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($tickets as $key => $ticket)
                    @if ($ticket->user != null)
                        <tr>
                            <td>{{$ticket->order->code ?? ''}}</td>
                            <td>{{$ticket->user->shop->name ?? ''}}</td>
                            <td>{{ $ticket->user->email }}</td>
                            <td>{{single_price($ticket->order->product_storehouse_total)}}</td>
                            <td>
                                @if ($ticket->order->payment_status == 'paid')
                                    <span class="badge badge-inline badge-success">{{ translate('Buyer has paid')}}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{ translate('Unpaid')}}</span>
                                @endif
                            </td>
                            <td>{{$ticket->created_at}}</td>
                            <td>{{$ticket->updated_at}}@if($ticket->viewed == 0) <span class="badge badge-inline badge-info">{{ translate('New') }}</span> @endif</td>
                            <td class="text-right">
                                <a href="{{route('support_ticket.admin_show', encrypt($ticket->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $tickets->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
    $(document).ready(function () {

    });

    function sort_support(el){
        $('#sort_support').submit();
    }
    </script>
@endsection
