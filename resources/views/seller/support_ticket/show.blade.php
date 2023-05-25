@extends('seller.layouts.app')
<style type="text/css">
    div.aiz-main-content {
        width: 80%;
        margin: auto;
    }
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
                            @if(!empty($ticketreply->reply))
                            <div class="media">
                                <div class="media-body">
                                    <div class="comment-header">
                                        <span class="text-bold h6 text-muted title">
                                            @php echo $ticketreply->reply; @endphp
                                            @if($ticketreply->files)
                                                <div class="images {{$ticketreply->user->id == Auth::id() ? 'mine' : ''}}">
                                                @foreach ((explode(",",$ticketreply->files)) as $key => $file)
                                                    @php $file_detail = \App\Models\Upload::where('id', $file)->first(); @endphp
                                                    @if($file_detail != null)
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($file) }}" onclick="previewImg(this)" class="mr-3 lazyload size-100px img-fit rounded" alt="Image">
                                                    @endif
                                                @endforeach
                                            </div>
                                            @endif
                                            <p class="text-muted text-sm fs-11 time">{{date('m-d H:i', strtotime($ticketreply->created_at))}}</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <form action="{{ route('seller.support_ticket.reply_store') }}" method="post" id="ticket-reply-form" enctype="multipart/form-data">
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
                        <div class="col-md-10">
                            <input class="form-control" type="text" name="reply" value="" />
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="submit_reply('pending')">{{ translate('Send Reply') }}</button>
                        </div>
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
