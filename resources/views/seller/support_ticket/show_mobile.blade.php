@extends('seller.layouts.app')

<link rel="stylesheet" href="{{ static_asset('assets/css/chat.css') }}">
<style>
    video::-webkit-media-controls-play-button {
        background-color: red;
        width: 30px;
        height: 30px;
        position: absolute;
        top: 10px;
        left: 10px;
    }

    .seller-support-chat-mobile #app {
        min-height: 0;
        background: #f8fafc;
        text-align: left;
    }

    .seller-support-chat-mobile .chatroot {
        margin-top: 0;
        height: calc(100dvh - 72px);
        min-height: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .seller-support-chat-mobile .titlebar {
        position: static;
        flex: 0 0 50px;
        height: 50px;
        background: #ffffff;
        border-bottom: 1px solid #e6eaf0;
        color: #0f172a;
        font-size: 15px;
        z-index: 1;
    }

    .seller-support-chat-mobile .back {
        top: 14px;
    }

    .seller-support-chat-mobile .chatroot > div:nth-child(2) {
        flex: 1 1 auto;
        min-height: 0;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }

    .seller-support-chat-mobile .chatlist {
        min-height: 0;
        height: 100%;
        padding: 14px 12px 18px;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    .seller-support-chat-mobile .chat {
        margin-left: 0;
        margin-right: 0;
        padding-top: 8px;
    }

    .seller-support-chat-mobile .head {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .seller-support-chat-mobile .atext,
    .seller-support-chat-mobile .btext {
        align-items: flex-end;
        gap: 8px;
        margin-left: 0;
        margin-right: 0;
    }

    .seller-support-chat-mobile .aspan,
    .seller-support-chat-mobile .bspan {
        max-width: calc(100vw - 110px);
        padding: 10px 12px;
        border-radius: 14px;
        line-height: 1.55;
        font-size: 13.5px;
        word-break: break-word;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    }

    .seller-support-chat-mobile .aspan {
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }

    .seller-support-chat-mobile .bspan {
        background: #2563eb;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    .seller-support-chat-mobile .chatImg {
        width: auto;
        height: auto;
        max-width: min(240px, 54vw);
        max-height: 240px;
        padding: 0;
        border-radius: 12px;
        object-fit: cover;
    }

    .seller-support-chat-mobile video {
        max-width: min(260px, 62vw);
        border-radius: 12px;
        background: #0f172a;
    }

    .seller-support-chat-mobile .botRoot {
        position: sticky;
        left: auto;
        right: auto;
        bottom: 0;
        flex: 0 0 auto;
        height: auto;
        padding: 8px 10px calc(8px + env(safe-area-inset-bottom));
        background: rgba(255, 255, 255, 0.94);
        border-top: 1px solid #e6eaf0;
        backdrop-filter: blur(18px);
        box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.06);
    }

    .seller-support-chat-mobile .bot {
        height: 40px;
        margin: 0;
        padding: 0;
        gap: 8px;
        background: transparent;
        box-shadow: none;
        border-radius: 0;
    }

    .seller-support-chat-mobile .fujian {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        flex: 0 0 38px;
    }

    .seller-support-chat-mobile #ticket-reply-form {
        flex: 1 1 auto;
        min-width: 0;
    }

    .seller-support-chat-mobile .input {
        width: 100%;
        height: 38px;
        padding: 0 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #ffffff;
    }

    .seller-support-chat-mobile .senddiv {
        position: static;
        width: 46px;
        flex: 0 0 46px;
    }

    .seller-support-chat-mobile .send {
        width: 46px;
        height: 38px;
        border-radius: 10px;
        background: #2563eb;
        color: #ffffff;
        line-height: 38px;
        text-align: center;
        font-size: 12px;
        font-weight: 600;
    }

    .seller-support-chat-mobile .file-preview {
        left: 10px;
        right: 10px;
        top: auto;
        bottom: 62px;
        max-height: 140px;
        overflow-y: auto;
        border-radius: 10px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        background: #ffffff;
    }
</style>

@section('panel_content')
<div class="seller-support-chat-mobile">
<div id="app">
    <div class="chatroot">
    <div class="titlebar">
        <div class="back" onclick="chat_back()"><img src="{{static_asset('assets/img/chat/back.png')}}" style="width:18px;height: 13px ;" /></div>
        {{translate('Customer Service Center')}}
    </div>
    <div>
        <div class="chatlist">
            @foreach($ticket_replies as $index => $ticketreply)
            <div class="chat {{$ticketreply->user->id == Auth::id() ? 'mine' : ''}}" data-id="{{$ticketreply->id}}">
                @if($index % 4 === 0)
                <div class="timeIndex">{{substr($ticketreply->created_at, 5, -3)}}</div>
                @endif

                @if($ticketreply->user->id == Auth::id())
                        <div class="btext">
                            @if($ticketreply->files)
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    @php
                                        $filename = uploaded_asset($file);
                                    @endphp
                                    @if (strpos($filename, ".mp4") === false)
                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ $filename }}" onclick="previewImg(this)" />
                                    @else
                                        <video style="width: 200px" controls><source src="{{$filename}}" type="video/mp4"></video>
                                    @endif
                                @endforeach
                            @endif

                            @if($ticketreply->reply)
                                <span class="bspan">@php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp</span> @endif
                            <img src="{{Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head2.png')}}" class="head" style="margin-left: 8px;" />
                        </div>
                @else
                        <div class="atext">
                            <img src="{{static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" />

                            @if($ticketreply->files)
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    @php
                                        $filename = uploaded_asset($file);
                                    @endphp
                                    @if (strpos($filename, ".mp4") === false)
                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                    @else
                                        <video style="width: 200px" controls><source src="{{$filename}}" type="video/mp4"></video>
                                    @endif
                                @endforeach
                            @endif

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
            <div class="message fujian" onclick="openFileSelection()">
                <img src="{{static_asset('assets/img/chat/fujian.jpeg')}}" width="30" />
            </div>

            <form id="ticket-reply-form" action="{{route('seller.support_ticket.reply_store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="user_id" value="{{$ticket->user_id}}">
                <input type="hidden" name="attachments" class="selected-files">
                <input type="file" id="fileInput" name="file" style="display: none;" />

                <input class="input" placeholder="{{translate('Please enter your question')}}" name="reply" onkeydown="submitReply()" enterkeyhint="send" />
            </form>

            <div class="senddiv">
                <div class="message send" onclick="submit_reply('pending')">{{ translate('Send') }}</div>
                <div class="message loading" style="display: none;"><img src="{{static_asset('assets/img/loading.gif')}}" /> </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
<style type="text/css">
    .chatroot {
        margin-top: 0;
    }
    .aiz-content-wrapper {
        padding-top: 55px !important;
    }
</style>

@section('script')
    @include('partials.support_ticket.script')
@endsection
