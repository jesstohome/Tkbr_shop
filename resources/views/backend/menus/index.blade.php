@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Menus')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('menu.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Menu')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Menus')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg" width="10%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Route')}}</th>
                    <th data-breakpoints="lg">{{translate('Icon')}}</th>
                    <th data-breakpoints="lg">{{translate('Red Dot Keys')}}</th>
                    <th data-breakpoints="lg">{{translate('Addon Name')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menus as $key => $menu)
                    <tr>
                        <td>{{ ($key+1) + ($menus->currentPage() - 1)*$menus->perPage() }}</td>
                        <td>{{translate($menu->name)}}</td>
                        <td>{{$menu->route}}</td>
                        <td>
                            @if($menu->icon)
                                <i class="las la-{{$menu->icon}} aiz-side-nav-icon"></i> {{$menu->icon}}
                            @endif
                        </td>
                        <td>{{$menu->red_dot_keys}}</td>
                        <td>{{$menu->addon_name}}</td>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('menu.edit', $menu->id)}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('menu.destroy', $menu->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $menus->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
