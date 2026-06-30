@extends('seller.layouts.app')

@section('panel_content')
    <style>
        .seller-conversation-page {
            max-width: 1180px;
            margin: 12px auto 18px;
        }

        .seller-conversation-card {
            height: calc(100dvh - 132px);
            min-height: 480px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e6eaf0;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .seller-conversation-card .card-header {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
        }

        .conversation-title {
            min-width: 0;
        }

        .conversation-title h5 {
            color: #0f172a;
            line-height: 1.3;
        }

        .conversation-title .conversation-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
        }

        .seller-conversation-card .card-body {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: hidden;
            background: #f8fafc;
        }

        #messages {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 18px;
            scroll-behavior: smooth;
        }

        #messages .block-comment {
            margin-bottom: 14px !important;
        }

        #messages .avatar {
            width: 36px;
            height: 36px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }

        #messages .flex-grow-0 {
            max-width: min(74%, 720px) !important;
        }

        #messages .p-3.rounded {
            padding: 11px 14px !important;
            border-radius: 14px !important;
            line-height: 1.55;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        #messages .justify-content-end .p-3.rounded {
            border-bottom-right-radius: 4px !important;
        }

        #messages .d-flex:not(.justify-content-end) .p-3.rounded {
            border-bottom-left-radius: 4px !important;
        }

        .conversation-reply-form {
            flex: 0 0 auto;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid #e6eaf0;
            backdrop-filter: blur(18px);
        }

        .conversation-reply-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .conversation-reply-row textarea {
            min-height: 44px;
            max-height: 120px;
            resize: vertical;
            border-radius: 10px;
            line-height: 1.5;
        }

        .conversation-reply-row .form-group {
            flex: 1 1 auto;
            min-width: 0;
            margin-bottom: 0;
        }

        .conversation-reply-row .btn {
            height: 44px;
            min-width: 86px;
            flex: 0 0 auto;
        }

        @media (max-width: 767.98px) {
            .seller-conversation-page {
                margin-left: -12px;
                margin-right: -12px;
                margin-top: 8px;
                margin-bottom: 0;
            }

            .seller-conversation-card {
                height: calc(100dvh - 72px);
                min-height: 0;
                border-radius: 0;
                border-left: 0;
                border-right: 0;
            }

            .seller-conversation-card .card-header {
                padding: 12px;
                align-items: flex-start;
            }

            .conversation-title h5 {
                font-size: 14px !important;
            }

            #messages {
                padding: 14px 12px;
            }

            #messages .avatar {
                width: 32px;
                height: 32px;
            }

            #messages .flex-grow-0 {
                max-width: calc(100vw - 110px) !important;
            }

            #messages .p-3.rounded {
                font-size: 13.5px;
                padding: 10px 12px !important;
            }

            .conversation-reply-form {
                padding: 8px 10px calc(8px + env(safe-area-inset-bottom));
            }

            .conversation-reply-row {
                gap: 8px;
            }

            .conversation-reply-row textarea {
                min-height: 40px;
                max-height: 96px;
                padding-top: 9px;
                padding-bottom: 9px;
            }

            .conversation-reply-row .btn {
                height: 40px;
                min-width: 66px;
                padding-left: 12px;
                padding-right: 12px;
            }
        }
    </style>

    <div class="seller-conversation-page">
    <div class="card seller-conversation-card">
        <div class="card-header">
            <div class="conversation-title">
                <h5 class="card-title fs-16 fw-600 mb-0">#{{ $conversation->title }}</h5>
                <div class="conversation-meta">
                    <span>{{ translate('Conversations With ')}}</span>
                    <span>
                        @if ($conversation->sender_id == Auth::user()->id)
                            {{ $conversation->receiver->name }}
                        @else
                            {{ $conversation->sender->name }}
                        @endif
                    </span>
                    @if ($conversation->sender_id == Auth::user()->id && $conversation->receiver->shop != null)
                        <a href="{{ route('shop.visit', $conversation->receiver->shop->slug) }}">{{ $conversation->receiver->shop->name }}</a>
                    @endif
                </div>
            </div>
            @if($product_url)
                <button type="button" class="btn btn-sm btn-primary flex-shrink-0" onclick="window.location.href='{{$product_url}}'">{{translate('View conversation products')}}</button>
            @endif
        </div>

        <div class="card-body">
            <div id="messages">
                @include('frontend.partials.messages', ['conversation' => $conversation])
            </div>
            <form class="conversation-reply-form" action="{{ route('seller.conversations.message_store') }}" method="POST" id="reply_form">
                @csrf
                <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                <div class="conversation-reply-row">
                    <div class="form-group">
                        <textarea class="form-control" rows="1" name="message" placeholder="{{ translate('Type your reply') }}" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    var lastMessageCount = $('#messages .block-comment').length;

    function scrollToBottom(force) {
        var el = document.getElementById('messages');
        if (!el) return;
        if (force === false) {
            var distance = el.scrollHeight - el.scrollTop - el.clientHeight;
            if (distance > 140) return;
        }
        window.requestAnimationFrame(function() {
            el.scrollTop = el.scrollHeight;
        });
    }

    function refresh_messages(){
        var el = document.getElementById('messages');
        var shouldKeepBottom = true;
        if (el) {
            shouldKeepBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 160;
        }
        $.post('{{ route('seller.conversations.refresh') }}', {_token:'{{ @csrf_token() }}', id:'{{ encrypt($conversation->id) }}'}, function(data){
            $('#messages').html(data);
            var newCount = $('#messages .block-comment').length;
            if (newCount > lastMessageCount) {
                playNotificationSound();
                scrollToBottom(true);
            } else if (shouldKeepBottom) {
                scrollToBottom(true);
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

    $(window).on('load', function() {
        scrollToBottom(true);
        setTimeout(function() { scrollToBottom(true); }, 250);
    });

    $(document).ready(function() {
        scrollToBottom(true);
        setTimeout(function() { scrollToBottom(true); }, 100);
    });

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
            setTimeout(function() { scrollToBottom(true); }, 150);
        }.bind(this)).fail(function() {
            submitting = false;
        });
    });
    </script>
@endsection
