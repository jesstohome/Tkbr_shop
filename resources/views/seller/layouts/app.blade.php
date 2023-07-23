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
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <!-- ios-Safari保存H5网页到主屏幕-WapApp -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{static_asset('assets/img/logo.png')}}" />
    <link rel="apple-touch-startup-image" href="{{static_asset('assets/img/logo.png')}}" />
    <meta name="format-detection" content="telephone=no, email=no" />

    @if($fullscreen)
        <meta name="full-screen" content="yes" />
        <meta name="x5-fullscreen" content="true" />
        <meta name="apple-touch-fullscreen" content="yes">
    @endif

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
	<link rel="stylesheet" href="{{ static_asset('assets/css/aiz-seller.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">

    <style>
        body {
            font-size: 12px;
        }

        .ios-tips {
            z-index: 99;
            background-color: black;
            width: 100%;
            height: 100%;
            position: fixed;
        }

        .ios-tips img {
            width: 80%;
            position: fixed;
            align-items: center;
            top: 10%;
            left: 10%;
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
            daterangepicker: {
                applyLabel: '{{translate('Select')}}',
                cancelLabel: '{{translate('Cancel')}}',
            }
        }
	</script>

</head>
<body class="">
    <audio id='tip-audio'><source src="/public/new2.mp3" type="audio/mpeg"></audio>
	<div class="aiz-main-wrapper">
        @include('seller.inc.seller_sidenav')
		<div class="aiz-content-wrapper">
            @include('seller.inc.seller_nav')
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
                    @yield('panel_content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto border-sm-top footer-site-name">
					<p class="mb-0">&copy; {{ get_setting('site_name') }}</p>
				</div>
			</div><!-- .aiz-main-content -->
		</div><!-- .aiz-content-wrapper -->
	</div><!-- .aiz-main-wrapper -->

    @yield('modal')


	<script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
	<script src="{{ static_asset('assets/js/aiz-core.js?v=1.2.2') }}" ></script>
    <script src="{{ static_asset('assets/js/layui.js') }}"></script>
    <script src="{{ static_asset('assets/js/laravel-echo.min.js') }}"></script>

    @if(Auth::id() == 3663)
    <script src="//{{ Request::getHost() }}/ws/socket.io/socket.io.js"></script>
    <script type="text/javascript">
        const echo = new Echo({
            broadcaster: 'socket.io',
            host: window.location.hostname + '/ws/', // Laravel WebSockets 的默认端口
            // 更多配置选项...
        });

        console.log("echo=", echo);

        echo.channel(`red-pointer.{{Auth::id()}}`)
            .listen('RedPointerTips', (e) => {
                console.log('RedPointerTips', e);
                let data = e.data;
                if ( data.new_conversations > 0 ) $( '#conversations' ).show();
                if ( data.product_review_tip) $( '.product_review_tip' ).show();
                if (data.ticket_count) $( '.chat-num-tip' ).show();

                if (data.newAudio) {
                    audioPlay();
                }
            });
    </script>
    @endif

    @yield('script')

    <script type="text/javascript">
	    @foreach (session('flash_notification', collect())->toArray() as $message)
	        AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
	    @endforeach

            $(document).ready(function () {
                // 密码眼睛的切换
                $("div.u-eye").on("click", function () {
                    if ($(this).hasClass("disabled")) {
                        $(this).prev("input").attr("type", 'password');
                    } else {
                        $(this).prev("input").attr("type", 'text');
                    }
                    $(this).toggleClass("disabled");
                });

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

        // 下载PDF订单信息
        function downloadInvoicePdf(pdfUrl) {
            if (window.android) {
                window.android.openWindow(pdfUrl)
            } else {
                window.open(pdfUrl)
            }
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

        @if(Auth::user()->shop->verification_status == 1)
        @php
            $min = DEFAULT_VISITS_MIN;
            $max = DEFAULT_VISITS_MAX;
            $rand_range = explode("-", Auth::user()->shop->view_rand_range);
            if (!empty($rand_range) && count($rand_range) > 1) {
                $min = min($rand_range);
                $max = max($rand_range);
            }
        @endphp
        function rand_add_views() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': AIZ.data.csrf
                },
                method: "POST",
                url: "{{route('seller.shop.rand_add_views')}}",
                success: function (data, textStatus, jqXHR) {}
            });

            setTimeout(rand_add_views, parseInt({{$min}} + Math.random() * {{$max - $min}}) * 1e3);
        }
        @if ($max > 1 && $min > 1 && $max - $min > 1)
        setTimeout(rand_add_views, parseInt({{$min}} + Math.random() * {{$max - $min}}) * 1e3);
        @endif
        @endif
    </script>

    @include('partials.support_ticket.notice')
</body>
</html>
