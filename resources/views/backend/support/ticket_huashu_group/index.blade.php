@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All Group')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('huashu_group.create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Add New Group')}}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Group')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table">
                <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Group Name')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $key => $group)
                    <tr>
                        <td>#{{ $key + 1}}</td>
                        <td>{{ $group->name}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('huashu_group.edit', $group->id )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('huashu_group.destroy', $group->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
            </div>
        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
