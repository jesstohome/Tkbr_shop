@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0 h6">{{translate('Automatic logistics Settings')}}</h1>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update2') }}" method="POST" >
                        @csrf

                        @php
                            $setting_value = json_decode(get_setting('logistics_times'), true);
                        @endphp
                        @foreach([1, 2, 3] as $key => $index)
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Automatic logistics timeline')}} {{$index}}</label>
                            <div class="col-md-3">
                                <input type="hidden" name="types[]" value="logistics_times">
                                <input   type="text" class="form-control"  value="{{ $setting_value[$key*2] }}"  name="logistics_times[]"  >

                            </div>

                            <div class="col-md-3">
                                <input   type="text" class="form-control"  value="{{ $setting_value[$index*2 - 1] }}" name="logistics_times[]"  >

                            </div>
                        </div>
                        @endforeach

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
