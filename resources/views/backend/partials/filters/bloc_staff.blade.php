@if (isSupperAdmin())
    <div class="col-md-2 ml-auto">
        <select class="form-control aiz-selectpicker" name="bloc_id" id="bloc_id" onchange="typeof sort_sellers != 'undefined' && sort_sellers()">
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
