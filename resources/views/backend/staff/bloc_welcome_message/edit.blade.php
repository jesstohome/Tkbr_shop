@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Welcome Message Information')}}</h5>
</div>


<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <form class="p-4" action="{{ route('welcome.update')}}" method="POST">
                <input type="hidden" name="id" value="{{$bloc->id}}"/>
            	   @csrf
                <div class="card-body">

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="name">{{translate('Bloc Name')}} </label>
                        <div class="col-md-9">
                            <input type="text" readonly class="form-control" value="{{ $bloc->name }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="lang">{{translate('Examine Welcome Message')}} </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Examine Welcome Message')}}" id="examine_welcome_message" name="examine_welcome_message" class="form-control" value="{{ $bloc->examine_welcome_message }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="lang">{{translate('Welcome Message')}} </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Welcome Message')}}" id="welcome_message" name="welcome_message" class="form-control" value="{{ $bloc->welcome_message }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="lang">{{translate('Work Order Welcome Message')}} </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Work Order Welcome Message')}}" id="work_order_welcome_message" name="work_order_welcome_message" class="form-control" value="{{ $bloc->work_order_welcome_message }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="lang">{{translate('Welcome Message Interval Time')}} </label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{translate('Welcome Message Interval Time')}}" id="interval_time" name="interval_time" class="form-control" value="{{ $bloc->interval_time }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="lang">{{translate('Work Order Welcome Message Interval Time')}} </label>
                        <div class="col-md-9">
                            <input type="number" placeholder="{{translate('Work Order Welcome Message Interval Time')}}" id="work_order_interval_time" name="work_order_interval_time" class="form-control" value="{{ $bloc->work_order_interval_time }}">
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
