@extends('seller.layouts.app')
<style type="text/css">
    ul.ticket, ul.ticket li {
        background-color: #ebedf2;
    }
    ul.ticket li .title {
        position: relative;
        max-width: 65vw;
        background-color: white;
        padding: 5px 10px;
        display: flex;
        border-radius: 15px;
        flex-direction: column;
        align-items: flex-start;
        word-wrap: break-word;
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
        margin-right: 0.5rem;

    }

    .note-editable.card-block {
        height: 80px !important;
    }
</style>
@section('panel_content')
    <div class="card">
        <div class="card-header row gutters-5">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h5">Tictok Shop Serve</h5>
               <div class="mt-2">
                   <span> {{ $ticket->user->name }} </span>
               </div>
            </div>
        </div>
        <div class="card-body">
            <div class="pad-top">
                <ul class="list-group list-group-flush ticket">
                    @foreach($ticket->ticketreplies as $ticketreply)
                        <li class="list-group-item px-0 {{$ticketreply->user->id == Auth::id() ? 'mine' : ''}}">
                            <div class="media">
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="text-bold h6 text-muted title">
                                            @php echo $ticketreply->reply; @endphp
                                            <p class="text-muted text-sm fs-11 time">{{date('m-d H:i', strtotime($ticketreply->created_at))}}</p>
                                        </span>

                                    </div>
                                </div>
                            </div>
                            <div>
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    @php $file_detail = \App\Models\Upload::where('id', $file)->first(); @endphp
                                    @if($file_detail != null)
                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">
                                        <br>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <form id="ticket-reply-form" action="{{route('seller.support_ticket.reply_store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="user_id" value="{{$ticket->user_id}}">

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
                        <button type="submit" class="btn btn-sm btn-primary" onclick="submit_reply('pending')">{{ translate('Send Reply') }}</button>
                    </div>
                    <!-- <textarea class="aiz-text-editor" name="reply" data-buttons='[]' required></textarea> -->

                </div>
            </form>

        </div>
    </div>
@endsection
@section('script')
    <script src="{{ static_asset('assets/js/layer.min.js') }}"></script>
    <script type="text/javascript">
        var user_id = "{{Auth::user()->id}}"

        $(document).ready(function () {
            $( '#ticket-reply-form' ).on("submit", function (){
                return false;
            })
        })

        function submit_reply(status) {
            $('input[name=status]').val(status);
            if($('input[name=reply]').val().length > 0) {
                var data = new FormData( $( '#ticket-reply-form' )[0] );
                $.ajax( {
                    url: "{{route('seller.support_ticket.reply_store')}}",
                    type: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $("input[name=attachments]").val('');
                        $("input[name=reply]").val('');
                        $(".remove-attachment").click();
                        if (!response.success) {
                            AIZ.plugins.notify('danger', res.msg || 'Error');
                            return;
                        }

                        render_reply(response.list || [])
                    }
                } );
            } else {
                AIZ.plugins.notify('danger', '请先填写内容');
            }
        }

        function previewImg(obj) {
            //弹出层
            layer.photos({
                photos: { // 图片层的数据源
                    "title": "", // 相册标题
                    "id": 123, // 相册 id
                    "start": 0, // 初始显示的图片序号，默认 0
                    "data": [   // 相册包含的图片，数组格式
                        {
                            "alt": "image",
                            "pid": 666, // 图片id
                            "src": obj.src, // 原图地址
                            "thumb": obj.src // 缩略图地址
                        }
                    ]
                },
                tab: function(data, layero){ // 图片层切换后的回调
                    console.log(data); // 当前图片数据信息
                    console.log(layero); // 图片层的容器对象
                }
            });
        }

        function loop_load_new_reply() {
            $.ajax( {
                url: "{{route('seller.support_ticket.load_new_reply')}}",
                type: 'GET',
                data: {
                    ticket_id: "{{$ticket->id}}",
                },
                success: function (response)
                {
                    var list = response.list || [];
                    render_reply(list)
                }
            } );
        }

        function render_reply(list) {
            if (!list) list = [];

            if (list.length) {
                list.forEach((item) => {
                    var images = '';
                    (item.file_list || []).forEach((img) => {
                        images += `<img src="${img}" data-src="${img}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">`
                    })
                    $("ul.ticket").append(`<li class="list-group-item px-0 ${item.user_id == user_id ? 'mine' : ''}">
                            <div class="media">
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="text-bold h6 text-muted title">
                                            ${item.reply}
                                            <p class="text-muted text-sm fs-11 time">${item.created_time}</p>
                                        </span>

                                    </div>
                                </div>
                            </div>
                            <div>${images}</div>
                        </li>
                        `);
                })
            }
        }

        setInterval(loop_load_new_reply, 10e3);
    </script>
@endsection
