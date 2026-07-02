@extends('backend.layouts.app')

@section('content')

    <div class="col-lg-6  mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Profile')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('profile.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                	@csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="{{translate('Name')}}" name="name" value="{{ Auth::user()->name }}" readonly required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Email')}}</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" placeholder="{{translate('Email')}}" name="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="new_password">{{translate('New Password')}}</label>
                        <div class="col-sm-9" style="display: flex">
                            <input type="password" class="form-control" placeholder="{{translate('New Password')}}" name="new_password">
                            <div class="u-success u-eye"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="confirm_password">{{translate('Confirm Password')}}</label>
                        <div class="col-sm-9" style="display: flex">
                            <input type="password" class="form-control" placeholder="{{translate('Confirm Password')}}" name="confirm_password">
                            <div class="u-success u-eye"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="google2fa_secret">{{translate('Google 2FA Secret')}}</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" id="google2fa_secret" value="{{ Auth::user()->google2fa_secret }}" readonly>
                            <small class="text-muted">{{ translate('Please save this secret key in your Google Authenticator app') }}</small>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-block" onclick="refreshGoogleSecret()" title="{{ translate('Refresh Secret') }}">
                                <i class="las la-sync"></i> {{ translate('Refresh') }}
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Avatar')}} <small>(90x90)</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="avatar" class="selected-files" value="{{ Auth::user()->avatar_original }}">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="invite_code">{{translate('My Invite code')}}</label>
                        <div class="col-sm-9">
                            <input type="text" id="invite_code" value="{{ Auth::user()->staffInfo->invite_code }}" class="form-control" readonly required>
                        </div>
                    </div>

                    @if(!empty(Auth::user()->staffInfo->invite_code))
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="invite_code">{{translate('My Invite Url')}}</label>
                            <div class="col-sm-9">
                                {{route('shops.create', ['staff_invitation_code' => Auth::user()->staffInfo->invite_code])}}
                            </div>
                        </div>
                    @endif

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    function refreshGoogleSecret() {
        $.ajax({
            type: "POST",
            url: "{{ route('profile.refresh_google_secret') }}",
            data: { _token: AIZ.data.csrf }
        }).done(function(res) {
            $('#google2fa_secret').val(res.secret);
            AIZ.plugins.notify('success', '{{ translate('Secret key refreshed, please re-bind your Google Authenticator') }}');
        });
    }
</script>
@endsection
