@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Role')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('roles.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Role')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_role" action="" method="GET">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Roles')}}</h5>

        @if (isSupperAdmin())
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="bloc_id" id="bloc_id">
                    <option value="">{{translate('Filter by Bloc')}}</option>
                    @foreach(\App\Models\Bloc::all() as $bloc)
                        <option value="{{$bloc->id}}"  @isset($bloc_id) @if($bloc_id == $bloc->id) selected @endif @endisset>{{$bloc->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(isSupperAdmin() || isBlocManage())
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="staff_user_id" id="staff_user_id" data-live-search="true">
                    <option value="">{{translate('Filter by Staff')}}</option>
                    @foreach(filter_by_bloc(\App\Models\User::query()->whereIn('user_type', ['staff', 'admin']))->get() as $user)
                        <option value="{{$user->id}}"  @isset($staff_user_id) @if($staff_user_id == $user->id) selected @endif @endisset>{{$user->name}} ({{$user->email}})</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-3">
            <div class="form-group mb-0">
                <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group mb-0">
                <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Name & hit Enter') }}">
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
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Creator')}}</th>
                    @if(isSupperAdmin())
                        <th>{{translate('Bloc')}}</th>
                    @endif
                    <th>{{translate('Create Time')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $key => $role)
                    <tr>
                        <td>{{ ($key+1) + ($roles->currentPage() - 1)*$roles->perPage() }}</td>
                        <td>{{ $role->getTranslation('name')}}</td>
                        <td>{{ $role->creator->email ?: 'root@qq.com'}}</td>
                        @if(isSupperAdmin())
                            <td>{{ $role->bloc->name ?: '总平台'}}</td>
                        @endif
                        <td>{{ $role->created_at}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('roles.edit', ['id'=>$role->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('roles.destroy', $role->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $roles->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
