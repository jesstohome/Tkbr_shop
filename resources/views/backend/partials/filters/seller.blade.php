@php
    $sellers = filter_by_bloc(App\Models\User::where('user_type', '=', 'seller'))->join("shops", "shops.user_id", '=', 'users.id')->select("users.*", "shops.name as shop_name")->get();
@endphp

<div class="col-lg-2">
    <div class="form-group mb-0">
        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="seller_id" name="seller_id" data-live-search="true">
            <option value="">{{ translate('All Sellers') }}</option>
            @foreach ($sellers as $key => $seller)
                <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                    {{ $seller->shop_name }} ({{ $seller->email }}) ({{$seller->name}})
                </option>
            @endforeach
        </select>
    </div>
</div>
