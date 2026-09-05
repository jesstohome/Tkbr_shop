@extends('frontend.layouts.app')

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ $whitepaper->getTranslation('title') }}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('whitepapers.all') }}">{{ translate('Whitepapers') }}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        {{ $whitepaper->getTranslation('title') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container">
        <div class="p-4 bg-white rounded shadow-sm overflow-hidden mw-100 text-left">
            @if ($whitepaper->cover_image)
                <div class="text-center mb-4">
                    <img src="{{ uploaded_asset($whitepaper->cover_image) }}" alt="{{ $whitepaper->getTranslation('title') }}" class="img-fluid rounded lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                </div>
            @endif
            <div class="whitepaper-content">
                @php echo $whitepaper->getTranslation('content'); @endphp
            </div>
            @if ($whitepaper->getTranslation('pdf'))
                <div class="text-center mt-4 pt-3 border-top">
                    <a href="{{ route('whitepapers.download', $whitepaper->slug) }}" class="btn btn-primary btn-lg">
                        <i class="las la-download"></i> {{ translate('Download PDF') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
