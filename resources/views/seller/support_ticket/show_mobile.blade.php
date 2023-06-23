<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <title>{{translate('Customer Service Center')}}</title>
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
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
    <script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
    <script src="{{ static_asset('assets/js/aiz-core.js') }}" ></script>
    <script src="{{ static_asset('assets/js/layui.js') }}"></script>
</head>
<body>
<audio id='tip-audio'><source src="/public/new2.mp3" type="audio/mpeg"></audio>
<div id="app">
    <div class="chatroot">
    <div class="titlebar">
        <div class="back" onclick="chat_back()"><img src="{{static_asset('assets/img/chat/back.png')}}" style="width:18px;height: 13px ;" /></div>
        {{translate('Customer Service Center')}}
    </div>
    <div>
        <div class="chatlist">
            @foreach($ticket_replies as $index => $ticketreply)
            <div class="chat">
                @if($index % 4 === 0)
                <div class="timeIndex">{{date('m-d H:i', strtotime($ticketreply->created_at))}}</div>
                @endif

                @if($ticketreply->user->id == Auth::id())
                        <div class="btext">
                            @if($ticketreply->files)
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                @endforeach
                            @endif

                            @if($ticketreply->reply)
                                <span class="bspan">@php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp</span> @endif
                            <img src="{{Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head2.png')}}" class="head" style="margin-left: 8px;" />
                        </div>
                @else
                        <div class="atext">
                            <img src="{{ Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" />

                            @if($ticketreply->reply)
                                <span class="aspan">@php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp</span>@endif
                        </div>
                @endif

            </div>
            @endforeach
        </div>
    </div>
    <div class="botRoot">
        <div class="file-preview box sm"></div>
        <div class="bot">
            <form id="ticket-reply-form" action="{{route('seller.support_ticket.reply_store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="user_id" value="{{$ticket->user_id}}">
                <input type="hidden" name="attachments" class="selected-files">
                <input type="file" id="fileInput" name="file" style="display: none;" accept="image/*" multiple />

                <input class="input" placeholder="{{translate('Please enter your question')}}" name="reply" onkeyup="toggleSendBtn(this)" onkeydown="submitReply()" enterkeyhint="send" />
            </form>

            <div class="senddiv">
                <div class="message fujian" onclick="openFileSelection()"></div>
                <div class="message send"  style="display: none" onclick="submit_reply('pending')"></div>
            </div>
        </div>
    </div>
