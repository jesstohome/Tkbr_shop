@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Bloc Information')}}</h5>
        </div>
        <form action="{{ route('bloc.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Bloc Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Bloc Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="lang">{{translate('Language')}} </label>
                    <div class="col-md-9">
                        <select class="form-control aiz-selectpicker" name="lang" id="lang">
                            <option value="">{{translate('All')}}</option>
                            @foreach (\App\Models\Language::all() as $key => $language)
                                <option value="{{$language->code}}">{{$language->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Time Zone')}} </label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Time Zone')}}" id="time_zone" name="time_zone" class="form-control" value="{{ $bloc->time_zone }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="lang">{{translate('Currency')}} </label>
                    <div class="col-md-9">
                        <select class="form-control aiz-selectpicker" name="currency_code" id="currency" required>
                            <option value="">请选择货币</option>
                            @foreach (\App\Models\Currency::query()->where('status', 1)->get() as $key => $currency)
                                <option value="{{$currency->code}}">{{translate($currency->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Ip Whitelist')}} </label>
                    <div class="col-md-9">
                        <textarea type="text" placeholder="多个IP换行录入" id="ip_whitelist" name="ip_whitelist" class="form-control">{{ $bloc->ip_whitelist }}</textarea>
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
