@extends('seller.layouts.app')

@section('panel_content')
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 mt-2">
        <div class="min-w-0">
            <h1 class="h5 fw-700 mb-0 text-truncate" style="color:#1e293b;">{{ translate('Dashboard') }}</h1>
            <p class="text-muted small mb-0 text-truncate">{{ translate('Welcome back') }}, {{ Auth::user()->name }}</p>
        </div>
        <div class="d-none d-md-block flex-shrink-0 ml-2" style="max-width:180px;">
            <span class="badge badge-soft-primary px-2 py-1 text-truncate d-block">
                <i class="las la-store mr-1"></i> {{ Auth::user()->shop->name }}
            </span>
        </div>
    </div>

    {{-- Stat Cards: 2 per row on mobile, 4 on desktop --}}
    <div class="row mb-3">
        <div class="col-6 col-xl-3 mb-2 mb-xl-0">
            <div class="stat-card card-products text-white">
                <div class="stat-card-inner">
                    <div class="stat-card-left">
                        <div class="stat-value">{{ \App\Models\Product::where('user_id', Auth::user()->id)->count() }}</div>
                        <div class="stat-label">{{ translate('Products') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="las la-box"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-2 mb-xl-0">
            <div class="stat-card card-rating text-white">
                <div class="stat-card-inner">
                    <div class="stat-card-left">
                        <div class="stat-value">{{ Auth::user()->shop->rating }}</div>
                        <div class="stat-label">{{ translate('Rating') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="las la-star"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3 mb-2 mb-xl-0">
            <div class="stat-card card-orders text-white">
                <div class="stat-card-inner">
                    <div class="stat-card-left">
                        <div class="stat-value">{{ \App\Models\Order::where('seller_id', Auth::user()->id)->where('created_at', '<=', date('Y-m-d H:i:s'))->count() }}</div>
                        <div class="stat-label">{{ translate('Orders') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="las la-clipboard-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="stat-card card-sales text-white">
                <div class="stat-card-inner">
                    <div class="stat-card-left">
                        <div class="stat-value">
                            @php
                                $orderDetails = \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->get();
                                $total = 0;
                                foreach ($orderDetails as $key => $orderDetail) {
                                    if ($orderDetail->order != null &&
                                    $orderDetail->order->payment_status == 'paid' &&
                                    $orderDetail->order->created_at <= date('Y-m-d H:i:s') &&
                                    $orderDetail->order->delivery_status != 'cancelled'
                                    ) {
                                        $total += $orderDetail->price;
                                    }
                                }
                            @endphp
                            {{ single_price($total) }}
                        </div>
                        <div class="stat-label">{{ translate('Sales') }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="las la-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Chart & Sold Amount --}}
    <div class="row mb-3">
        <div class="col-12 col-lg-5 mb-2 mb-lg-0">
            <div class="chart-card mb-2">
                <div class="fw-600 mb-2" style="color:#1e293b; font-size:14px;">{{ translate('Sales Statistics') }}</div>
                <canvas id="graph-1" class="w-100" height="160"></canvas>
            </div>
            <div class="chart-card">
                @php
                    $date = date('Y-m-d');
                    $days_ago_30 = date('Y-m-d', strtotime('-30 days', strtotime($date)));
                    $days_ago_60 = date('Y-m-d', strtotime('-60 days', strtotime($date)));

                    $orderTotal = \App\Models\Order::where('seller_id', Auth::user()->id)
                        ->where('payment_status', 'paid')
                        ->where('delivery_status', '!=', 'cancelled')
                        ->where('created_at', '>=', $days_ago_30)
                        ->where('created_at', '<=', date('Y-m-d H:i:s'))
                        ->sum('grand_total');
                @endphp
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="fw-600" style="color:#1e293b; font-size:14px;">{{ translate('Sold Amount') }}</div>
                    <span class="badge badge-soft-success badge-sm">{{ translate('This Month') }}</span>
                </div>
                <div class="fw-700 mb-1" style="font-size:22px; color:#1e293b;">{{ single_price($orderTotal) }}</div>
                <p class="text-muted small mb-0">
                    @php
                        $orderTotal = \App\Models\Order::where('seller_id', Auth::user()->id)
                            ->where('payment_status', 'paid')
                            ->where('delivery_status', '!=', 'cancelled')
                            ->where('created_at', '>=', $days_ago_60)
                            ->where('created_at', '<=', $days_ago_30)
                            ->sum('grand_total');
                    @endphp
                    {{ translate('Last Month') }}: <strong>{{ single_price($orderTotal) }}</strong>
                </p>
            </div>
        </div>

        {{-- Category Products + Views --}}
        <div class="col-12 col-lg-3 mb-2 mb-lg-0">
            <div class="chart-card h-100 d-flex flex-column">
                <div class="fw-600 mb-2" style="color:#1e293b; font-size:14px;">{{ translate('Categories') }}</div>
                <div class="flex-grow-1">
                    @php
                    $categoryIds = \App\Models\Product::query()->where("user_id", Auth::user()->id)->groupBy('category_id')->pluck('category_id')->toArray();
                    @endphp
                    @foreach (\App\Models\Category::query()->whereIn("id", $categoryIds)->get() as $key => $category)
                        @php
                            $count = count($category->products->where('user_id', Auth::user()->id));
                        @endphp
                        @if ($count > 0)
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="border-color:#f1f5f9!important;">
                                <span class="text-truncate mr-2" style="font-size:12px; color:#334155; max-width:70%;">{{ $category->getTranslation('name') }}</span>
                                <span class="badge badge-soft-primary" style="font-size:11px;">{{ $count }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-auto pt-2 border-top" style="border-color:#f1f5f9!important;">
                    <div class="d-flex align-items-center">
                        <div class="mr-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:36px; height:36px; background:#eef2ff;">
                                <i class="las la-eye" style="color:#4f46e5; font-size:16px;"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fw-700" style="font-size:18px; color:#1e293b; line-height:1.2;">{{ Auth::user()->shop->views }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ translate('Today Views') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders Overview --}}
        <div class="col-12 col-lg-4">
            <div class="chart-card h-100">
                <div class="fw-600 mb-2" style="color:#1e293b; font-size:14px;">{{ translate('Orders Overview') }}</div>
                <div class="row">
                    <div class="col-6 col-lg-12">
                        <div class="order-stat-item">
                            <div class="order-stat-icon new-order">
                                <i class="las la-cart-plus"></i>
                            </div>
                            <div class="order-stat-info">
                                <h4>{{ \App\Models\Order::where('seller_id', Auth::user()->id)->where('delivery_status', 'pending')->where('created_at', '<=', date('Y-m-d H:i:s'))->count() }}</h4>
                                <span>{{ translate('New') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12">
                        <div class="order-stat-item">
                            <div class="order-stat-icon cancelled">
                                <i class="las la-times-circle"></i>
                            </div>
                            <div class="order-stat-info">
                                <h4>{{ \App\Models\Order::where('seller_id', Auth::user()->id)->where('delivery_status', 'cancelled')->count() }}</h4>
                                <span>{{ translate('Cancelled') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12">
                        <div class="order-stat-item">
                            <div class="order-stat-icon delivery">
                                <i class="las la-truck"></i>
                            </div>
                            <div class="order-stat-info">
                                <h4>{{ \App\Models\Order::where('seller_id', Auth::user()->id)->whereIn('delivery_status', ['prepare_goods',
            'sent_to_the_distribution_center',
            'distribution_sorting',
            'sent_to_the_delivery_center',
            'delivery_sorting',
            'delivery_in_progress'])->count() }}</h4>
                                <span>{{ translate('Delivering') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12">
                        <div class="order-stat-item">
                            <div class="order-stat-icon delivered">
                                <i class="las la-check-circle"></i>
                            </div>
                            <div class="order-stat-info">
                                <h4>{{ \App\Models\Order::where('seller_id', Auth::user()->id)->where('delivery_status', 'delivered')->count() }}</h4>
                                <span>{{ translate('Delivered') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions: 2 per row on mobile --}}
    <div class="row mb-3">
        <div class="col-6 col-lg-3 mb-2 mb-lg-0">
            <a href="{{ route('seller.money_withdraw_requests.index') }}" class="quick-action-card text-decoration-none">
                <div class="action-icon withdraw">
                    <i class="las la-hand-holding-usd"></i>
                </div>
                <div class="action-label">{{ translate('Money Withdraw') }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3 mb-2 mb-lg-0">
            <a href="{{ route('seller.product_storehouse.index') }}" class="quick-action-card text-decoration-none">
                <div class="action-icon storehouse">
                    <i class="las la-warehouse"></i>
                </div>
                <div class="action-label">{{ translate('Storehouse') }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3 mb-2 mb-lg-0">
            <a href="{{ route('seller.shop.index') }}" class="quick-action-card text-decoration-none">
                <div class="action-icon settings">
                    <i class="las la-cog"></i>
                </div>
                <div class="action-label">{{ translate('Shop Settings') }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('seller.profile.index') }}" class="quick-action-card text-decoration-none">
                <div class="action-icon payment">
                    <i class="las la-credit-card"></i>
                </div>
                <div class="action-label">{{ translate('Payments') }}</div>
            </a>
        </div>
    </div>

    {{-- Top Products --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-600 mb-0" style="color:#1e293b; font-size:14px;">{{ translate('Top 12 Products') }}</h6>
            </div>
            <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5" data-lg-items="4"
                data-md-items="3" data-sm-items="2" data-arrows='true'>
                @foreach ($products as $key => $product)
                    <div class="carousel-box">
                        <div
                            class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md mb-2 has-transition bg-white">
                            <div class="position-relative">
                                <a href="{{ route('product', $product->slug) }}" class="d-block">
                                    <img class="img-fit lazyload mx-auto h-210px"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                        alt="{{ $product->getTranslation('name') }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </a>
                            </div>
                            <div class="p-md-3 p-2 text-left">
                                <div class="fs-15">
                                    @if (home_base_price($product) != home_discounted_base_price($product))
                                        <del class="fw-600 opacity-50 mr-1">{{ home_base_price($product) }}</del>
                                    @endif
                                    <span class="fw-700 text-primary">{{ home_discounted_base_price($product) }}</span>
                                </div>
                                <div class="rating rating-sm mt-1">
                                    {{ renderStarRating($product->rating) }}
                                </div>
                                <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                    <a href="{{ route('product', $product->slug) }}"
                                        class="d-block text-reset">{{ $product->getTranslation('name') }}</a>
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        AIZ.plugins.chart('#graph-1', {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($last_7_days_sales as $key => $last_7_days_sale)
                        '{{ $key }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Sales ($)',
                    data: [
                        @foreach ($last_7_days_sales as $key => $last_7_days_sale)
                            '{{ $last_7_days_sale }}',
                        @endforeach
                    ],

                    backgroundColor: ['#4f46e5', '#7c3aed', '#4f46e5', '#7c3aed', '#4f46e5', '#7c3aed', '#4f46e5'],
                    borderColor: ['#4f46e5', '#7c3aed', '#4f46e5', '#7c3aed', '#4f46e5', '#7c3aed', '#4f46e5'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        gridLines: {
                            color: '#e2e8f0',
                            zeroLineColor: '#f1f5f9'
                        },
                        ticks: {
                            fontColor: "#94a3b8",
                            fontFamily: 'Poppins, sans-serif',
                            fontSize: 10,
                            beginAtZero: true
                        },
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: "#94a3b8",
                            fontFamily: 'Poppins, sans-serif',
                            fontSize: 10
                        },
                        barThickness: 10,
                        barPercentage: .6,
                        categoryPercentage: .5,
                    }],
                },
                legend: {
                    display: false
                }
            }
        });
    </script>
@endsection
