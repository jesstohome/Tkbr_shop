@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Menu Information')}}</h5>
            </div>

            <form action="{{ route('menu.update', $menu->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="pid">{{translate('Parent Menu')}}</label>
                        <div class="col-sm-9">
                            <select name="pid" required class="form-control aiz-selectpicker">
                                <option value="0"></option>
                                @foreach($menus as $_menu)
                                    <option value="{{$_menu->id}}" @if($_menu->id == $menu->pid) selected @endif>{{translate($_menu->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Display Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control" value="{{translate($menu->name)}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}(EN)({{translate($menu->name)}})</label>
                        <div class="col-sm-9">
                            <input type="text" id="name" name="name" class="form-control" value="{{$menu->name}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="route">{{translate('Route')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="route" name="route" class="form-control" value="{{$menu->route}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="active_routes">{{translate('Active Routes')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="active_routes" name="active_routes" class="form-control" value="{{$menu->active_routes}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="icon">{{translate('Icon')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="icon" name="icon" class="form-control" value="{{$menu->icon}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="red_dot_keys">{{translate('Red Dot Keys')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="red_dot_keys" name="red_dot_keys" class="form-control" value="{{$menu->red_dot_keys}}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="addon_name">{{translate('Addon Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="addon_name" name="addon_name" class="form-control" value="{{$menu->addon_name}}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="setting_name">{{translate('Setting Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="setting_name" name="setting_name" class="form-control" value="{{$menu->setting_name}}">
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
