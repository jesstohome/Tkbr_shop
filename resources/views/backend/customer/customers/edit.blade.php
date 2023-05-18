@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Salesman Information')}}</h5>
            </div>

            <form class="form-horizontal" action="{{ route('customers.update', $user->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                <input name="address_id" type="hidden" value="{{$address->id ?? 0}}">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{ translate('Full Name') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Full Name')}}" id="name" name="name" class="form-control" required value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="balance">{{ translate('Balance') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Balance')}}" id="balance" name="balance" class="form-control" required value="{{ $user->balance }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{ translate('Country') }}</label>
                        <div class="col-sm-9">
                            <select id="country_id" class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="address[country_id]">
                                <option value="">{{ translate('Select your country') }}</option>
                                @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                    <option value="{{ $country->id }}" {{$address && $country->id == $address->country_id ? 'selected' : ''}}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="state_id">{{ translate('State') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" id="state_id" name="address[state_id]"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="city_id">{{ translate('City') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" id="city_id" name="address[city_id]"></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="address">{{ translate('Address') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Address')}}" id="address" name="address[address]" class="form-control" value="{{ $address->address ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="postal_code">{{ translate('Postal code') }}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Postal code')}}" id="postal_code" name="address[postal_code]" class="form-control" value="{{ $address->postal_code ?? ''}}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="phone">{{ translate('Phone') }}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" name="address[phone]" value="{{$address->phone ?? ''}}">
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#country_id').trigger('change');
        });
        $(document).on('change', '#country_id', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '#state_id', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('#state_id').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id,
                    select_state_id: "{{$address->state_id ?? 0}}"
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('#state_id').html(obj).trigger('change');
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('#city_id').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id,
                    select_city_id: "{{$address->city_id ?? 0}}"
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('#city_id').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    </script>
@endsection
