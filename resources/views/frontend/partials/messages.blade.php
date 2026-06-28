@foreach ($conversation->messages as $key => $message)
    @php
        $msg_user = $message->user;
        $is_mine = $message->user_id == Auth::user()->id;
        $user_name = $msg_user ? $msg_user->name : '';
        $avatar = $msg_user && $msg_user->avatar_original ? uploaded_asset($msg_user->avatar_original) : static_asset('assets/img/avatar-place.png');
    @endphp
    @if ($is_mine)
        {{-- 自己发的消息，靠右 --}}
        <div class="block block-comment mb-3">
            <div class="d-flex justify-content-end">
                <div class="flex-grow-0" style="max-width: 75%;">
                    <div class="d-flex justify-content-end align-items-center mb-1">
                        <small class="text-muted mr-2">{{ date('m-d H:i', strtotime($message->created_at)) }}</small>
                        <span class="fw-600">{{ $user_name }}</span>
                    </div>
                    <div class="p-3 rounded" style="background-color: #d9fdd3; word-break: break-word;">
                        {{ $message->message }}
                    </div>
                </div>
                <div class="ml-2 flex-shrink-0">
                    <img class="avatar avatar-xs rounded-circle" src="{{ $avatar }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                </div>
            </div>
        </div>
    @else
        {{-- 对方发的消息，靠左 --}}
        <div class="block block-comment mb-3">
            <div class="d-flex">
                <div class="mr-2 flex-shrink-0">
                    <img class="avatar avatar-xs rounded-circle" src="{{ $avatar }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                </div>
                <div class="flex-grow-0" style="max-width: 75%;">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-600">{{ $user_name }}</span>
                        <small class="text-muted ml-2">{{ date('m-d H:i', strtotime($message->created_at)) }}</small>
                    </div>
                    <div class="p-3 rounded" style="background-color: #f1f1f1; word-break: break-word;">
                        {{ $message->message }}
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
