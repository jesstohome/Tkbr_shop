@extends('backend.layouts.app')
<style type="text/css">
    ul.ticket {
        min-height: 50vh;
        max-height: 50vh;
        overflow-y: scroll;
    }
    ul.ticket, ul.ticket li {
        background-color: #ebedf2;
    }
    ul.ticket li .comment-header {
        display: flex;
        align-items: flex-start;
        flex-direction: column;
    }
    ul.ticket li .title {
        position: relative;
        max-width: 55vw;
        background-color: white;
        padding: 5px 10px;

        border-radius: 15px;

        word-wrap: break-word;
        overflow-wrap: break-word;
        min-width: 8rem;
        width: fit-content;
        padding-bottom: 1.5rem;
        line-height: 2rem;
    }
    ul.ticket li.mine .title {
        background-color: #d9fdd3;
    }
    ul.ticket li .title p {
        /*width: inherit;*/
        width: -webkit-fill-available;
    }
    ul.ticket li .time {
        right: 1rem;
        position: absolute;
        bottom: 0.2rem;
        text-align: right;
        padding: 0;
        margin: 0;
        color: #8b8b8b !important;
    }

    ul.ticket li .comment-header {
        margin-left: 0.5rem;
    }
    ul.ticket li.mine .comment-header {
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
        margin-right: 0.5rem;
    }

    div.images.mine {
        display: flex;
        justify-content: flex-end;
    }

    .note-editable.card-block {
        height: 80px !important;
    }

    .card.chat {
        /*position: fixed !important;*/
    }

    .card .card-body {
        padding: 20px 10px;
        padding-bottom: 5px;
    }

    li.mine .la-close {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 99;
        border: 1px solid;
        border-radius: 10px;
        cursor: pointer;
    }
</style>
@section('content')

<div class="col-lg-10 mx-auto">
    <div class="card chat">
        <div class="card-header row gutters-5">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h5">{{ $ticket->user->email }}</h5>
               <div class="mt-2">
                   <span> {{ $ticket->user->name }} </span>
                   <span class="ml-2"> {{ $ticket->user->created_at }} </span>
               </div>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-sm btn-primary" {{$ticket->order->product_storehouse_status ? 'disabled' : ''}} onclick="$('#confirm-payment-modal').modal('show');">{{$ticket->order->product_storehouse_status ? '已支付' : '确定订单已付款'}}</button>
            </div>
        </div>
        <div class="card-body">
            <div class="pad-top">
                <div class="top-info" style="margin-bottom: 15px">
                    <div class="row">
                        <div class="col-md-10">
                            <p>产品：
                                @if($ticket->order->details[0]->product)
                                <a href="{{route('product', $ticket->order->details[0]->product->slug)}}" target="_blank">{{$ticket->order->details[0]->product->name}}</a>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="order-info row">
                        <div class="col-md-2">
                            <p>订单号: {{$ticket->order->code}}</p>
                        </div>
                        <div class="col-md-2">
                            <p>买家付款金额: {{single_price($ticket->order->grand_total)}}</p>
                        </div>
                        <div class="col-md-2">
                            <p>提货金额: {{single_price($ticket->order->product_storehouse_total)}}</p>
                        </div>
                        <div class="col-md-2">
                            <p>利润: {{single_price($ticket->order->grand_total - $ticket->order->product_storehouse_total)}}</p>
                        </div>
                        @if($currency)
                        <div class="col-md-4">
                            <p>币种: {{translate($currency->name)}}, 汇率: ≈{{number_format($currency->exchange_rate, 2)}}, 转换后金额: ≈{{number_format($currency->exchange_rate * $ticket->order->product_storehouse_total, 2)}}</p>
                        </div>
                        @endif
                        <div class="col-md-2">
                            <p>提货状态: {{$ticket->order->product_storehouse_status ? '已提货' : '未提货'}}</p>
                        </div>
                        <div class="col-md-6">
                            <p>IP: {{$ticket->client_ip}} @if ($ticket->ip_location) ({{$ticket->ip_location}}) @endif</p>
                        </div>
                    </div>
                    <div class="order-opt row">
                        <div class="col-md-8">
                            备注：<input name="order_remark" value="{{$ticket->user->remark ?: ''}}" />
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush ticket">
                    @foreach($ticket_replies as $ticketreply)
                        @if(empty($ticketreply->user_id)) @continue @endif
                        <li class="list-group-item px-0 {{-1 == $ticketreply->user_id || $ticketreply->user_id == Auth::id() ? 'mine' : ''}} {{$ticketreply->read ? 'is-read' : 'un-read'}}" data-id="{{$ticketreply->id}}">
                            @if(!empty($ticketreply->reply) || $ticketreply->files)
                            <div class="media">
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="text-bold h6 text-muted title">
                                            @php echo $ticketreply->reply; @endphp
                                            @if($ticketreply->files)
                                            <div class="images {{$ticketreply->user->id == Auth::id() ? 'mine' : ''}}">
                                            @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                                    @php $file_detail = \App\Models\Upload::where('id', $file)->first(); @endphp
                                                    @if($file_detail != null)
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">
                                                    @endif
                                                @endforeach
                                            </div>
                                            @endif
                                            <p class="text-muted text-sm fs-11 time">
                                                {{date('m-d H:i', strtotime($ticketreply->created_at))}}
                                                @if((-1 == $ticketreply->user_id || $ticketreply->user_id == Auth::id()))
                                                    <span style='padding-left:3px;'>{{$ticketreply->read ? "已读" : "未读"}}</span>
                                                @endif
                                            </p>
                                        </span>
                                        <i class="la la-close" style="display: none"></i>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <form action="{{ route('support_ticket.admin_store') }}" method="post" id="ticket-reply-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="status" value="{{ $ticket->status }}" required>

                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="attachments" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="reply" value="" required />
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-primary" onclick="show_fast_reply_modal()">{{ translate('Select Fast Reply') }}</button>
                        <button type="submit" class="btn btn-sm btn-primary" onclick="submit_reply('pending')">{{ translate('Send Reply') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('modal')
    <!-- fast reply Modal -->
    <div class="modal fade" id="fast_reply_modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="fast-reply-modal-content">

            </div>
        </div>
    </div>

    <div id="confirm-payment-modal" class="modal fade">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">确认提示</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mt-1">确认已收到卖家货款？</p>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <a href="javascript:void(0)" class="btn btn-primary mt-2 comfirm-link" onclick="confirmPayment()">确定</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('partials.support_ticket.script')

    <script type="text/javascript">
        function confirmPayment() {
            $.post('{{ route('support_ticket.confirm_payment') }}', {
                _token: '{{ @csrf_token() }}',
                id: "{{$ticket->order->id}}"
            }, function (res) {
                AIZ.plugins.notify(res.success ? 'success' : 'danger', res.msg || '');
                res.success && location.reload()
            });
        }

        $(document).ready(function () {
            $("input[name=order_remark]").on("blur", function () {
                $.post('{{ route('support_ticket.save_remark') }}', {
                    _token: '{{ @csrf_token() }}',
                    seller_id: "{{$ticket->order->seller_id}}",
                    remark: $(this).val().trim(),
                }, function (res) {
                    AIZ.plugins.notify(res.success ? 'success' : 'danger', res.msg || '');
                });
            });
        });
    </script>
@endsection
