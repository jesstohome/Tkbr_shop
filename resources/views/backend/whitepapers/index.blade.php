@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Whitepapers') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('whitepapers.create') }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Whitepaper') }}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('Cover Image') }}</th>
                        <th>{{ translate('Title') }}</th>
                        <th data-breakpoints="lg">{{ translate('Slug') }}</th>
                        <th data-breakpoints="lg">{{ translate('Sort') }}</th>
                        <th data-breakpoints="lg">{{ translate('Views') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($whitepapers as $key => $whitepaper)
                        <tr>
                            <td>{{ ($key+1) + ($whitepapers->currentPage() - 1) * $whitepapers->perPage() }}</td>
                            <td>
                                @if ($whitepaper->cover_image)
                                    <img src="{{ uploaded_asset($whitepaper->cover_image) }}" alt="cover" class="h-50px">
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>{{ $whitepaper->getTranslation('title') }}</td>
                            <td>{{ $whitepaper->slug }}</td>
                            <td>{{ $whitepaper->sort }}</td>
                            <td>{{ $whitepaper->views }}</td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="{{ $whitepaper->id }}" @if($whitepaper->status == 1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('whitepapers.edit', $whitepaper->id) }}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{ route('whitepapers.destroy', $whitepaper->id) }}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $whitepapers->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function update_status(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('whitepapers.update_status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
