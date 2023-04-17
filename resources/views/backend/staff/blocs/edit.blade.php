@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Bloc Information')}}</h5>
</div>


<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <form class="p-4" action="{{ route('bloc.update', $bloc->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
            	   @csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="name">{{translate('Bloc Name')}} </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Bloc Name')}}" id="name" name="name" class="form-control" value="{{ $bloc->name }}" required>
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
