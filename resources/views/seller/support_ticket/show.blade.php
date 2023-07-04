@extends('seller.layouts.app')
<link rel="stylesheet" href="{{ static_asset('assets/css/chat-pc.css?v=1.1.0') }}">
<style type="text/css">
    .kefuroot {
        width: 100% !important;
    }
    .aiz-main-content .px-15px {
        padding-left: 0;
        padding-right: 0;
    }
    .aiz-main-content .px-lg-25px {
        padding-left: 0;
        padding-right: 0;
    }

    .notice {
        left: 0;
    }

    .botRoot {
        right: 0;
    }
</style>
@section('panel_content')
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
                                                <img class="chatImg lazyload" src="{{ static_asset('assets/img/chat/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                            @endforeach
                                        @endif
                                        @if($ticketreply->reply)
                                            <span class="bspan">@php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp</span>
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
                                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/chat/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                                @endforeach
                                            @endif
                                            @if($ticketreply->reply)
                                                <span class="aspan">@php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp</span>
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
                            <input type="file" id="fileInput" name="file" style="display: none;" accept="image/*" />
                            <input class="input" placeholder="{{translate('Please enter your question')}}" name="reply" onkeydown="submitReply()" enterkeyhint="send" />
                        </form>

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
@endsection

@section('script')
    @include('partials.support_ticket.script')
@endsection
