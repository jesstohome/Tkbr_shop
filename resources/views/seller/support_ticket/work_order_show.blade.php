@extends('seller.layouts.app')
<link rel="stylesheet" href="{{ static_asset('assets/css/chat-pc.css') }}">
@section('panel_content')
    <div class="chatroot">
        <div class="main">
            <div class="kefuroot">
                <div>
                    <div class="chatlist">
                        <div class="pic">
                            <div
                                style="width:550px;height:65px;background-image: url('{{static_asset('assets/img/chat/picbar.jpg')}}');background-size: 100% 100%;color: #5548FB; font-size: 20px;line-height: 80px;margin-top: 60px;">
                                您好，BEI智能客服助理为您服务！</div>
                            <img src="{{static_asset('assets/img/chat/jingling.png')}}"
                                 style="width: 140px; height: 140px;position: absolute;top: -80px;left:205px ">
                        </div>
                        @foreach($ticket_replies as $ticketreply)
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
                        </div>
                        @endforeach
                    </div>

                </div>
                <div class="botRoot">
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
                    <span style="color: #5548FB; flex: 1; text-align: left;padding-left: 12px;">谨防诈骗，AI客服防诈小提醒！</span>
                    <div class="cbtn">官方</div>
                    <img src="{{static_asset('assets/img/chat/guanbi.png')}}" style="width: 16px; height: 16px;" alt="" />
                </div>
            </div>
            <div class="orderinfoRoot">
                <div class="bar">我的订单
                    <div class="barindex"></div>
                </div>
                <div class="orderList">
                    <div class="item1">
                        <img class="orderImgRight" />
                        <div class="info">
                            <div class="text">共享充电宝20000毫安超大容量自带线</div>
                            <div class="orderbot">
                                <span class="ordername">¥1399.00</span>
                                <span class="ordreprice">发送</span>
                            </div>
                        </div>
                        <div class="item1divider"></div>
                    </div>

                    <div class="item1">
                        <img class="orderImgRight" />
                        <div class="info">
                            <div class="text">共享充电宝20000毫安超大容量自带线</div>
                            <div class="orderbot">
                                <span class="ordername">¥1399.00</span>
                                <span class="ordreprice">发送</span>
                            </div>
                        </div>
                        <div class="item1divider"></div>
                    </div>
                    <div class="item1">
                        <img class="orderImgRight" />
                        <div class="info">
                            <div class="text">共享充电宝20000毫安超大容量自带线</div>
                            <div class="orderbot">
                                <span class="ordername">¥1399.00</span>
                                <span class="ordreprice">发送</span>
                            </div>

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
