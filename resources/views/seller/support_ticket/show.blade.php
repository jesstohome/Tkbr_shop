@extends('seller.layouts.app')
<style type="text/css">
    div.footer-site-name {
        display: none;
    }
    ul.ticket {
        max-height: 60vh;
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
        height: calc(100vh - 75px - 25px - 50px);
        /*height: calc(100vh - 75px);*/
        margin-bottom: 0;
    }

    .card .card-body {
        /*padding: 0 !important;*/
    }
    #ticket-reply-form {
        position: absolute;
        bottom: 0;
        right: 15px;
        left: 15px;
    }

    div.input-box {
        background-color: beige;
        align-items: center;
    }

    div.input-box svg {
        width: 30px;
        height: 30px;
    }

    div.input-box svg.upload {
        margin-left:10px;
    }

    div.file-preview {
        position: fixed;
        bottom: 10vh;
        width: 80vw;
        z-index: 9999;
        display: block;
        background-color: white;
        border-radius: 3px;
    }
</style>
@section('panel_content')
    <div class="card chat">
        <div class="card-header row gutters-5">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h5">Tictok Shop Serve</h5>
               <div class="mt-2">
                   <span> {{ $ticket->user->name }} </span>
               </div>
            </div>
        </div>
        <div class="card-body msg-box" style="padding: 0!important;">
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
                            <div class="images {{$ticketreply->user->id == Auth::id() ? 'mine' : ''}}">
                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                    @php $file_detail = \App\Models\Upload::where('id', $file)->first(); @endphp
                                    @if($file_detail != null)
                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
                <form id="ticket-reply-form" action="{{route('seller.support_ticket.reply_store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                    <input type="hidden" name="user_id" value="{{$ticket->user_id}}">

                    <!--
                    <div class="form-group row">
                        <div class="col-md-12">

                            <div class="file-preview box sm"></div>
                        </div>
                    </div>
                    -->

                    <div class="row input-box">
                        <div class="col-2">
                            <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <svg t="1683950093693" class="icon upload" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2377" width="20" height="20">
                                    <path d="M346.450312 1023.992961a355.281147 355.281147 0 0 1-245.151671-95.987342 317.590118 317.590118 0 0 1 0-465.218649l417.096996-395.659822a264.093173 264.093173 0 0 1 351.249679 0 227.234034 227.234034 0 0 1 0 333.26805l-417.096996 395.787805a159.978903 159.978903 0 0 1-212.196016 0 137.32589 137.32589 0 0 1 0-201.381442l417.096995-395.851798a53.752911 53.752911 0 0 1 73.142354 0 47.289764 47.289764 0 0 1 0 69.366853L313.494658 664.232404a42.490397 42.490397 0 0 0 0 62.391772 48.313629 48.313629 0 0 0 65.847316 0l417.096996-395.59583a132.846481 132.846481 0 0 0 0-194.534346 150.700126 150.700126 0 0 0-204.772996 0L174.568979 532.217814a222.818616 222.818616 0 0 0 0 326.356961 252.89465 252.89465 0 0 0 343.954641 0l417.096995-395.723813a53.68892 53.68892 0 0 1 73.142354 0 47.289764 47.289764 0 0 1 0 69.366852l-417.096995 395.787805a355.153164 355.153164 0 0 1-245.215662 95.987342z" fill="#8F9BB3" p-id="2378"></path>
                                </svg>
                                <input type="hidden" name="attachments" class="selected-files">
                            </div>
                            <div class="file-preview box sm"></div>
                        </div>
                        <div class="col-8">
                            <input class="form-control" type="text" name="reply" value="" required />
                        </div>
                        <div class="col-2">
                            <svg t="1683950684312" class="icon" onclick="submit_reply('pending')" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8324" width="20" height="20"><path d="M211.649242 813.217191a425.984 425.984 0 1 0 602.421836-602.442865 425.984 425.984 0 1 0-602.421836 602.442865Z" fill="#00A0E9" p-id="8325"></path><path d="M266.3936 427.7248l422.5024-103.5776c20.4288-5.0176 37.5296 15.9744 28.4672 34.9696l-188.2112 395.1616c-9.728 20.3776-39.3728 18.432-46.2848-3.072l-48.0256-149.4016a25.06752 25.06752 0 0 1 5.2224-24.3712L522.1888 486.4c5.0176-5.5808-1.6896-13.9264-8.192-10.0864l-108.9024 63.5392a24.9856 24.9856 0 0 1-24.6272 0.3072L260.2496 473.8048c-19.8656-10.9568-15.9232-40.6528 6.144-46.08z" fill="#FFFFFF" p-id="8326"></path></svg>
                        </div>
                        <!-- <textarea class="aiz-text-editor" name="reply" data-buttons='[]' required></textarea> -->

                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
@section('script')
    @include('partials.support_ticket.script')

    <script type="text/javascript">
        $(document).ready(function () {
            // $("ul.ticket").height(document.documentElement.clientHeight - 75 - 20)
        });
    </script>
@endsection
