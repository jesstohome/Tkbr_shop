@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
</div>

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill border-light">
      				@foreach (\App\Models\Language::all() as $key => $language)
      					<li class="nav-item">
      						<a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('roles.edit', ['id'=>$role->id, 'lang'=> $language->code] ) }}">
      							<img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
      							<span>{{$language->name}}</span>
      						</a>
      					</li>
    	            @endforeach
      			</ul>
            <form id="role-form" class="p-4" action="{{ route('roles.update', $role->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                <input type="hidden" name="menu_ids" value="">
                <input type="hidden" name="lang" value="{{ $lang }}">
            	   @csrf
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $role->getTranslation('name', $lang) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="is_manage">{{translate('Is Manager')}}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" value="1" name="is_manage" @if($role->is_manage) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>

                <div class="form-group row">
                    <label class="col-md-2 col-from-label" for="banner"></label>
                    <div class="col-md-8">
                        <div id="jstree_demo_div"></div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
    </div>
</div>
</div>

@endsection
@section('script')
    <link rel="stylesheet" href="{{ static_asset('assets/jstree/themes/default/style.min.css') }}" />
    <script src="{{ static_asset('assets/jstree/jstree.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $('#jstree_demo_div').jstree({
                "plugins" : [ "wholerow", "checkbox" ],
                "checkbox" : {
                    "keep_selected_style" : false
                },
                'core' : {
                    'data' : JSON.parse("{{\App\Models\Menu::getMenuJsTreeJson($role->id)}}".replace(/&quot;/g, '"'))
                }
            });

            $('#jstree_demo_div').on('changed.jstree', function (e, data) {
                let menu_ids = (data.selected || []).concat($(this).jstree('get_undetermined') || []);
                $("input[name=menu_ids]").val(menu_ids)
            });
        });
    </script>
@endsection
