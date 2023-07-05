<!doctype html>
@if(\App\Models\Language::where('code', \Cookie::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- ios-Safari保存H5网页到主屏幕-WapApp -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{static_asset('assets/img/logo.png')}}" />
    <link rel="apple-touch-startup-image" href="{{static_asset('assets/img/logo.png')}}" />
    <meta name="format-detection" content="telephone=no, email=no" />

	<!-- Favicon -->
	<link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
	<title>{{ get_setting('website_name').' | '.get_setting('site_motto') }}</title>

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

	<!-- aiz core css -->
	<link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if(\App\Models\Language::where('code', \Cookie::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
	<link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">

    <style>
        body {
            font-size: 12px;
        }
    </style>
	<script>
    	var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
            no_files_found: '{{ translate('No files found') }}',
        }
	</script>

</head>
<body class="">
    <audio id='tip-audio'><source src="/public/new2.mp3" type="audio/mpeg"></audio>
    <audio id='tip-audio-chat'><source src="/public/chat.mp3" type="audio/mpeg"></audio>
    <audio id='tip-audio-work-chat'><source src="/public/work-chat.mp3" type="audio/mpeg"></audio>
    <audio id='tip-audio-new-seller'><source src="/public/new-seller.mp3" type="audio/mpeg"></audio>
    <audio id='tip-audio-new-withdraw'><source src="/public/new-withdraw.mp3" type="audio/mpeg"></audio>
    <audio id='tip-audio-new-zixun'><source src="/public/new-zixun.mp3" type="audio/mpeg"></audio>

    <div class="aiz-main-wrapper">
        @if(env('APP_ENV') === 'local' && false)
            @include('backend.inc.admin_sidenav')
        @else
            @include('backend.inc.admin_sidenav_dynamics')
        @endif
		<div class="aiz-content-wrapper">
            @include('backend.inc.admin_nav')
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
                    @yield('content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto footer-site-name">
					<p class="mb-0">&copy; {{ get_setting('site_name') }} v{{ get_setting('current_version') }}</p>
				</div>
			</div><!-- .aiz-main-content -->
		</div><!-- .aiz-content-wrapper -->
	</div><!-- .aiz-main-wrapper -->

    @yield('modal')


	<script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
	<script src="{{ static_asset('assets/js/aiz-core.js?v=1.1') }}" ></script>

    @yield('script')

    <script type="text/javascript">
	    @foreach (session('flash_notification', collect())->toArray() as $message)
	        AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
	    @endforeach

        // 密码眼睛的切换
        $("div.u-eye").on("click", function () {
            if ($(this).hasClass("disabled")) {
                $(this).prev("input").attr("type", 'password');
            } else {
                $(this).prev("input").attr("type", 'text');
            }
            $(this).toggleClass("disabled");
        });

        if ($('#lang-change').length > 0) {
            $('#lang-change .dropdown-menu a').each(function() {
                $(this).on('click', function(e){
                    e.preventDefault();
                    var $this = $(this);
                    var locale = $this.data('flag');
                    $.post('{{ route('language.change') }}',{_token:'{{ csrf_token() }}', locale:locale}, function(data){
                        location.reload();
                    });

                });
            });
        }
        function menuSearch(){
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i,item){
				if($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#'){
					return item;
				}
			});

			if(filter !== ''){
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if(items.length > 0){
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						 $("#search-menu").append(`<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				}else{
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block">{{ translate('Nothing Found') }}</span></li>`);
				}
			}else{
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
        }
    </script>
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
    </script>
    <script>


        let played = 0;
        function audioPlay(type) {
            @if(get_admin_setting('msg_tip_mute'))
                return false;
            @endif

            console.log("type=", type)
            if (!type) {
                $("#tip-audio")[0].play();
            } else if (type === 'chat') {
                $("#tip-audio-chat")[0].play();
            } else if (type === 'work-chat') {
                $("#tip-audio-work-chat")[0].play();
            } else if (type === 'new-seller') {
                $("#tip-audio-new-seller")[0].play();
            } else if (type === 'new-withdraw') {
                $("#tip-audio-new-withdraw")[0].play();
            } else if (type === 'new-zixun') {
                $("#tip-audio-new-zixun")[0].play();
            } else {
                $("#tip-audio")[0].play();
            }
        }

        function check_new_msg() {
            $.get( '{{route('admin.check_new_msg')}}', {}, function (res)
            {
                if ( res.code == 1 ) {
                    if (res.hasNewAudio) {
                        if (res.chatAudio) {
                            audioPlay('chat');
                        } else if (res.workOrderChatAudio) {
                            audioPlay('work-chat');
                        } else if (res.new_shop_created_tip) {
                            audioPlay('new-seller');
                        } else if (res.new_withdraw_tip) {
                            audioPlay('new-withdraw');
                        } else if (res.new_conversation_tip) {
                            audioPlay('new-zixun');
                        } else {
                            audioPlay();
                        }
                    }
                    for (const ck in res.keys) {
                        // console.log(ck, res.keys[ck]);
                        if (res.keys[ck]) {
                            $("." + ck).show().parents("ul.level-2").prev("a").find("span.badge-dot").show();
                        }
                    }
                }
            }, 'json' )
        }

        // 表单重置
        function reset_form() {
            $('.aiz-selectpicker').selectpicker('val', '');
            $('.aiz-selectpicker').each((k, it) => {
                $(it).find("option").first().attr("selected", true).siblings().attr("selected", false);
            });
            setTimeout(function () {
                $("form input[type=text]").val('');
            }, 200);
        }

        window.onload = function () {
            check_new_msg();
            setInterval(check_new_msg, 10e3 );
        }
    </script>

    @include('partials.support_ticket.notice')

</body>
</html>
