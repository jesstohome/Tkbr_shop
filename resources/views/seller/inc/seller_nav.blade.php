<style type="text/css">
    .seller-header-nav .btn-icon {
        width: 40px !important;
        min-width: 40px;
        height: 40px;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 767.98px) {
        .seller-header-nav .btn-icon {
            width: 36px !important;
            min-width: 36px;
            height: 36px;
        }
    }
</style>

<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between seller-header-nav">
    <div class="d-flex align-items-center">
        <div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3 ml-0" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
        <div class="d-none d-md-flex align-items-center ml-2">
            <span class="text-muted" style="font-size:13px;">{{ translate('Seller Dashboard') }}</span>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-stretch flex-grow-xl-1">
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            @php
                $customer_service_link = '';
                if (Auth::user()->parent_user){
                   if (Auth::user()->parent_user->customer_service_link){
                       $customer_service_link = Auth::user()->parent_user->customer_service_link;
                   }
                }
            @endphp
            @if ($customer_service_link)
                <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                    <div class="aiz-topbar-item">
                        <div class="d-flex align-items-center">
                            <span onclick="Jump()" class="btn btn-sm btn-soft-primary customer_service_link" title="{{ translate('Customer service link') }}">
                                <i class="las la-headset mr-1"></i> {{ translate('Customer service')}}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            {{-- Mute Toggle --}}
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex">
                    @if(get_admin_setting('msg_tip_mute'))
                        <a class="d-flex align-items-center" href="javascript:void(0);" role="button" onclick="toggle_mute(0)" title="{{ translate('Unmute notifications') }}">
                            <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                                <span class="d-flex align-items-center position-relative">
                                    <svg t="1682401718540" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2743" width="18" height="18"><path d="M736 512c0-43.56-11.28-83.22-33.83-119-22.56-35.78-52.5-63-89.83-81.67V421l121.33 121.33C735.22 536.11 736 526 736 512z m123.67 0c0 45.11-8.56 88.66-25.67 130.67l74.67 77C942.89 654.33 960 585.89 960 514.33S944.83 376.66 914.5 316c-30.33-60.67-71.95-112-124.84-154s-112-70.78-177.33-86.33v102.66c71.55 21.78 130.67 63.39 177.33 124.84 46.67 61.44 70.01 131.05 70.01 208.83zM127 64l-63 63 235.67 235.67H64v298.67h198.33L512 911V575l212.33 212.33c-37.33 28-74.67 47.44-112 58.33v102.66c66.89-15.55 127.55-45.89 182-91L897 960l63-63-448-448L127 64z m385 49L407 218l105 105V113z" p-id="2744"></path></svg>
                                </span>
                            </span>
                        </a>
                    @else
                        <a class="d-flex align-items-center" href="javascript:void(0);" role="button" onclick="toggle_mute(1)" title="{{ translate('Mute notifications') }}">
                            <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                                <span class="d-flex align-items-center position-relative">
                                    <svg t="1682401770513" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3817" width="18" height="18"><path d="M576 701.6v-65.6c55.2-14.4 96-64 96-124s-40.8-109.6-96-124v-65.6C666.4 337.6 736 416.8 736 512s-69.6 174.4-160 189.6z m0-568v64.8c145.6 29.6 256 159.2 256 313.6 0 154.4-110.4 284-256 313.6v64.8c181.6-30.4 320-188 320-378.4S757.6 164 576 133.6zM256 384H128v256h128l256 256V128L256 384z" p-id="3818"></path></svg>
                                </span>
                            </span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Notifications --}}
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                            <span class="d-flex align-items-center position-relative">
                                <i class="las la-bell fs-22"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="badge badge-sm badge-dot badge-circle badge-danger position-absolute absolute-top-right"></span>
                                @endif
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-lg py-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                        </div>
                        <div class="px-3 c-scrollbar-light overflow-auto" style="max-height:300px;">
                            <ul class="list-group list-group-flush">
                                @forelse(Auth::user()->unreadNotifications->take(20) as $notification)
                                    <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                        <div class="media text-inherit">
                                            <div class="media-body">
                                                @if($notification->type == 'App\Notifications\OrderNotification')
                                                    @if($notification->data['order_id'] != 0)
                                                        <p class="mb-1 text-truncate-2">
                                                            <a href="{{ route('seller.orders.show', encrypt($notification->data['order_id'])) }}">
                                                                {{translate('Order code: ')}} {{$notification->data['order_code']}} {{ translate('has been '. ucfirst(str_replace('_', ' ', $notification->data['status'])))}}
                                                            </a>
                                                        </p>
                                                        <small class="text-muted">
                                                            {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                                        </small>
                                                    @else
                                                        <p class="mb-1 text-truncate-2">
                                                            <a href="javascript:void(0);">
                                                                {{$notification->data['order_code']}}
                                                            </a>
                                                        </p>
                                                        <small class="text-muted">
                                                            {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                                        </small>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <div class="py-4 text-center fs-16">
                                            {{ translate('No notification found') }}
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="text-center border-top">
                            <a href="{{ route('seller.all-notification') }}" class="text-reset d-block py-2">
                                {{translate('View All Notifications')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Language --}}
            @php
                if(\Cookie::has('locale')){
                    $locale = \Cookie::get('locale', Config::get('app.locale'));
                }
                else{
                    $locale = env('DEFAULT_LANGUAGE');
                }
            @endphp

            <div class="aiz-topbar-item ml-2 d-none d-md-flex">
                <a class="d-flex align-items-center text-decoration-none" href="javascript:void(0);">
                    <span class="badge badge-soft-primary px-3 py-2">
                        <i class="las la-wallet mr-1"></i>
                        {{ single_price( Auth::user()->balance) }}
                    </span>
                </a>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown" id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="{{ static_asset('assets/img/flags/'.$locale.'.png') }}" height="11" style="border-radius:2px;">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">
                        @foreach (\App\Models\Language::all() as $key => $language)
                            <li>
                                <a href="javascript:void(0)" data-flag="{{ $language->code }}" class="dropdown-item @if($locale == $language->code) active @endif">
                                    <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" class="mr-2">
                                    <span class="language">{{ $language->name }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                <img
                                    src="{{ uploaded_asset(Auth::user()->avatar_original) }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';"
                                >
                            </span>
                            <span class="d-none d-md-block">
                                <span class="d-block fw-500">{{Auth::user()->name}}</span>
                                <span class="d-block small opacity-60">{{ translate('Seller') }}</span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="{{ route('seller.profile.index') }}" class="dropdown-item">
                            <i class="las la-user-circle"></i> <span>{{translate('Profile')}}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout')}}" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i> <span>{{translate('Logout')}}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function Jump() {
        window.open( '{{$customer_service_link}}' )
    }

    function toggle_mute(value) {
        $.post("{{route('business_settings.update4admin')}}", {types: ['msg_tip_mute'], 'msg_tip_mute': value}, function () {
            AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
            location.reload()
        })
    }
</script>
