@extends('frontend.layouts.app')

@section('content')
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">{{ translate('Whitepapers') }}</h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        {{ translate('Whitepapers') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container">
        <div class="row">
            @foreach ($whitepapers as $whitepaper)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 overflow-hidden">
                        <a href="{{ route('whitepapers.details', $whitepaper->slug) }}" class="d-block">
                            @if ($whitepaper->cover_image)
                                <img src="{{ uploaded_asset($whitepaper->cover_image) }}" alt="{{ $whitepaper->getTranslation('title') }}" class="card-img-top img-fluid lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                            @else
                                <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" alt="{{ $whitepaper->getTranslation('title') }}" class="card-img-top img-fluid lazyload">
                            @endif
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-600 mb-2">
                                <a href="{{ route('whitepapers.details', $whitepaper->slug) }}" class="text-reset">
                                    {{ $whitepaper->getTranslation('title') }}
                                </a>
                            </h5>
                            @if ($whitepaper->getTranslation('summary'))
                                <p class="text-muted mb-3">{{ $whitepaper->getTranslation('summary') }}</p>
                            @endif
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <a href="{{ route('whitepapers.details', $whitepaper->slug) }}" class="btn btn-sm btn-primary">{{ translate('Read More') }}</a>
                                @if ($whitepaper->getTranslation('pdf'))
                                    <a href="{{ route('whitepapers.download', $whitepaper->slug) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="las la-download"></i> {{ translate('Download PDF') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="aiz-pagination aiz-pagination-center">
            {{ $whitepapers->links() }}
        </div>
    </div>
</section>
@endsection
