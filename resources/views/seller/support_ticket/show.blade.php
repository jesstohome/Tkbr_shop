@extends('seller.layouts.app')
<style type="text/css">
    div.footer-site-name {
        display: none;
    }
    ul.ticket {
        height: calc(100% - 118px);
        overflow-y: scroll;
        margin-top: 75px;
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
        height: 100%;
        margin-bottom: 0;
    }

    .card .card-header {
        position: absolute !important;
        min-height:auto;
        top: 0;
        z-index: 10;
        left: 0;
        right: 0;
        background-color: white;
        height: 75px;
    }
    .card .card-body {
        /*padding: 0 !important;*/
    }
    #ticket-reply-form {
        position: absolute;
        height: 43px;
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
                            <svg t="1684411791031" onclick="submit_reply('pending')" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="18723" width="20" height="20"><path d="M512 512m-448 0a448 448 0 1 0 896 0 448 448 0 1 0-896 0Z" fill="#608BE9" p-id="18724"></path><path d="M192 302m32 0l576 0q32 0 32 32l0 356q0 32-32 32l-576 0q-32 0-32-32l0-356q0-32 32-32Z" fill="#EAEDF5" p-id="18725"></path><path d="M224 722h576c17.673 0 32-14.327 32-32v-58C660.96 493.333 554.294 424 512 424c-42.294 0-148.96 69.333-320 208v58c0 17.673 14.327 32 32 32z" fill="#CCDAF7" p-id="18726"></path><path d="M224 302h576c17.673 0 32 14.327 32 32v58C651.35 517.333 544.683 580 512 580c-32.683 0-139.35-62.667-320-188v-58c0-17.673 14.327-32 32-32z" fill="#FFFFFF" p-id="18727"></path></svg>
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
            $(".card.chat").height(document.body.clientHeight - 75)
            $(".aiz-main-content").height(document.body.clientHeight - 75)
        });
    </script>
@endsection
