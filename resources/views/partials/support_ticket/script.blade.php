<script src="{{ static_asset('assets/js/layui.js') }}"></script>
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
                url: "{{Auth::user()->user_type == 'admin' ? route('support_ticket.admin_store') : route('seller.support_ticket.reply_store')}}",
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
            AIZ.plugins.notify('danger', '{{translate('Please fill in the content first')}}');
        }
    }

    function previewImg(obj) {
        var curTop = document.body.scrollTop
        //弹出层
        layer.photos({
            scrollbar: false,
            photos: { // 图片层的数据源
                "title": "images", // 相册标题
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
            hideFooter: true,
            tab: function(data, layero){ // 图片层切换后的回调
                console.log(data); // 当前图片数据信息
                console.log(layero); // 图片层的容器对象
            },
            end: function(){
                console.log('弹层已被移除');
                document.body.scrollTop = curTop
            },
        });
    }

    function loop_load_new_reply() {
        $.ajax( {
            url: "{{route(Auth::user()->user_type == 'admin' ? 'support_ticket.load_new_reply' : 'seller.support_ticket.load_new_reply')}}",
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
                            <div class="images ${item.user_id == user_id ? 'mine' : ''}">${images}</div>
                        </li>
                        `);
            })
        }
    }
    setInterval(loop_load_new_reply, 5e3);

    function show_fast_reply_modal() {
        $.get('{{ route('huashu.index') }}',{_token:'{{ @csrf_token() }}'}, function(data){
            $('#fast-reply-modal-content').html(data);
            $('#fast_reply_modal').modal('show', {backdrop: 'static'});
        });
    }
</script>
