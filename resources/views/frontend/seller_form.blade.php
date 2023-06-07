@extends('frontend.layouts.app')

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Register your shop')}}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="{{ route('shops.create') }}">"{{ translate('Register your shop')}}"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="pt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <form id="shop" class="" action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="invitation_code" value="{{$invitation_code}}"/>
                    @csrf
                    @if (!Auth::check())
                        <div class="bg-white rounded shadow-sm mb-3">
                            <div class="fs-15 fw-600 p-3 border-bottom">
                                {{ translate('Personal Info')}}
                            </div>
                            <div class="p-3">
                                <div class="form-group">
                                    <label>{{ translate('Your Name')}} <span class="text-primary">*</span></label>
                                    <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" placeholder="{{  translate('Name') }}" name="name">
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Email')}} <span class="text-primary">*</span></label>
                                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Your Password')}} <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  translate('Password') }}" name="password" data-bv-notempty-message="{{translate('The password is required and cannot be empty')}}">
                                </div>
                                <div class="form-group">
                                    <label>{{ translate('Repeat Password')}} <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control" placeholder="{{  translate('Confirm Password') }}" name="password_confirmation" data-bv-notempty-message="{{translate('The confirm password is required and cannot be empty')}}">
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="bg-white rounded shadow-sm mb-4">
                        <div class="fs-15 fw-600 p-3 border-bottom">
                            {{ translate('Basic Info')}}
                        </div>
                        <div class="p-3">
                            <div class="form-group">
                                <label>{{ translate('Shop Name')}} <span class="text-primary">*</span></label>
                                <input type="text" class="form-control" placeholder="{{ translate('Shop Name')}}" name="shop_name" data-bv-notempty-message="{{translate('The shop name is required and cannot be empty')}}">
                            </div>

                            @if(empty($invitation_code))
                            <div class="form-group">
                                <label>{{ translate('Invite code')}} <span class="text-primary">*</span></label>
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Invite code')}}" name="staff_invite_code" @if(!empty($staff_invitation_code)) readonly @endif value="{{$staff_invitation_code ?? ''}}">
                            </div>
                            @endif

                            @if(!empty(get_setting('seller_reg_id_card_switch')))
                             <div class="form-group">
                                <label>{{ translate('Certificates Type')}} <span class="text-primary">*</span></label>
                                 <select class="form-control" name="certtype">
                                     <option value="idcard"> {{translate('id card')}}</option>
                                     <option value="passport"> {{translate('passport')}}</option>
                                     <option value="driving license"> {{translate('driving license')}}</option>
                                     <option value="social security card"> {{translate('Social Security Card')}}</option>
                                 </select>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('Certificates Front')}} <span class="text-primary">*</span></label>
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="identity_card_front" value="" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{{ translate('Certificates Back')}} <span class="text-primary">*</span></label>

                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="identity_card_back" value="" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>

                    @if(get_setting('google_recaptcha') == 1)
                        <div class="form-group mt-2 mx-auto row">
                            <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                        </div>
                    @endif

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary fw-600">{{ translate('Register Your Shop')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
    <link src="{{ static_asset('assets/css/bootstrapValidator.min.css') }}"/>
    <script src="{{ static_asset('assets/js/bootstrapValidator.min.js') }}" ></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script type="text/javascript">
    // making the CAPTCHA  a required field for form submission
    $(document).ready(function(){
        $("#shop").on("submit", function(evt)
        {
            try {
                var response = grecaptcha.getResponse();
                if(response.length == 0)
                {
                    //reCaptcha not verified
                    alert("please verify you are humann!");
                    evt.preventDefault();
                    return false;
                }
            } catch {}

            if ($("input[name=name]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The user name is required and cannot be empty')}}');
                return false;
            }
            if ($("input[name=shop_name]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The shop name is required and cannot be empty')}}');
                return false;
            }

            @if(empty($invitation_code))
            if (($("input[name=staff_invite_code]").val() || '').trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The invite code is required and cannot be empty')}}');
                return false;
            }
            @endif

            if ($("input[name=email]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The email is required and cannot be empty')}}');
                return false;
            }
            if ($("input[name=password]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The password is required and cannot be empty')}}');
                return false;
            }
            if ($("input[name=password_confirmation]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The confirm password is required and cannot be empty')}}');
                return false;
            }
            if ($("input[name=password_confirmation]").val().trim() === '') {
                AIZ.plugins.notify('danger', '{{translate('The confirm password is required and cannot be empty')}}');
                return false;
            }

            // 校验两张图片
            @if(!empty(get_setting('seller_reg_id_card_switch')))
            if ($("input[name=identity_card_front]").val() == '') {
                AIZ.plugins.notify('danger', '{{ translate('Identity Card Front Not Allow Empty!') }}');
                return false;
            }
            if ($("input[name=identity_card_back]").val() == '') {
                AIZ.plugins.notify('danger', '{{ translate('Identity Card Back Not Allow Empty!') }}');
                return false;
            }
            @endif

            //captcha verified
            //do the rest of your validations here
            var data = new FormData($('#shop')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': AIZ.data.csrf
                },
                method: "POST",
                url: "{{route('shops.store')}}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    if (data.success) {
                        location.href = '{{route('shops.index')}}';
                    } else {
                        AIZ.plugins.notify('danger', data.msg ||  '');
                    }
                }
            });

            return false;
        });
    });
</script>
@endsection
