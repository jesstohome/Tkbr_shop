@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <input type="hidden" name="menu_ids" value="">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>
                <div class="form-group row">
                    <label class="col-md-2 col-from-label"></label>
                    <div class="col-md-8">
                        <div id="jstree_demo_div"></div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </from>
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
