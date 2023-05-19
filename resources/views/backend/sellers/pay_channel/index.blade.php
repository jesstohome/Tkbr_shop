@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All Bloc')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('pay_channel.create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Add New Bloc')}}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Blocs')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table">
                <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Bloc Name')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($pay_channels as $key => $pay_channel)
                    <tr>
                        <td>{{ ($key+1) + ($pay_channels->currentPage() - 1)*$pay_channels->perPage() }}</td>
                        <td>{{ $pay_channel->name}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('pay_channel.edit', ['id'=>$pay_channel->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @if($pay_channel->status != 0)
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="confirm_ban('{{route('pay_channel.ban', encrypt($pay_channel->id))}}');" title="{{ translate('Ban this Bloc') }}">
                                    <i class="las la-user-slash"></i>
                                </a>
                            @else
                                <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="confirm_unban('{{route('pay_channel.ban', encrypt($pay_channel->id))}}');" title="{{ translate('Unban this Bloc') }}">
                                    <i class="las la-user-check"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $pay_channels->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

@endsection

@section('modal')
    <div class="modal fade" id="confirm-ban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you really want to ban this Bloc?')}}</p>
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
                    <p>{{translate('Do you really want to unban this Bloc?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    @include('modals.delete_modal')
@endsection


@section('script')
    <script>
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
