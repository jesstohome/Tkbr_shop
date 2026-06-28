@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="h6">
            <span>{{ translate('Conversations With ')}}</span>
            @if ($conversation->sender_id == Auth::user()->id && $conversation->receiver->shop != null)
                <a href="{{ route('shop.visit', $conversation->receiver->shop->slug) }}" class="">{{ $conversation->receiver->shop->name }}</a>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title fs-16 fw-600 mb-0">#{{ $conversation->title }}
            (
                @if ($conversation->sender_id == Auth::user()->id)
                    {{ $conversation->receiver->name }}
                @else
                    {{ $conversation->sender->name }}
                @endif
            )
            </h5>
        </div>
        <div class="m-auto" style="margin-top: 10px !important;">
            @if($product_url)
                <button type="button" class="btn btn-primary" onclick="window.location.href='{{$product_url}}'">{{translate('View conversation products')}}</button>
            @endif
        </div>

        <div class="card-body">
            <div id="messages">
                @include('frontend.partials.messages', ['conversation' => $conversation])
            </div>
            <form class="pt-4" action="{{ route('seller.conversations.message_store') }}" method="POST" id="reply_form">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div class="form-group">
                    <textarea class="form-control" rows="4" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    var lastMessageCount = $('#messages .block-comment').length;

    function scrollToBottom() {
        var el = document.getElementById('messages');
        if (el) el.scrollTop = el.scrollHeight;
    }

    function refresh_messages(){
        $.post('{{ route('seller.conversations.refresh') }}', {_token:'{{ @csrf_token() }}', id:'{{ encrypt($conversation->id) }}'}, function(data){
            $('#messages').html(data);
            var newCount = $('#messages .block-comment').length;
            if (newCount > lastMessageCount) {
                playNotificationSound();
                scrollToBottom();
            }
            lastMessageCount = newCount;
        });
    }

    function playNotificationSound() {
        try {
            var audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            var oscillator = audioCtx.createOscillator();
            var gainNode = audioCtx.createGain();
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            gainNode.gain.value = 0.3;
            oscillator.start();
            setTimeout(function() { oscillator.stop(); }, 200);
        } catch(e) {}
    }

    setInterval(function(){
        refresh_messages();
    }, 4000);

    var submitting = false;
    $('#reply_form').on('submit', function(e) {
        e.preventDefault();
        if (submitting) return false;
        var msg = $(this).find('textarea[name=message]').val().trim();
        if (!msg) return false;
        submitting = true;
        $.post('{{ route('seller.conversations.message_store') }}', {
            _token: '{{ @csrf_token() }}',
            conversation_id: '{{ $conversation->id }}',
            message: msg
        }, function() {
            $(this).find('textarea[name=message]').val('');
            submitting = false;
            refresh_messages();
        }.bind(this)).fail(function() {
            submitting = false;
        });
    });
    </script>
@endsection
