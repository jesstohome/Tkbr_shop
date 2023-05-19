@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Pay Channel Information')}}</h5>
        </div>
        <form action="{{ route('pay_channel.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="lang">{{translate('Country')}} </label>
                    <div class="col-md-9">
                        <select class="form-control aiz-selectpicker" name="country_id" id="country_id">
                            <option value="">{{translate('All')}}</option>
                            @foreach ($countries as $key => $country)
                                <option value="{{$country->id}}">{{translate($country->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Bloc')}} </label>
                    <div class="col-md-9">
                        <select name="bloc_ids[]" id="bloc_ids" size="15" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Blocs') }}" data-live-search="true" data-selected-text-format="count">
                            @foreach($blocs as $bloc)
                                <option value="{{$bloc->id}}" <?php if(in_array($bloc->id, explode(",", get_setting('india_htpay_bloc_ids')))) echo "selected";?> >{{ $bloc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Field')}} </label>
                    <div class="col-md-9">
                        <select name="field_codes[]" id="field_codes" size="15" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Field') }}" data-live-search="true" data-selected-text-format="count">
                            @foreach($fields as $field_code => $field_name)
                                <option value="{{$field_code}}" >{{ translate($field_name) }}</option>
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
