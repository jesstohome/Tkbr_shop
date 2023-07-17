@extends('seller.layouts.app')

<link rel="stylesheet" href="{{ static_asset('assets/css/chat.css') }}">

@section('panel_content')
<div id="app">
    <div class="chatroot">
        <div class="titlebar">
            <div class="back" onclick="chat_back()"><img src="{{static_asset('assets/img/chat/back.png')}}" style="width:18px;height: 13px ;" /></div>
            {{translate('Customer Service Center')}}
        </div>
        <div class="order-info">
            <h5 class="mb-md-0 h5" style="height: 25px;overflow: hidden;">{{$ticket->order->details[0]->product ? $ticket->order->details[0]->product->getTranslation('name') : ''}}</h5>
            <div class="info-item text-center">
                <span> {{ translate("The manufacturer has paid a security deposit") }} </span>
            </div>
            <div class="info-item text-center">
                <p> {{ translate('Order No') }}: {{$ticket->order->code}} </p>
                @if($currency) <p> {{ translate('Currency') }}: {{translate($currency->name)}} </p> @endif
                <p> {{ translate('Order Amount') }}: {{single_price($ticket->order->grand_total)}} @if($currency) ≈ {{number_format(number_format($currency->exchange_rate, 2) * $ticket->order->grand_total, 2)}} @endif</p>
                @if($currency) <p> {{ translate('exchange rate') }}:  ≈{{number_format($currency->exchange_rate, 2)}} </p> @endif
                <p> {{ translate('Pickup amount') }}: {{single_price($ticket->order->product_storehouse_total)}} @if($currency) ≈ {{number_format(number_format($currency->exchange_rate, 2) * $ticket->order->product_storehouse_total, 2)}} @endif</p>
            </div>
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
                                @if(!empty($ticketreply->user_id))
                                <img src="{{ static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" />
                                @endif

                                @if($ticketreply->files)
                                    @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                        <img class="chatImg lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" />
                                    @endforeach
                                @endif

                                @if($ticketreply->reply)
                                    <span class="aspan" @if(empty($ticketreply->user_id)) style="margin:auto;"@endif>
                                        @if(empty($ticketreply->user_id))
                                            <svg t="1685791561209" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3147" width="16" height="16"><path d="M809.6 416.64h-53.76V308.48c0-135.68-108.096-243.84-243.2-243.84-135.04 0-243.2 108.16-243.2 243.84v108.16h-53.76c-29.44 0-53.76 24.32-53.76 53.76v433.28c0 29.44 24.32 53.76 53.76 53.76h593.92c30.08 0 54.4-24.32 54.4-53.76V470.4c0-29.44-24.32-53.76-54.4-53.76z m-135.04 0H350.72V308.48c0-89.6 72.96-162.56 161.92-162.56a162.56 162.56 0 0 1 161.92 162.56v108.16z" fill="#040000" p-id="3148"></path></svg>
                                        @endif
                                        @php echo empty($ticketreply->user_id) || -1 == $ticketreply->user_id ? translate($ticketreply->reply) : $ticketreply->reply; @endphp
                                    </span>
                                @endif
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
                    <input type="file" id="fileInput" name="file" style="display: none;" accept="video/*" capture="camcorder"> />

                    <input class="input" placeholder="{{translate('Please enter your question')}}" name="reply" onkeydown="submitReply()" enterkeyhint="send" />
                </form>

                <div class="senddiv">
                    <div class="message send" onclick="submit_reply('pending')"></div>
                    <div class="message loading" style="display: none;"><img src="{{static_asset('assets/img/loading.gif')}}" /> </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style type="text/css">
    .chatlist {
        padding-top:10px;
    }
</style>

@section('script')
@include('partials.support_ticket.script')
@endsection
