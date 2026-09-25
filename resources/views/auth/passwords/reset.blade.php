@extends('frontend.layouts.app')

@section('content')
<div class="py-6">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <div class="bg-white rounded shadow-sm p-4 text-left">
                    <h1 class="h3 fw-600">{{ translate('Reset Password') }}</h1>
                    <p class="mb-4 opacity-60">{{translate('Enter your email address and new password and confirm password.')}} </p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <div class="form-group">
                            <div class="input-group">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ translate('Email') }}" required autofocus>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-primary" id="send_reset_code">{{ translate('Send Code') }}</button>
                                </div>
                            </div>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ $code ?? old('code') }}" placeholder="{{translate('Code')}}" required autofocus>

                            @if ($errors->has('code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('code') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ translate('New Password') }}" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="{{ translate('Confirm Password') }}" required>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-block">
                                {{ translate('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        // 发送重置密码验证码
        var sendCodeTimer = null;
        $('#send_reset_code').on('click', function () {
            var btn = $(this);
            if (btn.hasClass('disabled')) return;

            var email = $("input[name=email]").val().trim();
            if (email === '') {
                AIZ.plugins.notify('danger', '{{translate('The email is required and cannot be empty')}}');
                return;
            }

            $.ajax({
                headers: { 'X-CSRF-TOKEN': AIZ.data.csrf },
                method: 'POST',
                url: '{{route('password.reset.send_code')}}',
                data: { email: email },
                success: function (res) {
                    if (res.success) {
                        AIZ.plugins.notify('success', res.msg);
                        var seconds = 60;
                        btn.addClass('disabled').text(seconds + 's');
                        sendCodeTimer = setInterval(function () {
                            seconds--;
                            if (seconds <= 0) {
                                clearInterval(sendCodeTimer);
                                btn.removeClass('disabled').text('{{translate('Send Code')}}');
                            } else {
                                btn.text(seconds + 's');
                            }
                        }, 1000);
                    } else {
                        AIZ.plugins.notify('danger', res.msg);
                    }
                },
                error: function () {
                    AIZ.plugins.notify('danger', '{{translate('Request failed, please try again')}}');
                }
            });
        });
    });
</script>
@endsection
