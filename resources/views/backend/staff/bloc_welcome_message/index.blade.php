@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Blocs')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table">
                <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Bloc Name')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @php
                if (!isSupperAdmin()) $blocs = [Auth::user()->bloc];
                @endphp
                @foreach($blocs as $key => $bloc)
                    <tr>
                        <td>{{ ($key+1) }}</td>
                        <td>{{ $bloc->name}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('welcome.edit', ['id'=>$bloc->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(isSupperAdmin())
            <div class="aiz-pagination">
                {{ $blocs->appends(request()->input())->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection
