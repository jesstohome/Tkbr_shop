@extends('backend.layouts.app')

@section('content')

<div class="card">
{{--    <div class="card-header">--}}
        <form action="" class="card-header">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('Offline Wallet Recharge Requests')}}</h5>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="approval_status" id="approval_status">
                    <option value="">{{translate('Filter by Status')}}</option>
                    <option value="pass" @if ($approval_status === 'pass') selected @endif>{{translate('Pass')}}</option>
                    <option value="nopass" @if ($approval_status === 'nopass') selected @endif>{{translate('No Pass')}}</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="name" name="name" @isset($name) value="{{ $name }}" @endisset placeholder="{{ translate('name') }}" onkeyup="filterProducts()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="operator" name="operator" @isset($operator) value="{{ $operator}}" @endisset placeholder="{{ translate('operator') }}" onkeyup="filterProducts()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $date }}" name="date" placeholder="{{ translate('Filter by date') }}" data-format="Y-MM-DD" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-styled">{{ translate('Search') }}</button>
        </form>
{{--    </div>--}}

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Operator')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{translate('Method')}}</th>
                    <th>{{translate('TXN ID')}}</th>
                    <th>{{translate('Photo')}}</th>
                    <th>{{translate('Examine')}}</th>
                    <th>{{translate('Status')}}</th>
                    <th>{{translate('Type')}}</th>
                    <th>{{translate('Date')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wallets as $key => $wallet)
                    @if ($wallet->user != null)
                        <tr>
                            <td>{{ ($key+1) }}</td>
                            <td>{{ $wallet->user->name }}</td>
                            <td>{{ $wallet->operator->user_type=='admin'?'admin':$wallet->operator->name }}</td>
                            <td>{{ $wallet->amount }}</td>
                            <td>{{ $wallet->payment_method }}</td>
                            <td>{{ $wallet->payment_details }}</td>
                            <td>
                                @if ($wallet->reciept != null)
                                    <a href="{{ uploaded_asset($wallet->reciept) }}" target="_blank">{{translate('Open Reciept')}}</a>
                                @endif
                            </td>
                            <td>
                                @if(!$wallet->approval)
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="wallet_review('{{ $wallet->id }}')" title="{{ translate('Examine') }}">
                                    {{translate('Examine')}}
                                    @if(hget_plus('new_offline_pickup_pay_tip', $wallet->id))
                                        <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                                    @endif
                                </a>
                                @else
                                    <span class="badge badge-inline badge-success">{{ translate('Audited') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($wallet->approval == 1)
                                    <span class="badge badge-inline badge-success">{{ translate('Pass') }}</span>
                                @elseif($wallet->approval == 2)
                                    <span class="badge badge-inline badge-danger">{{ translate('No Pass') }}</span>
                                @else
                                    <span class="badge badge-inline badge-warning">{{ translate('Unaudited') }}</span>
                                @endif
                            </td>
                            <td>
                                @if( $wallet->type == 1 )
                                {{ translate('Balance Recharge')}}
                                @elseif($wallet->type == 3)
                                    {{translate('Pick Up')}}
                                @else
                                {{ translate('Guarantee Recharge')}}
                            @endif
                            </td>
                            <td>{{ $wallet->created_at }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $wallets->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection


@section('modal')
    <!-- review Modal -->
    <div id="review-modal" class="modal fade">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{translate('Review Confirmation')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">{{translate('Are you sure to Pass this?')}}</p>
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('Pass')}}</label>
                        <div class="col-md-6">
                            <label class="mb-0">
                                <input type="radio" name="status" value="1">
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-6 col-from-label">{{translate('No Pass')}}</label>
                        <div class="col-md-6">
                            <label class="mb-0">
                                <input type="radio" name="status" value="0">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a href="" id="save-link" class="btn btn-primary mt-2">{{translate('Save')}}</a>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->

@endsection

@section('script')
    <script type="text/javascript">
        function update_approved() {
            let status = $("input[name=status]:checked").val()
            $.post('{{ route('offline_recharge_request.approved') }}', {_token:'{{ csrf_token() }}', id:wallet_id, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        var wallet_id = 0;

        function wallet_review(id) {
            wallet_id = id;
            $("#review-modal").modal("show")
        }
        $("#save-link").on("click", function () {
            update_approved()
        })
    </script>
@endsection
