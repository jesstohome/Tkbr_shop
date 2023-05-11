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
                    audioPlay && audioPlay();

                    $(".chat-num-tip").html(response.count).show();
                }
            }
        } );
    }
    $(document).ready(function () {
        setInterval(check_unread, 30e3)
    })
</script>
