@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" id="sort_support" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Support Desk') }}</h5>
            </div>
            <div class="col-md-4 ml-auto">
                <select class="form-control aiz-selectpicker" name="group" id="group" onchange="sort_support()">
                    <option value="">{{translate('All')}}</option>
                    @foreach($groups as $group_val => $_group)
                    <option value="{{$group_val}}"  @isset($group) @if($group == $group_val) selected @endif @endisset>{{$_group}} ({{filter_by_bloc(\App\Models\Ticket::query()->where("group", $group_val)->where("type", "service"))->count() . ' ' . translate('Peoples')}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type ticket code & Enter') }}">
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <table class="aiz-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th data-breakpoints="lg">{{ translate('Ticket ID') }}</th>
                    <th data-breakpoints="lg">{{ translate('Sending Date') }}</th>
                    <th>{{ translate('Subject') }}</th>
                    <th data-breakpoints="lg">{{ translate('Shop') }}</th>
                    <th data-breakpoints="lg">{{ translate('Email') }}</th>
                    <th data-breakpoints="lg">{{ translate('Group') }}</th>
                    <th data-breakpoints="lg">{{ translate('Last reply') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($tickets as $key => $ticket)
                    @if ($ticket->user != null)
                        <tr>
                            <td class="edit" data-ticket-id="{{$ticket->id}}">{{$ticket->tag_name ?: translate('Permanent Work Order')}}</td>
                            <td>{{ $ticket->created_at }} @if($ticket->viewed == 0) <span class="badge badge-inline badge-info">{{ translate('New') }}</span> @endif</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->user->shop ? $ticket->user->shop->name : '' }}</td>
                            <td>{{ $ticket->user->email }}</td>
                            <td>
                                <select class="form-control ticket-group" data-ticket-id="{{$ticket->id}}" onchange="change_group(this, {{$ticket->id}})">
                                    @foreach($groups as $group_val => $_group)
                                        <option value="{{$group_val}}"  @if($ticket->group == $group_val) selected @endif>{{$_group}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                @if (count($ticket->ticketreplies) > 0)
                                    {{ $ticket->ticketreplies->last()->created_at }}
                                @else
                                    {{ $ticket->created_at }}
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{route('support_ticket.admin_show', encrypt($ticket->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="clearfix">
            <div class="pull-right">
                {{ $tickets->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script>
    $(document).ready(function () {
        $("td.edit").on("click", function () {
            if ($(this).hasClass('editing')) return;

            $(this).addClass('editing');
            var that = $(this);
            var edittd = "<input id='remark-name' type='text' value='" + $(this).html() + "' />";
            $(this).html(edittd);
        });

        $("body").on("keypress", "#remark-name", function (event) {
            if (event.keyCode != 13) return;

            var curTd = $(this).parent("td");
            var tag_name = $("#remark-name").val() || '';
            curTd.removeClass('editing').html(tag_name);

            $.ajax( {
                headers: {
                    'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
                },
                url: "{{route('support_ticket.update_tag_name')}}",
                type: 'POST',
                data: {
                    id: curTd.data("ticket-id"),
                    tag_name: tag_name,
                },
                success: function (response)
                {
                    if ( response.success) {
                        AIZ.plugins.notify('success', '{{ translate('Successfully edited') }}');
                    }
                }
            } );
        });
    });

    function sort_support(el){
        $('#sort_support').submit();
    }

    function change_group(evt, ticket_id) {
        $.ajax( {
            headers: {
                'X-CSRF-TOKEN': $( 'meta[name="csrf-token"]' ).attr( 'content' )
            },
            url: "{{route('support_ticket.change_group')}}",
            type: 'POST',
            data: {
                id: ticket_id,
                group: $(evt).val(),
            },
            success: function (response)
            {
                if ( response.success) {
                    AIZ.plugins.notify('success', '{{ translate('Successfully edited') }}');
                }
            }
        } );
    }
    </script>
@endsection
