@extends('seller.layouts.app')
<link rel="stylesheet" href="{{ static_asset('assets/css/chat-pc.css') }}">
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
                                        <img src="{{Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/chat/head2.png')}}" class="head" style="margin-left: 8px;" />
                                    </div>
                            @else
                                @if(empty($ticketreply->user_id))
                                        <div class="pic">
                                            <div class="welcome-msg">
                                                {{translate($ticketreply->reply)}}！</div>
                                            <img src="{{static_asset('assets/img/chat/jingling.png')}}" style="width: 140px; height: 140px;position: absolute;top: -80px;left:205px ">
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
                    <img src="{{static_asset('assets/img/chat/icon_laba.png')}}" style="width: 16px; height: 16px;" alt="" />
                    <span style="color: #5548FB; flex: 1; text-align: left;padding-left: 12px;">{{translate($remind_tips)}}！</span>
                </div>
            </div>
            <div class="orderinfoRoot">
                <div class="bar">{{translate('Order Info')}}
                    <div class="barindex"></div>
                </div>
                <div class="orderList">
                    <div class="item1">
                        <div class="info">
                            <div class="text"><span> {{ translate("The manufacturer has paid a security deposit") }} </span></div>
                        </div>
                        <div class="item1divider"></div>
                    </div>
                    <div class="item1">
                        <img class="orderImgRight" @if($ticket->order->details[0]->product) style="background-image: url('{{uploaded_asset($ticket->order->details[0]->product->image)}}')" @endif/>
                        <div class="info">
                            <div class="text">{{$ticket->order->details[0]->product ? $ticket->order->details[0]->product->getTranslation('name') : ''}}</div>
                            <div class="orderbot">
                                <span class="ordername">{{single_price($ticket->order->product_storehouse_total)}}</span>
                            </div>

                        </div>
                        <div class="item1divider"></div>
                    </div>
                    <div class="item1">
                        <div class="info" style="text-align: left">
                            <p> {{ translate('Order No') }}: {{$ticket->order->code}} </p>
                            @if($currency) <p> {{ translate('Currency') }}: {{translate($currency->name)}} </p> @endif
                            <p> {{ translate('Order Amount') }}: {{single_price($ticket->order->grand_total)}} @if($currency) ≈ {{number_format($currency->exchange_rate * $ticket->order->grand_total, 2)}} @endif</p>
                            @if($currency) <p> {{ translate('exchange rate') }}:  ≈{{number_format($currency->exchange_rate, 2)}} </p> @endif
                            <p> {{ translate('Pickup amount') }}: {{single_price($ticket->order->product_storehouse_total)}} @if($currency) ≈ {{number_format($currency->exchange_rate * $ticket->order->product_storehouse_total, 2)}} @endif</p>
                        </div>
                        <div class="item1divider"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('script')
    @include('partials.support_ticket.script')
@endsection
