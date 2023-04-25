<style>
    .nnnooo{ display:none !important;}
</style>
<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                @if(get_setting('system_logo_white') != null)
                    <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @else
                    <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @endif
            </a>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name="" placeholder="{{ translate('Search in menu') }}" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                @if (env("APP_ENV") == "local")
                    <li class="aiz-side-nav-item">
                        <a href="{{route('menu.index')}}" class="aiz-side-nav-link">
                            <i class="las la-home aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Menu')}}</span>
                        </a>
                    </li>
                @endif

                @php
                    $user = Auth::user();
                    $menus = \App\Models\Menu::getMenus();

                    $sellers = \App\Models\Shop::where('verification_status', 0)->where('verification_info', '!=', null);
                    $sellers = filter_by_bloc($sellers);
                    $sellers = $sellers->count();

                    $refund_count = \App\Models\RefundRequest::where('refund_status', 0)->select('id');
                    $refund_count = filter_by_bloc($refund_count);
                    $refund_count = $refund_count->count();

                    $conversation_count = \App\Models\Conversation::where('admin_viewed', 0);
                    $conversation_count = filter_by_bloc($conversation_count);
                    $conversation_count = $conversation_count->count();

                    $support_ticket = \App\Models\Ticket::where('viewed', 0)->select('id');
                    $support_ticket = filter_by_bloc($support_ticket);
                    $support_ticket = $support_ticket->count();
                @endphp
                @foreach($menus as $menu)
                        @if(!empty($menu->addon_name) && !addon_is_activated($menu->addon_name))
                            @continue
                        @endif
                    <li class="aiz-side-nav-item">
                        <a href="{{$menu->route ? route($menu->route) : '#'}}" class="aiz-side-nav-link">
                            <i class="las la-{{$menu->icon}} aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate($menu->name)}}</span>
                            @if($menu->show_red_tips) <span class="badge badge-danger badge-circle badge-sm badge-dot"></span> @endif

                            @if($menu->name == 'Support')
                                @if ($conversation_count > 0 || $support_ticket > 0)
                                    <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                                @else
                                    <span class="badge badge-danger badge-circle badge-sm badge-dot conversations" style="display: none"> </span>
                                @endif
                            @endif

                            @if($menu->children) <span class="aiz-side-nav-arrow"></span> @endif
                        </a>
                        @if($menu->children && (empty($menu->addon_name) || addon_is_activated($menu->addon_name)))
                            <ul class="aiz-side-nav-list level-2">
                                @foreach($menu['children'] as $menu2)
                                    <li class="aiz-side-nav-item">
                                        <a href="{{$menu2->route ? route($menu2->route) : '#'}}" class="aiz-side-nav-link {{ $menu2->active_routes ? areActiveRoutes(explode(",", $menu2->active_routes)) : ''}}">
                                            <span class="aiz-side-nav-text">{{translate($menu2->name)}}</span>
                                            @if($menu2->route == 'sellers.index' && $sellers > 0)<span class="badge badge-info">{{ $sellers }}</span> @endif
                                            @if($menu2->route == 'refund_requests_all' && $refund_count > 0) <span class="badge badge-info">{{ $refund_count }}</span> @endif

                                            @if($menu2->show_red_tips || $menu2->route == 'support_ticket.admin_index' && $support_ticket > 0 || $menu2->route == 'conversations.admin_index' && $conversation_count > 0)
                                                <span class="badge badge-danger badge-circle badge-sm badge-dot"></span>
                                            @endif


                                            @if($menu2->route == 'poin-of-sales.conversation')
                                                <span class="badge badge-danger badge-circle badge-sm badge-dot conversations" style="display: none"> </span>
                                            @endif
                                            @if($menu2->route == 'all_orders.index')
                                                <span class="badge badge-danger badge-circle badge-sm badge-dot" id="order-red-tip" style="display: none"> </span>
                                            @endif

                                            @if($menu2->children) <span class="aiz-side-nav-arrow"></span> @endif
                                        </a>
                                        @if($menu2->children && (empty($menu2->addon_name) || addon_is_activated($menu2->addon_name)))
                                            <ul class="aiz-side-nav-list level-3">
                                                @foreach($menu2->children as $menu3)
                                                    <li class="aiz-side-nav-item">
                                                        <a href="{{route($menu3->route)}}" class="aiz-side-nav-link {{ $menu3->active_routes ? areActiveRoutes(explode(",", $menu3->active_routes)) : ''}}">
                                                            <span class="aiz-side-nav-text">{{translate($menu3->name)}}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

                <!-- 系统配置 -->
                @if(Auth::user()->user_type == 'admin')
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-dharmachakra aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Setup & Configurations')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('file_system.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('File System & Cache Configuration')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('social_login.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Social media Logins')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Facebook')}}</span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('facebook_chat.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Facebook Chat')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('facebook-comment') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Facebook Comment')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Google')}}</span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google_analytics.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Analytics Tools')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google_recaptcha.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google reCAPTCHA')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google-map.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google Map')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google-firebase.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google Firebase')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!-- 俱乐部积分插件-->
                            @if (addon_is_activated('club_point'))
                            <li class="aiz-side-nav-item">
                                        <a href="#" class="aiz-side-nav-link">

                                            <span class="aiz-side-nav-text">{{translate('Club Point System')}}</span>
                                            @if (env("DEMO_MODE") == "On")
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            @endif
                                            <span class="aiz-side-nav-arrow"></span>
                                        </a>
                                        <ul class="aiz-side-nav-list level-3">
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('club_points.configs') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Club Point Configurations')}}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('set_product_points')}}" class="aiz-side-nav-link {{ areActiveRoutes(['set_product_points', 'product_club_point.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Set Product Point')}}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('club_points.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['club_points.index', 'club_point.details'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('User Points')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            @endif

                            <!--博客系统-->
                             <li class="aiz-side-nav-item">
                                        <a href="#" class="aiz-side-nav-link">

                                            <span class="aiz-side-nav-text">{{ translate('Blog System') }}</span>
                                            <span class="aiz-side-nav-arrow"></span>
                                        </a>
                                        <ul class="aiz-side-nav-list level-3">
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('blog.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog.create', 'blog.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{ translate('All Posts') }}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('blog-category.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog-category.create', 'blog-category.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{ translate('Categories') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                            <!-- 员工 -->
                             <li class="aiz-side-nav-item">
                                        <a href="#" class="aiz-side-nav-link">

                                            <span class="aiz-side-nav-text">{{translate('Staffs')}}</span>
                                            <span class="aiz-side-nav-arrow"></span>
                                        </a>
                                        <ul class="aiz-side-nav-list level-3">
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('staffs.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('All staffs')}}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('roles.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Staff permissions')}}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('bloc.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['bloc.index', 'bloc.create', 'bloc.edit'])}}">
                                                    <span class="aiz-side-nav-text">{{translate('Bloc')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                            <!-- 系统 -->
                             <li class="aiz-side-nav-item">
                                        <a href="#" class="aiz-side-nav-link">

                                            <span class="aiz-side-nav-text">{{translate('System')}}</span>
                                            <span class="aiz-side-nav-arrow"></span>
                                        </a>
                                        <ul class="aiz-side-nav-list level-3">
                                            <li class="aiz-side-nav-item">
                                                <a href="{{ route('system_update') }}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Update')}}</span>
                                                </a>
                                            </li>
                                            <li class="aiz-side-nav-item">
                                                <a href="{{route('system_server')}}" class="aiz-side-nav-link">
                                                    <span class="aiz-side-nav-text">{{translate('Server status')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                            <!--上传的文件-->
                            <li class="aiz-side-nav-item">
                                        <a href="{{ route('uploaded-files.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-files.create'])}}">

                                            <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                                        </a>
                                    </li>

                            <!-- 插件管理器 -->
                            <li class="aiz-side-nav-item">
                                    <a href="{{route('addons.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['addons.index', 'addons.create'])}}">

                                        <span class="aiz-side-nav-text">{{translate('Addon Manager')}}</span>
                                    </a>
                                </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->


<script type="text/javascript">
    function getConversations() {
        $.ajax( {
            type: "get",
            url: '{{ route('conversations.admin_message_count') }}',
            success: function (data)
            {
                if ( data.result > 0 ) {
                    $( '.conversations' ).show();
                }
                else {
                    $( '.conversations' ).hide();
                }
            }
        } );
    }

    function get_not_view_count() {
        $.ajax( {
            type: "post",
            url: '{{ route('orders.get_not_view_count') }}',
            success: function (data)
            {
                if ( data.result > 0 ) {
                    $( '.order-red-tip' ).show();
                }
                else {
                    $( '.order-red-tip' ).hide();
                }
            }
        } );
    }

    setTimeout(function () {
        getConversations();

        get_not_view_count();
    }, 2000);

    setInterval( function ()
    {
        getConversations();

        get_not_view_count();
    }, 10000 )
</script>
<script>


    function audioPlay(text) {
        var zhText = text;
        zhText = encodeURI( zhText );
        var audio = "<audio autoplay=\"autoplay\">" + "<source src=\"/public/new2.mp3\" type=\"audio/mpeg\">" + "<embed height=\"0\" width=\"0\" src=\"http://tts.baidu.com/text2audio?text=" + zhText + "\">" + "</audio>";
        $( 'body' ).append( audio );
    }

    function check_new_msg() {
        $.get( '{{route('admin.check_new_msg')}}', {}, function (res)
        {
            if ( res.code == 1 ) {
                audioPlay( res.msg );
            }
        }, 'json' )
    }

    window.onload = function ()
    {
        @if(!get_admin_setting('msg_tip_mute'))
        check_new_msg();
        setInterval(check_new_msg, 10e3 );
        @endif
    }
</script>
