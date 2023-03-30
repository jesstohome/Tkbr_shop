@extends('seller.layouts.app')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('CreditscoreStream History') }}</h5>
        </div>
        @if (count($creditscorestreams) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Date')}}</th>
                            <th>{{ translate('Creditscore Before')}}</th>
                            <th>{{ translate('Creditscore After')}}</th>
                            <th>{{ translate('Remark')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($creditscorestreams as $key => $creditscorestream)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                </td>
                                <td>{{ date('d-m-Y', strtotime($creditscorestream->created_at)) }}</td>
                                <td>
                                    {{ $creditscorestream->creditscore_before }}
                                </td>
                                <td>
                                    {{ $creditscorestream->creditscore_after }}
                                </td>
                                <td>
                                    {{ $creditscorestream->remark }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                	{{ $creditscorestreams->links() }}
              	</div>
            </div>
        @endif
    </div>

@endsection
