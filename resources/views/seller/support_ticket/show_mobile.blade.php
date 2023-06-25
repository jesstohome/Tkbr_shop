@extends('seller.layouts.app')

<link rel="stylesheet" href="{{ static_asset('assets/css/chat.css') }}">

@section('panel_content')
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
                            <img src="{{static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" />

                            @if($ticketreply->files)
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    <img class="chatImg lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
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