</div>
</div>
</body>
<style type="text/css">
    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        width: 100%;
        height: 100%;
        background: linear-gradient(118deg, #C1D1FF 0%, #FFFFFF 76%, #FFFFFF 100%);
        background-repeat: no-repeat;

    }

    body {
        margin: 0;

    }
    .chatroot {
        margin-top: 44px;
    }

    .back {
        position: absolute;
        left: 14px;
        top: 8px;
    }

    .chatlist {
        min-height: 700px;
        padding-bottom: 120px;
        overflow: auto;
    }

    .timeIndex {
        display: flex;
        margin: auto;
        width: 90px;
        margin-top: 10px;
        margin-bottom: 10px;
        text-align: center;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        height: 15px;
        background: rgba(122, 133, 177, 0.27);
        border-radius: 6px;
        font-size: 11px;

    }

    .orderInfo {
        display: flex;
        background-image: url('/static/images/orderBg.png');
        background-size: 100% 100%;
        align-items: center;
        padding-left: 10px;
        padding-right: 10px;
        width: 275px;
        margin: auto;
        height: 109px;

    }

    .orderTitle {
        color: #333333;
        white-space: nowrap;
        /*强制span不换行*/
        overflow: hidden;
        /*超出宽度部分隐藏*/
        text-overflow: ellipsis;
        /*超出部分以点号代替*/
        font-size: 13px;
    }

    .chat {
        padding-top: 10px;
        margin-left: 14px;
        margin-right: 14px;
    }

    .orderNum {
        color: #F0A925;
        font-size: 11px;
        white-space: nowrap;
        /*强制span不换行*/
        overflow: hidden;
        /*超出宽度部分隐藏*/
        text-overflow: ellipsis;
        /*超出部分以点号代替*/
    }

    .orderPrice {
        color: #DF1B1B;
        font-size: 16px;
        white-space: nowrap;
        /*强制span不换行*/
        overflow: hidden;
        /*超出宽度部分隐藏*/
        text-overflow: ellipsis;
        /*超出部分以点号代替*/

    }

    .orderImg {
        width: 70px;
        height: 70px;
        background: #0A0625;
    }

    .info {
        display: flex;
        flex-grow: 1;
        height: 70px;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;

    }

    .head {
        width: 32px;
        height: 32px;
        border-radius: 16px;
        background-image: ulr('/static/image');
    }

    .atext {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        margin-top: 14px;
        margin-right: 10px;
    }

    .aspan {
        background: #F4F5FF;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 12px;
        margin-right: 4px;
        padding-bottom: 12px;
        border-radius: 0 14px 14px 14px;
        color: #0A0625;
        max-width: 210px;
        font-size: 14px;
        text-align: left;
        word-wrap: break-word;
    }

    .chatImg {
        {{--background-image: url("{{static_asset('assets/img/chat/demo.png')}}");--}}
        background-size: 100% 100%;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 12px;
        margin-left: 4px;
        max-width: 210px;
        padding-bottom: 12px;
        border-radius: 14px 0 14px 14px;
        color: #ffffff;
        height: 110px;
        width: 140px;
        font-size: 14px;
        text-align: left;
    }

    .bspan {
        background: #3393FF;
        padding-left: 16px;
        padding-right: 16px;
        padding-top: 12px;
        margin-left: 4px;
        max-width: 210px;
        padding-bottom: 12px;
        border-radius: 14px 0 14px 14px;
        color: #ffffff;
        font-size: 14px;
        text-align: left;
        word-wrap: break-word;
    }

    .btext {
        display: flex;
        justify-content: flex-end;
        align-items: flex-start;
        margin-top: 14px;
        margin-left: 10px;
    }

    .titlebar {
        display: flex;
        background: #C1D1FF;
        align-items: center;
        justify-content: center;

        font-size: 18px;
        color: #000;
        height: 44px;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        font-weight: bold;
    }

    .botRoot {
        position: fixed;
        bottom: 0;
        height: 92px;
        left: 0;
        right: 0;

        background: rgba(255, 255, 255, 0.4);
        box-shadow: 0 -4px 18px 0 rgba(195, 199, 223, 0.3);
        backdrop-filter: blur(50px);
    }

    .bot {
        margin-top: 17px;
        display: flex;
        align-items: center;
        position: relative;
        justify-content: space-between;
        height: 46px;
        margin-left: 20px;
        margin-right: 20px;
        background: #FFFFFF;
        box-shadow: 0 0 26px 0 rgba(105, 127, 255, 0.31);
        border-radius: 41px;

        padding-left: 14px;
        padding-right: 14px;

    }

    .input {
        background: #FFFFFF;
        width: 274px;
        color: #333333;
        height: 37px;
        padding-left: 14px;
        border-radius: 5px;
    }

    .senddiv {
        display: flex;
        height: 100%;
        width: 54px;
        right: 6px;
        position: absolute;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .send {
        width: 54px;
        height: 32px;
        background-size: 100% 100%;
        /*background-image: url('/static/images/sendbg.png');*/
        background-image: url("{{static_asset('assets/img/chat/sendbg.png')}}");

    }

    .fujian {
        width: 54px;
        height: 32px;
        background-size: 100% 100%;
        /*background-image: url('/static/images/fujian.png');*/
        background-image: url("{{static_asset('assets/img/chat/fujian.png')}}");

    }

    input:focus {
        border: 0;
        outline: 0;
    }

    input {
        border: 0;
        outline: 0;
    }

    /**
    新加的CSS
     */
    .file-preview {
        position: absolute;
        top: -80px;
        left: 0;
    }
    .file-preview img {
        width:100px;

    }
    .file-preview .remove {
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
<script>

</script>
@include('partials.support_ticket.script')
