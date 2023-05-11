@extends('backend.layouts.app')
<style type="text/css">
    ul.ticket {
        max-height: 50vh;
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
    .card .card-body {
        padding: 20px 10px;
        padding-bottom: 5px;
    }
</style>
@section('content')

<div class="col-lg-10 mx-auto">
    <div class="card">
        <div class="card-header row gutters-5">
            <div class="text-center text-md-left">
                <h5 class="mb-md-0 h5">{{ $ticket->user->email }}</h5>
               <div class="mt-2">
                   <span> {{ $ticket->user->name }} </span>
                   <span class="ml-2"> {{ $ticket->user->created_at }} </span>
               </div>
            </div>
        </div>
        <div class="card-body">
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
            </div>

            <form action="{{ route('support_ticket.admin_store') }}" method="post" id="ticket-reply-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ticket_id" value="{{$ticket->id}}" required>
                <input type="hidden" name="status" value="{{ $ticket->status }}" required>

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
                    <div class="col-md-8">
                        <input class="form-control" type="text" name="reply" value="" required />
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-sm btn-primary" onclick="show_fast_reply_modal()">{{ translate('Select Fast Reply') }}</button>
                        <button type="submit" class="btn btn-sm btn-primary" onclick="submit_reply('pending')">{{ translate('Send Reply') }}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('modal')
    <!-- fast reply Modal -->
    <div class="modal fade" id="fast_reply_modal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="fast-reply-modal-content">

            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('partials.support_ticket.script')
@endsection
