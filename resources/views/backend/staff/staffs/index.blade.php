@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Staffs')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('staffs.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Staffs')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_staff" action="" method="GET">
    <div class="card-header row gutters-5">
        <h5 class="mb-0 h6">{{translate('Staffs')}}</h5>
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
            <div class="col-md-4 ml-auto">
                <select class="form-control aiz-selectpicker" name="staff_id" id="staff_id" data-live-search="true">
                    <option value="">{{translate('Filter by Staff')}}</option>
                    @foreach(filter_by_bloc(\App\Models\Staff::query())->get() as $staff)
                        <option value="{{$staff->id}}"  @isset($staff_id) @if($staff_id == $staff->id) selected @endif @endisset>{{$staff->user->name}} ({{$staff->user->email}})</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-md-3">
            <div class="form-group mb-0">
                <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off" data-advanced-range="true">
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
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg" width="10%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Email')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{translate('Bloc')}}</th>
                    <th data-breakpoints="lg">{{translate('Role')}}</th>
                    <th data-breakpoints="lg">{{translate('Invite code')}}</th>
                    <th data-breakpoints="lg">{{translate('Creation time')}}</th>
                    <th width="15%" class="text-center">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $key => $staff)
                    @if($staff->user != null)
                        <tr>
                            <td>{{ ($key+1) + ($staffs->currentPage() - 1)*$staffs->perPage() }}</td>
                            <td>{{$staff->user->name}}</td>
                            <td>{{$staff->user->email}}</td>
                            <td>{{$staff->user->phone}}</td>
                            <td>{{$staff->bloc->name}}</td>
                            <td>
								@if ($staff->role != null)
									{{ $staff->role->getTranslation('name') }}
								@endif
							</td>
                            <td>{{$staff->invite_code}}</td>
                            <td>{{$staff->created_at}}</td>
                            <td class="text-right">
		                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('staffs.edit', encrypt($staff->id))}}" title="{{ translate('Edit') }}">
		                                <i class="las la-edit"></i>
		                            </a>
                                @if($staff->user->banned != 1)
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="confirm_ban('{{route('customers.ban', encrypt($staff->user->id))}}');" title="{{ translate('Ban this Customer') }}">
                                        <i class="las la-user-slash"></i>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="confirm_unban('{{route('customers.ban', encrypt($staff->user->id))}}');" title="{{ translate('Unban this Customer') }}">
                                        <i class="las la-user-check"></i>
                                    </a>
                                @endif

                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('staffs.destroy', $staff->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $staffs->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to unban this Customer?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script type="text/javascript">
    function confirm_ban(url) {
        $( '#confirm-ban' ).modal( 'show', { backdrop: 'static' } );
        document.getElementById( 'confirmation' ).setAttribute( 'href', url );
    }

    function confirm_unban(url) {
        $( '#confirm-unban' ).modal( 'show', { backdrop: 'static' } );
        document.getElementById( 'confirmationunban' ).setAttribute( 'href', url );
    }
</script>
@endsection
