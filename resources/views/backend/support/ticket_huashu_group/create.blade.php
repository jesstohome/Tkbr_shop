@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Group Information')}}</h5>
        </div>
        <form action="{{ route('huashu_group.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Group Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Group Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">归属话术</label>
                    <div class="col-md-9">
                        <select name="huashu_ids[]" id="haushu" class="form-control aiz-selectpicker" multiple required data-live-search="true" data-selected-text-format="count">
                            @foreach(filter_by_bloc(\App\Models\TicketHuaShu::query()->where('group_id', 0))->get() as $item)
                                <option value="{{$item->id}}">{{ $item->abstract }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
