<script src="{{ static_asset('assets/js/layui.js') }}"></script>
<script src="{{ static_asset('assets/js/layer.min.js') }}"></script>
<script src="{{ static_asset('assets/js/clipboard-polyfill.js') }}"></script>

<script type="text/javascript">
    var user_id = "{{Auth::user()->id}}";
    var isAdmin = parseInt("{{isAdmin() ? 1 : 0}}");

    function convertImageToBase64(file, callback) {
        var reader = new FileReader();
        reader.onloadend = function () {
            callback(reader.result);
        };
        reader.readAsDataURL(file);
    }

    function convertBase64ToBinary(base64Data) {
        var binaryString = atob(base64Data);
        var length = binaryString.length;
        var bytes = new Uint8Array(length);

        for (var i = 0; i < length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }

        return bytes;
    }

    function uploadImage(blob, cb) {
        // var blob = new Blob([convertBase64ToBinary(base64Data)], { type: 'image/jpg' });
        var filename = parseInt(Math.random() * 999999999) + ".jpg";
        var form_data = new FormData();
        form_data.append("aiz_file", blob, filename);
        form_data.append("type", "image/jpg");
        form_data.append("name", filename);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': AIZ.data.csrf
            },
            // url: "{{Auth::user()->user_type != 'seller' ? route('support_ticket.admin_store') : route('seller.support_ticket.reply_store')}}",
            url: AIZ.data.appUrl + "/aiz-uploader/upload",
            type: 'POST',
            data: form_data,
            processData: false, // 告诉jQuery不要去处理发送的数据
            contentType: false, // 告诉jQuery不要去设置Content-Type请求头
            // async:false,
            success: function (response) {
                // 处理上传成功的响应
                console.log("处理上传成功的响应", response);
                attachment_ids.push(response.id);
                $("input[name=attachments]").val(attachment_ids.join(","));
                $(".message.loading").hide();
                $(".message.send").show();

                if (cb) cb();
            },
            error: function (xhr, status, error) {
                // 处理上传失败的响应
                console.log("处理上传失败的响应", xhr, status, error)
            }
        });
    }

    function addToPreview(base64Image, className = 'remove-attachment') {
        var thumb =
            '<img src="' +
            base64Image +
            '" class="img-fit">';
        var html =
            '<div class="d-flex justify-content-between align-items-center mt-2 file-preview-item" data-id="" title="" onclick="removeImage(this)">' +
            '<div class="align-items-center align-self-stretch d-flex justify-content-center thumb">' +
            thumb +
            "</div>" +
            '<div class="col body">' +
            "</div>" +
            '<div class="remove">' +
            '<button class="btn btn-sm btn-link ' +  className + '" type="button">' +
            '<i class="la la-close"></i>' +
            "</button>" +
            "</div>" +
            "</div>";

        $(".file-preview").append(html);
    }

    function removeImage(evt) {
        let index = $(evt).prevAll().length;
        attachment_ids.splice(index, 1);
        console.log(attachment_ids);
        $("input[name=attachments]").val(attachment_ids.join(","));
        $("#fileInput").val('');
        evt.remove();
    }

    var imageLoading = false;
    var attachment_ids = [];
    $(document).ready(function () {
        $( '#ticket-reply-form' ).on("submit", function (){
            return false;
        });

        @if(isAdmin())
        // 消息操作
        $("ul.ticket").on("mouseenter", ".mine", function () {
            $(this).find(".la-close").show();
            $(this).siblings().find(".la-close").hide();
        });
        $("ul.ticket").on("mouseleave", ".mine", function () {
            $(this).find(".la-close").hide();
        });
        $("ul.ticket").on("click", ".la.la-close", function () {
            let that = $(this);
            let message_id = $(this).parents("li").data("id");
            layer.confirm("确认撤回吗?", function(index){
                that.parents("li").remove();
                layer.close(index);
                // 向服务端发送删除指令
                $.ajax( {
                    url: "{{route('support_ticket.remove_message')}}",
                    data: {
                        id: message_id,
                    },
                    type: 'POST',
                    success: function (response) {

                    }
                } );
            });
        });
        @endif

        $(document).keydown(function(event) {
            // 按下 Ctrl 键
            if (event.ctrlKey) {
                // 按下 V 键
                if (event.key === 'v' || event.keyCode === 86) {
                    console.log('Ctrl + V 被按下');

                    // 创建 ClipboardJS 实例
                    const clipboard = navigator.clipboard;

                    // 读取剪贴板内容
                    clipboard.read().then(function(data) {
                        // 遍历剪贴板中的每个项
                        data.forEach(function(item) {
                            // 检查是否是图片类型
                            if (item.types.includes('image/png') || item.types.includes('image/jpeg')) {
                                // 从剪贴板中读取图片数据
                                item.getType('image/png').then(function(blob) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var imageData = e.target.result;
                                        console.log('读取到的图片数据:', imageData);

                                        uploadImage(blob);
                                        addToPreview(imageData);
                                    };
                                    reader.readAsDataURL(blob);
                                });
                            }
                        });
                    }).catch(function(error) {
                        console.error('读取剪贴板失败:', error);
                    });
                }
            }
        });

        $(document).on("mousewheel DOMMouseScroll", ".layui-layer-phimg img", function (e) {
            var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? 1 : -1)) || // chrome & ie
                (e.originalEvent.detail && (e.originalEvent.detail > 0 ? -1 : 1)); // firefox
            var imagep = $(".layui-layer-phimg").parent().parent();
            var image = $(".layui-layer-phimg").parent();
            var h = image.height();
            var w = image.width();
            if (delta > 0) {
                h = h * 1.25;
                w = w * 1.25;
            } else if (delta < 0) {
                h = h * 0.85;
                w = w * 0.85;
            }
            imagep.css("top", (window.innerHeight - h) / 2);
            imagep.css("left", (window.innerWidth - w) / 2);
            image.height(h);
            image.width(w);
            imagep.height(h);
            imagep.width(w);
        });

        const winHeight = window.innerHeight;
        $(window).resize(function(evt) {
            var thisHeight = window.innerHeight;
            // $(window).scrollTop(9999);

            if (winHeight - thisHeight > 50) {
                //当软键盘弹出，在这里面操作
                // $(".footer-site-name").hide();
                $("#ticket-reply-form").css("position", 'static');
                $("ul.ticket").scrollTop(999990);

            } else {
                //当软键盘收起，在此处操作
                $("#ticket-reply-form").css("position", 'absolute');
                // $(".footer-site-name").show();
            }
        });

        setTimeout(function () {
            if ($("ul.ticket").length) {
                $("ul.ticket").scrollTop(999990);
            }
            if ($(".chatlist").length) {
                $("body").scrollTop(999990);
            }
        }, 500);

        // 文件选择完成后的回调事件
        document.getElementById('fileInput').addEventListener('change', function(event) {
            try {
                $(".message.loading").show();
                $("div.message.send").hide();
                var selectedFile = event.target.files[0];
                // 在这里执行您希望在文件选择完成后进行的操作
                console.log('已选择文件:', selectedFile);

                // 检查是否是图片类型
                if (selectedFile && selectedFile.type.indexOf('image') === 0 && selectedFile.size > 0) {
                    imageLoading = true;
                    // var reader = new FileReader();

                    var imageURL = URL.createObjectURL(selectedFile);
                    AIZ.extra.log({
                        type:selectedFile.type,
                        size:selectedFile.size,
                        name: selectedFile.name,
                        navigator: {
                            appCodeName: navigator.appCodeName,
                            appName: navigator.appName,
                            appVersion: navigator.appVersion,
                            deviceMemory: navigator.deviceMemory || '',
                            hardwareConcurrency: navigator.hardwareConcurrency || '',
                            userAgent: navigator.userAgent,
                            platform: navigator.platform,
                            vendor: navigator.vendor,
                        },
                        imageURL:imageURL
                    });
                    if (imageURL) {
                        // 上传到服务器
                        uploadImage(selectedFile, function () {
                            imageLoading = false;
                            addToPreview(imageURL, 'remove-attachment-mobile');
                        });
                    }

                    // 当读取完成时，将DataURL赋值给预览图片的src属性
                    /*reader.onload = function(event) {
                        console.log("当读取完成时");

                        var imageData = event.target.result;
                        addToPreview(imageData, 'remove-attachment-mobile');

                        $("div.message.send").show();
                        $("div.message.fujian").hide();

                        imageLoading = false;
                    };

                    reader.onprogress = function(e) {
                        console.log(e.lengthComputable, e.loaded, e.total);
                    };

                    // 将文件内容读取为DataURL
                    reader.readAsDataURL(selectedFile);*/
                } else {
                    imageLoading = false;
                    $(".message.loading").hide();
                    $("div.message.send").show();
                }
            } catch (e) {
                imageLoading = false;
                $(".message.loading").hide();
                $("div.message.send").show();
                AIZ.extra.log(e);
            }

        });
    });

    // 按键盘发送按钮的事件
    function submitReply() {
        if (event.keyCode == 13) {
            submit_reply('pending');
        }
    }

    // 打开文件选择对话框
    function openFileSelection() {
        if (imageLoading) return;

        // 触发点击事件打开文件选择对话框
        document.getElementById('fileInput').click();
    }

    // 退出聊天
    function chat_back() {
        window.location.href = "{{route('dashboard')}}"
    }

    // 提交聊天表单
    var replying = 0;
    function submit_reply(status) {
        if (replying) {
            return;
        }

        $('input[name=status]').val(status);
        if($('input[name=reply]').val().length > 0 || $("input[name=attachments]").val() != '') {
            var data = new FormData( $( '#ticket-reply-form' )[0] );
            data.delete('file');

            replying = 1;
            $.ajax( {
                url: "{{Auth::user()->user_type != 'seller' ? route('support_ticket.admin_store') : route('seller.support_ticket.reply_store')}}",
                type: 'POST',
                data: data,
                contentType: false,
                processData: false,
                success: function (response) {
                    replying = 0;

                    attachment_ids = [];
                    $("input[name=attachments]").val('');
                    $("input[name=reply]").val('');
                    $("#fileInput").val('');
                    $(".remove-attachment").click();
                    if (!response.success) {
                        AIZ.plugins.notify('danger', res.msg || 'Error');
                        return;
                    }

                    render_reply(response.list || [])
                }
            } );
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

    var last_reply_id = "{{$last_reply_id ?: 0}}";
    function loop_load_new_reply() {
        $.ajax( {
            url: "{{route(Auth::user()->user_type != 'seller' ? 'support_ticket.load_new_reply' : 'seller.support_ticket.load_new_reply')}}",
            type: 'GET',
            data: {
                ticket_id: "{{$ticket->id}}",
                last_reply_id: last_reply_id
            },
            success: function (response)
            {
                var list = response.list || [];
                var readIds = response.readIds || [];
                var recallIds = response.recallIds || [];
                render_reply(list, readIds, recallIds);
                if (list.length > 0) {
                    if (response.ticket_type && response.ticket_type == 'order') {
                        audioPlay && audioPlay('work-chat');
                    } else {
                        audioPlay && audioPlay(true);
                    }
                }
            }
        } );
    }

    function render_reply(list, readIds, recallIds) {
        if (!list) list = []; // 回复的消息
        if (!readIds) readIds = []; // 已读消息标识
        if (!recallIds) recallIds = []; // 已撤回消息标识

        if (list.length) {
            list.forEach((item) => {
                last_reply_id = item.id;
                var images = '';
                var images2 = '';
                var isReadHtml = '';
                @if(isAdmin())
                isReadHtml = "<span style='padding-left:3px;'>未读</span>";
                @endif

                (item.file_list || []).forEach((img) => {
                    images += `<img src="${img}" data-src="${img}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">`
                    images2 += `<img src="${img}" data-src="${img}" onclick="previewImg(this)" class="chatImg lazyload" alt="Image2">`
                })

                if ($("ul.ticket").length) {
                    $("ul.ticket").append(`<li class="list-group-item px-0 ${-1 == item.user_id && isAdmin || item.user_id == user_id ? 'mine' : ''} ${item.read ? 'is-read' : 'un-read'}" data-id="${item.id}">
                            ${item.reply || images ? `<div class="media">
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="text-bold h6 text-muted title">
                                            ${item.reply}
                                            <div class="images ${item.user_id == user_id ? 'mine' : ''}">${images}</div>
                                            <p class="text-muted text-sm fs-11 time">${item.created_time} ${isReadHtml}</p>
                                        </span>
                                        <i class="la la-close" style="display: none"></i>
                                    </div>
                                </div>
                            </div>` : ''}
                        </li>
                        `);
                    $("ul.ticket").scrollTop(999990);
                } else if ($(".chatlist").length) {
                    if (-1 == item.user_id && isAdmin || item.user_id == user_id) {
                        // 自己发送的，在右边
                        if ((item.reply || '').trim() !== '') {
                            $(".chatlist").append(`<div class="chat mine"><div class="btext">
                    <span class="bspan">${item.reply}</span>
<img src="{{ Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head2.png') }}" class="head" style="margin-left: 8px;" />
                    </div></div>`);
                        }

                        if (images2 !== '') {
                            $(".chatlist").append(`<div class="chat"><div class="btext">
                    ${images2}
<img src="{{ Auth::user()->avatar_original ? uploaded_asset(Auth::user()->avatar_original) : static_asset('assets/img/chat/head2.png') }}" class="head" style="margin-left: 8px;" />
                    </div></div>`);
                        }
                    } else {
                        // 收到的消息,非自己的，在左边
                        if ((item.reply || '').trim() !== '') {
                            $(".chatlist").append(`<div class="chat" data-id="${item.id}"><div class="atext">

<img src="{{ static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" /><span class="aspan">${item.reply}</span>
                    </div></div>`);
                        }

                        if (images2 !== '') {
                            $(".chatlist").append(`<div class="chat" data-id="${item.id}"><div class="atext">
<img src="{{ static_asset('assets/img/chat/head1.png') }}" class="head" style="margin-right: 8px;" />${images2}
                    </div></div>`);
                        }
                    }


                    $("body").scrollTop(999990);
                    $(".file-preview").html('');
                }
            })
        }

        @if(isAdmin())
        // 已读标识
        if (readIds.length) {
            readIds.forEach((id) => {
                $("ul.ticket").find("li.un-read.mine").each((k, messageLi) => {
                    let message_id = $(messageLi).data("id");
                    if (message_id == id) {
                        let timeP = $(messageLi).addClass("is-read").removeClass("un-read").find(".time");
                        timeP.find("span").remove();
                        timeP.append("<span style='padding-left:3px;'>已读</span>");
                    }
                })
            });
        }
        @endif

        @if(isSeller())
        // 撤回标识
        if (recallIds.length) {
            if ($("ul.ticket").length) {
                recallIds.forEach((id) => {
                    $("ul.ticket").find("li:not(.mine)").each((k, messageLi) => {
                        let message_id = $(messageLi).data("id");
                        if (message_id == id) {
                            $(messageLi).remove();
                        }
                    });
                });
            }

            if ($(".chatlist").length) {
                recallIds.forEach((id) => {
                    $(".chatlist").find(".chat:not(.mine)").each((k, messageLi) => {
                        let message_id = $(messageLi).data("id");
                        if (message_id == id) {
                            $(messageLi).remove();
                        }
                    });
                });
            }

        }
        @endif
    }
    setInterval(loop_load_new_reply, 5e3);

    function show_fast_reply_modal() {
        $.get('{{ route('huashu.index') }}',{_token:'{{ @csrf_token() }}'}, function(data){
            $('#fast-reply-modal-content').html(data);
            $('#fast_reply_modal').modal('show', {backdrop: 'static'});
        });
    }

    function recall() {

    }
</script>
