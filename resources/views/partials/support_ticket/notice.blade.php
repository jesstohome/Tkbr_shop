<script>
    function check_unread() {
        $.ajax( {
            url: "{{route(Auth::user()->user_type != 'seller' ? 'support_ticket.load_new_reply' : 'seller.support_ticket.load_new_reply')}}",
            type: 'GET',
            data: {
                check: 1
            },
            success: function (response)
            {
                if (response.count > 0) {
                    AIZ.plugins.notify('info', "{{translate('You have a new job message')}}");

                    $(".chat-num-tip").html(response.count).show();
                }
            }
        } );
    }

    // 只播放有新消息的声音
    function loop_load_new_reply_audio() {
        $.ajax( {
            url: "{{route(Auth::user()->user_type != 'seller' ? 'support_ticket.load_new_reply' : 'seller.support_ticket.load_new_reply')}}" + "?check=2",
            type: 'GET',
            data: {
                ticket_id: "{{$ticket ? $ticket->id : ''}}",
            },
            success: function (response)
            {
                var count = response.count || 0;
                if (count > 0) {
                    $(".chat-num-tip").html(count).show();
                }
            }
        } );
    }

    $(document).ready(function () {
        setInterval(check_unread, 10e3);

        @if(empty($in_chat_page))
        setInterval(loop_load_new_reply_audio, 5e3);
        @endif
    })
</script>
