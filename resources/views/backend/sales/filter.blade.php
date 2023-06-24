@if (isSupperAdmin())
    <div class="col-md-2 ml-auto">
        <select class="form-control aiz-selectpicker" name="bloc_id" id="bloc_id" data-live-search="true">
            <option value="">{{translate('Filter by Bloc')}}</option>
            @foreach(\App\Models\Bloc::all() as $bloc)
                <option value="{{$bloc->id}}"  @isset($bloc_id) @if($bloc_id == $bloc->id) selected @endif @endisset>{{$bloc->name}}</option>
            @endforeach
        </select>
    </div>
@endif

@if(isSupperAdmin() || isBlocManage())
    <div class="col-md-2 ml-auto">
        <select class="form-control aiz-selectpicker" name="staff_id" id="staff_id" data-live-search="true">
            <option value="">{{translate('Filter by Staff')}}</option>
            @foreach(filter_by_bloc(\App\Models\Staff::query())->get() as $staff)
                <option value="{{$staff->id}}"  @isset($staff_id) @if($staff_id == $staff->id) selected @endif @endisset>{{$staff->user->name}}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="col-lg-2 ml-auto">
    <select class="form-control aiz-selectpicker" name="product_storehouse_status" id="product_storehouse_status">
        <option value="">{{translate('All')}}</option>
        <option value="0" @if ($product_storehouse_status != '' && $product_storehouse_status == 0) selected @endif>{{translate('Not picked up')}}</option>
        <option value="1" @if ($product_storehouse_status == 1) selected @endif>{{translate('Picked up')}}</option>
    </select>
</div>

<div class="col-lg-2 ml-auto">
    <select class="form-control aiz-selectpicker" name="freeze_status" id="freeze_status">
        <option value="">{{translate('Has the loan been released')}}</option>
        <option value="0" @if ($freeze_status != '' && $freeze_status == 0) selected @endif>{{translate('No')}}</option>
        <option value="1" @if ($freeze_status == 1) selected @endif>{{translate('Yes')}}</option>

    </select>
</div>
<div class="col-lg-2 ml-auto">
    <input type="text" class="form-control" id="min-price" name="min_price" value="{{ $min_price ?: '' }}" placeholder="最小价格">
    ~
    <input type="text" class="form-control" id="max-price" name="max_price" value="{{ $max_price ?: ''}}" placeholder="最大价格">
</div>
