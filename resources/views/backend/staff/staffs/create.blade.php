@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Staff Information')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('staffs.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{translate('Email')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Email')}}" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="mobile">{{translate('Phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Phone')}}" id="mobile" name="mobile" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">{{translate('Password')}}</label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="{{translate('Password')}}" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Bloc')}}</label>
                        <div class="col-sm-9">
                            <select name="bloc_id" required class="form-control aiz-selectpicker" >
                                <option value="" {{'admin' != Auth::user()->user_type ? 'disabled' : ''}}></option>
                                @foreach($blocs as $bloc)
                                    <option value="{{$bloc->id}}" {{$bloc->id == Auth::user()->bloc_id ? 'selected' : ''}} {{'admin' != Auth::user()->user_type ? 'disabled' : ''}}>{{$bloc->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="google2fa_secret">{{translate('Google 2FA Secret')}}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="google2fa_secret" name="google2fa_secret" readonly>
                            <small class="text-muted">{{ translate('Please save the Google Authenticator secret key') }}</small>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block" onclick="generateGoogleSecret()">
                                <i class="las la-sync"></i> {{ translate('Generate') }}
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Group identity')}}</label>
                        <div class="col-sm-9">
                            <select name="role_id" required class="form-control aiz-selectpicker">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->getTranslation('name')}}</option>
                                @endforeach
                            </select>
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

@section('script')
<script>
    function generateGoogleSecret() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        var secret = '';
        for (var i = 0; i < 16; i++) {
            secret += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        $('#google2fa_secret').val(secret);
    }
</script>
@endsection
