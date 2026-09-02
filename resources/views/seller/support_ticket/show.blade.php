@extends('seller.layouts.app')
<link rel="stylesheet" href="{{ static_asset('assets/css/chat-pc.css?v=1.1.0') }}">
<style type="text/css">
    .seller-support-chat-page {
        padding: 12px 16px 18px;
    }

    .seller-support-chat-page .chatroot {
        min-height: 0;
        background: #f7f8fb;
    }

    .seller-support-chat-page .main {
        display: block;
        max-width: 1180px;
        margin: 0 auto;
    }

    .kefuroot {
        width: 100% !important;
    }

    .seller-support-chat-page .kefuroot {
        height: calc(100dvh - 132px);
        min-height: 480px;
        padding-top: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border: 1px solid #e6eaf0;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        position: relative;
    }

    .aiz-main-content .px-15px {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .aiz-main-content .px-lg-25px {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .notice {
        left: 0;
    }

    .botRoot {
        right: 0;
    }

    .seller-support-chat-page .notice {
        position: static;
        height: 58px;
        flex: 0 0 58px;
        order: -1;
        background: #ffffff;
        border-bottom: 1px solid #e6eaf0;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .seller-support-chat-page .kefuroot > div:first-child {
        flex: 1 1 auto;
        min-height: 0;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }

    .seller-support-chat-page .chatlist {
        height: 100%;
        min-height: 0;
        padding: 18px 20px 22px;
        overflow-y: auto;
        overflow-x: hidden;
        scroll-behavior: smooth;
    }

    .seller-support-chat-page .chat {
        margin-left: 0;
        margin-right: 0;
        padding-top: 8px;
    }

    .seller-support-chat-page .head {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .seller-support-chat-page .atext,
    .seller-support-chat-page .btext {
        align-items: flex-end;
        gap: 8px;
    }

    .seller-support-chat-page .aspan,
    .seller-support-chat-page .bspan {
        max-width: min(72%, 760px);
        padding: 11px 14px;
        border-radius: 14px;
        line-height: 1.55;
        font-size: 14px;
        word-break: break-word;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
    }

    .seller-support-chat-page .aspan {
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }

    .seller-support-chat-page .bspan {
        background: #2563eb;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }

    .seller-support-chat-page .chatImg {
        width: auto;
        height: auto;
        max-width: min(260px, 48vw);
        max-height: 260px;
        padding: 0;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
    }

    .seller-support-chat-page video {
        max-width: min(320px, 55vw);
        border-radius: 12px;
        background: #0f172a;
    }

    .seller-support-chat-page .welcome-msg {
        width: auto;
        max-width: 680px;
        margin: 8px auto;
        padding: 12px 16px;
        background: #eef2ff;
        border: 1px solid #dbe4ff;
        border-radius: 12px;
        color: #3730a3;
        font-size: 14px;
        line-height: 1.5;
    }

    .seller-support-chat-page .pic {
        width: auto;
        max-width: 720px;
    }

    .seller-support-chat-page .timeIndex {
        width: auto;
        min-width: 92px;
        height: 22px;
        padding: 0 10px;
        color: #64748b;
        background: #e2e8f0;
        border-radius: 999px;
    }

    .seller-support-chat-page .botRoot {
        position: sticky;
        left: auto;
        right: auto;
        bottom: 0;
        height: auto;
        flex: 0 0 auto;
        background: rgba(255, 255, 255, 0.94);
        border-top: 1px solid #e6eaf0;
        backdrop-filter: blur(18px);
        box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.06);
        z-index: 5;
        padding: 10px 14px;
    }

    .seller-support-chat-page .bot {
        height: 46px;
        margin: 0;
        padding: 0;
        gap: 10px;
        justify-content: flex-start;
    }

    .seller-support-chat-page .fujian {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        flex: 0 0 42px;
    }

    .seller-support-chat-page #ticket-reply-form {
        min-width: 0;
    }

    .seller-support-chat-page .input {
        height: 42px;
        min-width: 0;
        margin-right: 0;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        background: #ffffff;
        resize: none;
        overflow-y: auto;
        line-height: 1.4;
    }

    .seller-support-chat-page .newline {
        height: 42px;
        min-width: 52px;
        margin-left: 10px;
        padding: 0 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #334155;
        font-size: 13px;
        cursor: pointer;
        flex: 0 0 auto;
    }

    .seller-support-chat-page .senddiv {
        width: auto;
        flex: 0 0 auto;
        position: static;
    }

    .seller-support-chat-page .send {
        width: auto;
        min-width: 76px;
        height: 42px;
        line-height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        background: #2563eb;
        font-weight: 600;
    }

    .seller-support-chat-page .file-preview {
        position: absolute;
        left: 14px;
        right: 14px;
        bottom: 66px;
        width: auto;
        max-height: 150px;
        overflow-y: auto;
        border-radius: 10px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
    }

    @media (max-width: 767.98px) {
        .seller-support-chat-page {
            padding: 8px 0 0;
        }

        .seller-support-chat-page .chatroot,
        .seller-support-chat-page .kefuroot {
            height: calc(100dvh - 72px);
            min-height: 0;
            border-radius: 0;
            border-left: 0;
            border-right: 0;
        }

        .seller-support-chat-page .notice {
            height: 50px;
            flex-basis: 50px;
            font-size: 15px;
        }

        .seller-support-chat-page .chatlist {
            padding: 14px 12px 18px;
        }

        .seller-support-chat-page .head {
            width: 32px;
            height: 32px;
        }

        .seller-support-chat-page .aspan,
        .seller-support-chat-page .bspan {
            max-width: calc(100vw - 110px);
            font-size: 13.5px;
        }

        .seller-support-chat-page .botRoot {
            padding: 8px 10px calc(8px + env(safe-area-inset-bottom));
        }

        .seller-support-chat-page .bot {
            gap: 8px;
        }

        .seller-support-chat-page .fujian {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
        }

        .seller-support-chat-page .input {
            height: 38px;
            padding: 8px 12px;
        }

        .seller-support-chat-page .newline {
            height: 38px;
            min-width: 42px;
            margin-left: 8px;
            padding: 0 8px;
            font-size: 12px;
        }

        .seller-support-chat-page .send {
            min-width: 60px;
            height: 38px;
            line-height: 38px;
            padding: 0 12px;
        }
    }
</style>
@section('panel_content')
    <div class="seller-support-chat-page">
    <div class="chatroot">
        <div class="main">
            <div class="kefuroot">
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
                                                <img class="chatImg lazyload" src="{{ static_asset('assets/img/chat/placeholder.jpg') }}" data-src="{{$filename}}" onclick="previewImg(this)" />
                                                @else
                                                     <video style="width: 200px" controls><source src="{{$filename}}" type="video/mp4"></video>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if($ticketreply->reply)
                                            <span class="bspan">@php $reply_text = empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; echo nl2br(e($reply_text)); @endphp</span>
                                        @endif
                                        <img src="{{Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head2.png')}}" class="head" style="margin-left: 8px;" />
                                    </div>
                                @else
                                    @if(empty($ticketreply->user_id))
                                        <div class="pic">
                                            <div class="welcome-msg">
                                                <svg t="1685791561209" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3147" width="16" height="16"><path d="M809.6 416.64h-53.76V308.48c0-135.68-108.096-243.84-243.2-243.84-135.04 0-243.2 108.16-243.2 243.84v108.16h-53.76c-29.44 0-53.76 24.32-53.76 53.76v433.28c0 29.44 24.32 53.76 53.76 53.76h593.92c30.08 0 54.4-24.32 54.4-53.76V470.4c0-29.44-24.32-53.76-54.4-53.76z m-135.04 0H350.72V308.48c0-89.6 72.96-162.56 161.92-162.56a162.56 162.56 0 0 1 161.92 162.56v108.16z" fill="#040000" p-id="3148"></path></svg>
                                                {{translate($ticketreply->reply)}}！
                                            </div>
                                        </div>
                                    @else
                                        <div class="atext">
                                            <img src="{{static_asset('assets/img/chat/head1.png')}}" class="head" style="margin-right: 8px;" />
                                            @if($ticketreply->files)
                                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                                    @php
                                                        $filename = uploaded_asset($file);
                                                    @endphp
                                                    @if (strpos($filename, ".mp4") === false)
                                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/chat/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                                    @else
                                                        <video style="width: 200px" controls><source src="{{$filename}}" type="video/mp4"></video>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if($ticketreply->reply)
                                                <span class="aspan">@php $reply_text = empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; echo nl2br(e($reply_text)); @endphp</span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="botRoot">
                    <div class="file-preview box sm"></div>
                    <div class="bot">
                        <div class="message fujian" onclick="openFileSelection()"><img src="{{static_asset('assets/img/chat/fujian.jpeg')}}" width="30"/></div>

                        <form id="ticket-reply-form" action="{{route('seller.support_ticket.reply_store')}}" method="POST" enctype="multipart/form-data" style="display: flex;flex:1">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                            <input type="hidden" name="user_id" value="{{$ticket->user_id}}">
                            <input type="hidden" name="attachments" class="selected-files">
                            <input type="file" id="fileInput" name="file" style="display: none;" accept="image/*,video/*" />
                            <textarea class="input" placeholder="{{translate('Please enter your question')}}" name="reply" onkeydown="submitReply()" enterkeyhint="send"></textarea>
                        </form>

                        <div class="message newline" onclick="insertLineBreak()">{{ translate('New Line') }}</div>

                        <div class="senddiv">
                            <div class="message send" onclick="submit_reply('pending')">{{translate('Send')}}</div>
                            <div class="message loading" style="display: none;"><img src="{{static_asset('assets/img/loading.gif')}}" /> </div>
                        </div>

                    </div>
                    <div class="sendBtn"></div>
                </div>
                <div class="notice">
                    {{translate('Tiktok Shop Serve')}}
                </div>
            </div>
        </div>

    </div>
    </div>
@endsection

@section('script')
    @include('partials.support_ticket.script')
@endsection
