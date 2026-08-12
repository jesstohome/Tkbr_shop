@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="h3">{{ translate('Wallet Balance Adjustment Records') }}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_wallet_logs" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="type" id="type">
                    <option value="">{{ translate('All Types') }}</option>
                    <option value="admin_recharge" @if($type == 'admin_recharge') selected @endif>{{ translate('Admin Recharge') }}</option>
                    <option value="admin_deduct" @if($type == 'admin_deduct') selected @endif>{{ translate('Admin Deduct') }}</option>
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select name="seller_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach ($sellers as $key => $seller)
                        <option value="{{ $seller->id }}" @if($seller_id == $seller->id) selected @endif>
                            {{ $seller->name }} ({{ $seller->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off" data-advanced-range="true">
                </div>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
            </div>
        </div>

        <div class="card-body" style="overflow-x: auto">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Seller') }}</th>
                    <th data-breakpoints="lg">{{ translate('Amount') }}</th>
                    <th data-breakpoints="lg">{{ translate('Type') }}</th>
                    <th data-breakpoints="lg">{{ translate('Created At') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($walletLogs as $key => $log)
                    <tr>
                        <td>{{ $walletLogs->firstItem() + $key }}</td>
                        <td>{{ $log->user ? $log->user->name . ' (' . $log->user->email . ')' : '' }}</td>
                        <td>{{ single_price($log->amount) }}</td>
                        <td>
                            @if ($log->type == 'admin_recharge')
                                <span class="badge badge-inline badge-success">{{ translate('Admin Recharge') }}</span>
                            @elseif ($log->type == 'admin_deduct')
                                <span class="badge badge-inline badge-danger">{{ translate('Admin Deduct') }}</span>
                            @endif
                        </td>
                        <td>{{ $log->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $walletLogs->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection
