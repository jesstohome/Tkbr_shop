@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Menu Information')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="pid">{{translate('Parent Menu')}}</label>
                        <div class="col-sm-9">
                            <select name="pid" required class="form-control aiz-selectpicker">
                                <option value="0"></option>
                                @foreach($menus as $menu)
                                    <option value="{{$menu->id}}">{{translate($menu->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}(EN)</label>
                        <div class="col-sm-9">
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="route">{{translate('Route')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="route" name="route" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="active_routes">{{translate('Active Routes')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="active_routes" name="active_routes" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="icon">{{translate('Icon')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="icon" name="icon" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="red_dot_keys">{{translate('Red Dot Keys')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="red_dot_keys" name="red_dot_keys" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="addon_name">{{translate('Addon Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="addon_name" name="addon_name" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="setting_name">{{translate('Setting Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="setting_name" name="setting_name" class="form-control">
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
