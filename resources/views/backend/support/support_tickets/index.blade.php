@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" id="sort_support" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Support Desk') }}</h5>
            </div>
            @include('backend.partials.filters.bloc_staff')
            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="group" id="group" onchange="sort_support()">
                    <option value="">{{translate('All')}}</option>
                    @foreach($groups as $group_val => $_group)
                    <option value="{{$group_val}}"  @isset($group) @if($group == $group_val) selected @endif @endisset>{{$_group}} ({{filter_by_bloc(\App\Models\Ticket::query()->where("group", $group_val)->where("type", "service"))->count() . ' ' . translate('Peoples')}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 ml-auto">
                <div class="col-sm-12">
                    <input type="text" class="form-control aiz-date-range" name="created_at" value="{{$created_at}}" placeholder="创建时间" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-separator=" to " autocomplete="off">
                </div>
            </div>
            <div class="col-md-2 ml-auto">
                <div class="col-sm-12">
                    <input type="text" class="form-control aiz-date-range" name="updated_at" value="{{$updated_at}}" placeholder="回复时间" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-separator=" to " autocomplete="off">
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Search by Email, Shop Name or Name') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
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
                    @if (isSupperAdmin())<th>{{ translate('Bloc') }}</th>@endif
                    <th>{{ translate('Staffs') }}</th>
                    <th data-breakpoints="lg">{{ translate('Shop') }}</th>
                    <th data-breakpoints="lg">{{ translate('Email') }}</th>
                    <th data-breakpoints="lg">{{ translate('Group') }}</th>
                    <th data-breakpoints="lg">{{ translate('Last reply') }}</th>
                    @if (isSupperAdmin() || isBlocManage())
                    <th data-breakpoints="lg">{{ translate('Salesman') }}</th>
                    @endif
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
            </thead>
            <tbody>
                    @foreach ($tickets as $key => $ticket)
                    <tr>
                        <td class="edit" data-ticket-id="{{$ticket->id}}">{{$ticket->tag_name ?: translate('Permanent Work Order')}}</td>
                        <td>{{ $ticket->created_at }} @if($ticket->viewed == 0) <span class="badge badge-inline badge-info">{{ translate('New') }}</span> @endif</td>
                        <td>{{ $ticket->subject }}</td>
                        @if (isSupperAdmin())<td>{{$ticket->bloc ? $ticket->bloc->name : ''}}</td>@endif
                        <td>{{$ticket->staff ? $ticket->staff->user->email : ''}}</td>
                        <td>{{ $ticket->user && $ticket->user->shop ? $ticket->user->shop->name : '' }}</td>
                        <td>{{ $ticket->user ? $ticket->user->email : ''}}</td>
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
                        @if (isSupperAdmin() || isBlocManage())
                        <td>
                            @php
                                $uid = $ticket->user->pid;
                                if( $uid == '')
                                {
                                   echo '---';
                                }
                                else
                                {
                                  $r =  \App\Models\User::where('id',$uid)->first() ;
                                 echo $r['name'];

                                }
                            @endphp
                        </td>
                        @endif
                        <td class="text-right">
                            <a href="{{route('support_ticket.admin_show', encrypt($ticket->id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('View Details') }}">
                                <i class="las la-eye"></i>
                            </a>
                        </td>
                    </tr>
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
