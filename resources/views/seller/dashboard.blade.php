@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 text-primary">{{ translate('Dashboard') }}</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6 col-xxl-3">
            <div class="card shadow-none mb-4 bg-primary py-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-16 text-info">{{ translate('Products') }}</span>
                            </p>
                            <h3 class="mb-0 text-white fs-30">
                                {{ \App\Models\Product::where('user_id', Auth::user()->id)->count() }}
                            </h3>

                        </div>
                        <div class="col-auto text-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64.001" height="64" viewBox="0 0 64.001 64">
                                <path id="Path_66" data-name="Path 66"
                                    d="M146.431,117.56l-26.514-10.606a8.014,8.014,0,0,0-5.944,0L87.458,117.56a4,4,0,0,0-2.514,3.714v34.217a4,4,0,0,0,2.514,3.714l26.514,10.606a8.013,8.013,0,0,0,5.944,0L146.431,159.2a4,4,0,0,0,2.514-3.714V121.274a4,4,0,0,0-2.514-3.714m-31.714-8.748a5.981,5.981,0,0,1,4.456,0l26.1,10.44a1,1,0,0,1,0,1.858l-12.332,4.932-30.654-12.26Zm1.228,59.633L88.2,157.347a2,2,0,0,1-1.258-1.856V122.6l29,11.6Zm1-36L88.612,121.11a1,1,0,0,1,0-1.858L99.6,114.858l30.654,12.262Zm30,23.048a2,2,0,0,1-1.258,1.856l-27.742,11.1V134.2l13-5.2V146.61a1.035,1.035,0,0,0,2-.466V128.2l14-5.6Z"
                                    transform="translate(-84.944 -106.382)" fill="#039be5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-xxl-3">
            <div class="card shadow-none mb-4 bg-primary py-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-16 text-info">{{ translate('Rating') }}</span>
                            </p>
                            <h3 class="mb-0 text-white fs-30">
                                {{ Auth::user()->shop->rating }}
                            </h3>

                        </div>
                        <div class="col-auto text-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="61.143" viewBox="0 0 64 61.143">
                                <path id="Path_57" data-name="Path 57"
                                    d="M63.286,22.145a2.821,2.821,0,0,0-1.816-.926L43.958,19.455a2.816,2.816,0,0,1-2.294-1.666L34.574,1.68a2.813,2.813,0,0,0-5.148,0l-7.09,16.11a2.813,2.813,0,0,1-2.292,1.666L2.53,21.219a2.813,2.813,0,0,0-1.59,4.9l13.13,11.72a2.818,2.818,0,0,1,.876,2.7l-3.734,17.2a2.812,2.812,0,0,0,4.166,3.026L30.584,51.9a2.8,2.8,0,0,1,2.832,0l15.206,8.864a2.813,2.813,0,0,0,4.166-3.026l-3.734-17.2a2.818,2.818,0,0,1,.876-2.7l13.13-11.72a2.813,2.813,0,0,0,.226-3.972m-1.5,2.546L48.658,36.413a4.717,4.717,0,0,0-1.47,4.524l3.732,17.2a.9.9,0,0,1-1.336.97l-15.2-8.866a4.729,4.729,0,0,0-4.758,0L14.416,59.109a.9.9,0,0,1-1.336-.97l3.732-17.2a4.717,4.717,0,0,0-1.47-4.524L2.212,24.691a.9.9,0,0,1,.51-1.57l17.512-1.766a4.721,4.721,0,0,0,3.85-2.8l7.09-16.11a.9.9,0,0,1,1.652,0l7.09,16.11a4.721,4.721,0,0,0,3.85,2.8l17.512,1.766a.9.9,0,0,1,.51,1.57"
                                    transform="translate(0 0)" fill="#039be5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-xxl-3">
            <div class="card shadow-none mb-4 bg-primary py-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-16 text-info">{{ translate('Total Order') }}</span>
                            </p>
                            <h3 class="mb-0 text-white fs-30">
                                {{ \App\Models\Order::where('seller_id', Auth::user()->id)->where('delivery_status', 'delivered')->count() }}
                            </h3>
                        </div>
                        <div class="col-auto text-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">
                                <g id="Group_25" data-name="Group 25" transform="translate(-1561.844 1020.618)">
                                    <path id="Path_58" data-name="Path 58"
                                        d="M229.23,106.382h-12a6,6,0,0,0,0,12h12a6,6,0,0,0,0-12m0,10h-12a4,4,0,0,1,0-8h12a4,4,0,0,1,0,8"
                                        transform="translate(1370.615 -1127)" fill="#039be5" />
                                    <path id="Path_59" data-name="Path 59"
                                        d="M213.73,117.882h24a1,1,0,0,1,0,2h-24a1,1,0,0,1,0-2"
                                        transform="translate(1372.115 -1115.5)" fill="#039be5" />
                                    <path id="Path_60" data-name="Path 60" d="M210.23,117.382a2,2,0,1,0,2,2,2,2,0,0,0-2-2"
                                        transform="translate(1367.615 -1116)" fill="#039be5" />
                                    <line id="Line_1" data-name="Line 1" transform="translate(1578.047 -1014.618)"
                                        fill="none" stroke="red" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="0.142" />
                                    <line id="Line_2" data-name="Line 2" transform="translate(1609.643 -1014.618)"
                                        fill="none" stroke="red" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="0.142" />
                                    <path id="Path_61" data-name="Path 61"
                                        d="M213.73,123.882h24a1,1,0,0,1,0,2h-24a1,1,0,0,1,0-2"
                                        transform="translate(1372.115 -1109.5)" fill="#039be5" />
                                    <path id="Path_62" data-name="Path 62" d="M210.23,123.382a2,2,0,1,0,2,2,2,2,0,0,0-2-2"
                                        transform="translate(1367.615 -1110)" fill="#039be5" />
                                    <path id="Path_63" data-name="Path 63"
                                        d="M213.73,129.882h24a1,1,0,0,1,0,2h-24a1,1,0,1,1,0-2"
                                        transform="translate(1372.115 -1103.5)" fill="#039be5" />
                                    <path id="Path_64" data-name="Path 64" d="M210.23,129.382a2,2,0,1,0,2,2,2,2,0,0,0-2-2"
                                        transform="translate(1367.615 -1104)" fill="#039be5" />
                                    <line id="Line_3" data-name="Line 3" transform="translate(1609.643 -1015.618)"
                                        fill="none" stroke="red" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="0.142" />
                                    <line id="Line_4" data-name="Line 4" transform="translate(1578.047 -1015.618)"
                                        fill="none" stroke="red" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="0.142" />
                                    <path id="Path_65" data-name="Path 65"
                                        d="M265.23,116.382a8,8,0,0,0-8-8h-7.2a1,1,0,0,0,0,2h7.2a6,6,0,0,1,6,6v44a6,6,0,0,1-6,6h-48a6,6,0,0,1-6-6v-44a6,6,0,0,1,6-6h7.2a1,1,0,0,0,0-2h-7.2a8,8,0,0,0-8,8v44a8,8,0,0,0,8,8h48a8,8,0,0,0,8-8Z"
                                        transform="translate(1360.615 -1125)" fill="#039be5" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-xxl-3">
            <div class="card shadow-none mb-4 bg-primary py-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-16 text-info">{{ translate('Total Sales') }}</span>
                            </p>
                            <h3 class="mb-0 text-white fs-30">
                                @php
                                    $orderDetails = \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->get();
                                    $total = 0;
                                    foreach ($orderDetails as $key => $orderDetail) {
                                        if ($orderDetail->order != null && $orderDetail->order->payment_status == 'paid') {
                                            $total += $orderDetail->price;
                                        }
                                    }
                                @endphp
                                {{ single_price($total) }}
                            </h3>

                        </div>
                        <div class="col-auto text-right">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64.001" viewBox="0 0 64 64.001">
                                <g id="Group_26" data-name="Group 26" transform="translate(-1571.385 1123.29)">
                                    <line id="Line_5" data-name="Line 5" transform="translate(1572.385 -1123.29)"
                                        fill="none" stroke="red" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="0.142" />
                                    <path id="Path_67" data-name="Path 67"
                                        d="M214.771,65.71a2,2,0,0,1-2-2v-59a1,1,0,0,0-2,0v59a4,4,0,0,0,4,4h59a1,1,0,0,0,0-2Z"
                                        transform="translate(1360.615 -1127)" fill="#039be5" />
                                    <line id="Line_6" data-name="Line 6" y1="0.136" x2="0.136"
                                        transform="translate(1586.533 -1087.117)" fill="none" stroke="red"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="0.142" />
                                    <path id="Path_68" data-name="Path 68"
                                        d="M264.6,10.027a3,3,0,0,0-4,4L247.536,27.1a2.994,2.994,0,0,0-2.594,0l-6.584-6.584a3,3,0,1,0-5.414,0L221.528,31.927a3,3,0,1,0,1.412,1.418l11.418-11.418a3,3,0,0,0,2.586,0l6.586,6.586a3,3,0,1,0,5.418,0l13.072-13.07a3,3,0,0,0,2.584-5.416M220.23,35.633a1,1,0,1,1,1-1,1,1,0,0,1-1,1m15.42-15.414a1,1,0,1,1,1-1,1,1,0,0,1-1,1M246.238,30.8a1,1,0,1,1,1-1,1,1,0,0,1-1,1m17.074-17.066a1,1,0,1,1,1-1,1,1,0,0,1-1,1"
                                        transform="translate(1367.074 -1120.976)" fill="#039be5" />
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-4 mb-4">
            <div class="card shadow-none bg-soft-primary">
                <div class="card-body">
                    <div class="card-title text-primary fs-16 fw-600">
                        {{ translate('Sales Stat') }}
                    </div>
                    <canvas id="graph-1" class="w-100" height="150"></canvas>
                </div>
            </div>
            <div class="card shadow-none bg-soft-primary mb-0">

                @php
                    $date = date('Y-m-d');
                    $days_ago_30 = date('Y-m-d', strtotime('-30 days', strtotime($date)));
                    $days_ago_60 = date('Y-m-d', strtotime('-60 days', strtotime($date)));

                    $orderTotal = \App\Models\Order::where('seller_id', Auth::user()->id)
                        ->where('payment_status', 'paid')
                        ->where('created_at', '>=', $days_ago_30)
                        ->sum('grand_total');
                @endphp

                <div class="card-body">
                    <div class="card-title text-primary fs-16 fw-600">
                        {{ translate('Sold Amount') }}
                    </div>
                    <p>{{ translate('Your Sold Amount (Current month)') }} {{translate('Including freight')}}</p>
                    <h3 class="text-primary fw-600 fs-30">
                        {{ single_price($orderTotal) }}
                    </h3>
                    <p class="mt-4">
                        @php
                            $orderTotal = \App\Models\Order::where('seller_id', Auth::user()->id)
                                ->where('payment_status', 'paid')
                                ->where('created_at', '>=', $days_ago_60)
                                ->where('created_at', '<=', $days_ago_30)
                                ->sum('grand_total');
                        @endphp
                        {{ translate('Last Month') }}: {{ single_price($orderTotal) }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-4 mb-4" style="display: flex;flex-direction: column;justify-content: space-between;">
            <div class="card shadow-none mb-0">
                <div class="card-body">
                    <div class="card-title text-primary fs-16 fw-600">
                        {{ translate('Category wise product count') }}
                    </div>
                    <hr>
                    <ul class="list-group">
                        @foreach (\App\Models\Category::all() as $key => $category)
                            @if (count($category->products->where('user_id', Auth::user()->id)) > 0)
                                <li class="d-flex justify-content-between align-items-center my-2 text-primary fs-13">
                                    {{ $category->getTranslation('name') }}
                                    <span class="">
                                        {{ count($category->products->where('user_id', Auth::user()->id)) }}
                                    </span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card shadow-none py-4 mt-1 mb-0" style="justify-self: end">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-16 text-info">{{ translate('Today Views') }}</span>
                            </p>
                            <h3 class="mb-0 text-primary fs-30 mt-1">
                                {{ Auth::user()->shop->views }}
                            </h3>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-4 mb-4">
            <div class="card h-450px mb-0 h-100">
                <div class="card-body">
                    <div class="card-title text-primary fs-16 fw-600">
                        {{ translate('Orders') }}
                        <p class="small text-muted mb-0">
                            <span class="fs-12 fw-600">{{ translate('This Month') }}</span>
                        </p>
                    </div>
                    <div class="row align-items-center mb-4">
                        <div class="col-auto text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                                <g id="Group_23742" data-name="Group 23742" transform="translate(2044 3467)">
                                    <rect id="Rectangle_17194" data-name="Rectangle 17194" width="30" height="30"
                                        transform="translate(-2044 -3467)" fill="none" />
                                    <g id="Group_23741" data-name="Group 23741" transform="translate(310 759)">
                                        <path id="Path_26908" data-name="Path 26908"
                                            d="M4.355,30a12.083,12.083,0,0,1-1.6-.517A4.905,4.905,0,0,1,.029,24.5c.146-1.377.228-2.761.339-4.142Q.532,18.313.7,16.271c.106-1.332.206-2.665.316-4,.129-1.555.227-3.114.413-4.662a2,2,0,0,1,2-1.687c.782-.012,1.565,0,2.348,0h.336A5.77,5.77,0,0,1,8.275,1.3,5.615,5.615,0,0,1,12.367.018a5.841,5.841,0,0,1,5.38,5.9h.278c.753,0,1.507,0,2.26,0A2.116,2.116,0,0,1,22.5,7.986c.165,2.091.343,4.181.509,6.272s.322,4.183.488,6.273c.107,1.352.222,2.7.335,4.054a4.9,4.9,0,0,1-4.195,5.362A.61.61,0,0,0,19.5,30ZM6.118,7.678c-.893,0-1.743-.005-2.593,0-.282,0-.383.141-.407.463q-.151,1.97-.307,3.939Q2.559,15.2,2.3,18.325c-.156,1.935-.319,3.869-.455,5.806a6.248,6.248,0,0,0,.028,1.685,3.078,3.078,0,0,0,3.166,2.427q6.882,0,13.764,0c.088,0,.176,0,.264-.006a3.145,3.145,0,0,0,2.986-3.544c-.117-1.076-.177-2.158-.262-3.238-.105-1.342-.208-2.684-.315-4.026-.128-1.6-.261-3.209-.389-4.813q-.181-2.275-.357-4.551a.36.36,0,0,0-.365-.381c-.868-.009-1.735,0-2.63,0,0,.123,0,.218,0,.313,0,.615.006,1.23,0,1.845a.878.878,0,1,1-1.755-.006c-.006-.71,0-1.419,0-2.134h-8.1c0,.693,0,1.365,0,2.038a1.312,1.312,0,0,1-.034.347A.877.877,0,0,1,6.12,9.847c-.008-.711,0-1.422,0-2.168M7.894,5.9h8.069a4.036,4.036,0,1,0-8.069,0"
                                            transform="translate(-2351 -4226)" fill="#0277bd" />
                                        <path id="Path_26909" data-name="Path 26909"
                                            d="M156.63,290.4H153.2v-3.431a.872.872,0,1,0-1.744,0V290.4h-3.431a.872.872,0,1,0,0,1.744h3.431v3.431a.872.872,0,0,0,1.744,0v-3.431h3.431a.872.872,0,0,0,0-1.744"
                                            transform="translate(-2491.298 -4498.774)" fill="#0277bd" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-13 text-primary fw-600">{{ translate('New Order') }}</span>
                            </p>
                            <h3 class="mb-0 text-info">
                                {{ \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'pending')->count() }}
                            </h3>
                        </div>
                    </div>
                    <div class="row align-items-center mb-4">
                        <div class="col-auto text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="30"
                                height="30" viewBox="0 0 30 30">
                                <defs>
                                    <clipPath id="clip-path">
                                        <rect id="Rectangle_17198" data-name="Rectangle 17198" width="30" height="30"
                                            transform="translate(0 0)" fill="none" />
                                    </clipPath>
                                </defs>
                                <g id="Group_23751" data-name="Group 23751" transform="translate(0 0.001)">
                                    <g id="Group_23750" data-name="Group 23750" transform="translate(0 -0.001)"
                                        clip-path="url(#clip-path)">
                                        <path id="Path_27505" data-name="Path 27505"
                                            d="M13.122,30H7.03A7.041,7.041,0,0,1,0,22.959V7.03A7.041,7.041,0,0,1,7.03,0H22.959A7.041,7.041,0,0,1,30,7.03v5.857a1.172,1.172,0,1,1-2.343,0V7.03a4.691,4.691,0,0,0-4.7-4.687H7.03A4.691,4.691,0,0,0,2.343,7.03V22.959A4.691,4.691,0,0,0,7.03,27.646h6.092a1.177,1.177,0,0,1,0,2.354"
                                            transform="translate(0 0)" fill="#0277bd" />
                                        <path id="Path_27506" data-name="Path 27506"
                                            d="M193.376,91.163a1.171,1.171,0,0,0-1.171-1.171h-5.969a1.172,1.172,0,1,0,0,2.343h5.969a1.171,1.171,0,0,0,1.171-1.171v0"
                                            transform="translate(-174.22 -84.719)" fill="#0277bd" />
                                        <path id="Path_27507" data-name="Path 27507"
                                            d="M249.953,242.05a7.909,7.909,0,1,0,7.916,7.9,7.909,7.909,0,0,0-7.916-7.9m.008,13.467a5.563,5.563,0,1,1,5.558-5.566h.008a5.566,5.566,0,0,1-5.566,5.566"
                                            transform="translate(-227.869 -227.867)" fill="#0277bd" />
                                        <path id="Path_27508" data-name="Path 27508"
                                            d="M331.615,329.84l.929-.929a1.172,1.172,0,0,0-1.658-1.656l-.929.929-.929-.929a1.172,1.172,0,0,0-1.658,1.656l.929.929-.929.929a1.172,1.172,0,1,0,1.658,1.656l.929-.929.929.929a1.172,1.172,0,1,0,1.658-1.656Z"
                                            transform="translate(-307.867 -307.756)" fill="#0277bd" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-13 text-primary fw-600">{{ translate('Cancelled') }}</span>
                            </p>
                            <h3 class="mb-0 text-info">
                                {{ \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'cancelled')->count() }}
                            </h3>
                        </div>
                    </div>
                    <div class="row align-items-center mb-4">
                        <div class="col-auto text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                                <g id="Group_23745" data-name="Group 23745" transform="translate(1901 3455)">
                                    <rect id="Rectangle_17195" data-name="Rectangle 17195" width="30" height="30"
                                        transform="translate(-1901 -3455)" fill="none" />
                                    <g id="Group_23744" data-name="Group 23744" transform="translate(-867.487 654.098)">
                                        <path id="Path_26911" data-name="Path 26911"
                                            d="M1352.884,172.262h-4.464a.88.88,0,1,0,0,1.761h4.464a.88.88,0,1,0,0-1.761"
                                            transform="translate(-2379.291 -4277.175)" fill="#0277bd" />
                                        <path id="Path_26912" data-name="Path 26912"
                                            d="M1352.884,292.455h-4.464a.88.88,0,1,0,0,1.761h4.464a.88.88,0,1,0,0-1.761"
                                            transform="translate(-2379.291 -4390.326)" fill="#0277bd" />
                                        <path id="Path_26913" data-name="Path 26913"
                                            d="M1322.832,232.366h-4.464a.88.88,0,1,0,0,1.761h4.464a.88.88,0,0,0,0-1.761"
                                            transform="translate(-2351 -4333.757)" fill="#0277bd" />
                                        <path id="Path_26914" data-name="Path 26914"
                                            d="M1531.056,222.736h-5.341v-3.52a1.763,1.763,0,0,0-3-1.244l-7.04,7.04a1.76,1.76,0,0,0,0,2.489h0l4.035,4.035-4.918,4.918a1.761,1.761,0,0,0,2.49,2.49l6.162-6.163a1.76,1.76,0,0,0,0-2.489h0l-4.035-4.035,2.792-2.792v1.03a1.761,1.761,0,0,0,1.761,1.761h7.1a1.761,1.761,0,0,0,0-3.52Z"
                                            transform="translate(-2536.278 -4319.726)" fill="#0277bd" />
                                        <path id="Path_26915" data-name="Path 26915"
                                            d="M1475.968,150.029a1.761,1.761,0,0,0-2.222.22l-4.842,4.842a1.761,1.761,0,0,0,2.441,2.538l.049-.049,3.821-3.821,1.288.927,1.717-1.717a3.5,3.5,0,0,1,1-.687Z"
                                            transform="translate(-2493.036 -4255.966)" fill="#0277bd" />
                                        <path id="Path_26916" data-name="Path 26916"
                                            d="M1344.676,384.535a3.489,3.489,0,0,1-.9-1.589l-9.3,9.3a1.761,1.761,0,0,0,2.49,2.49l8.955-8.954Z"
                                            transform="translate(-2366.531 -4475.515)" fill="#0277bd" />
                                        <path id="Path_26917" data-name="Path 26917"
                                            d="M1690.437,117.9a2.5,2.5,0,1,1-2.5,2.5,2.5,2.5,0,0,1,2.5-2.5"
                                            transform="translate(-2699.74 -4226)" fill="#0277bd" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-13 text-primary fw-600">{{ translate('On Delivery') }}</span>
                            </p>
                            <h3 class="mb-0 text-info">
                                {{ \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'on_the_way')->count() }}
                            </h3>
                        </div>
                    </div>
                    <div class="row align-items-center mb-4">
                        <div class="col-auto text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                                <g id="Group_23747" data-name="Group 23747" transform="translate(1894 3457)">
                                    <rect id="Rectangle_17196" data-name="Rectangle 17196" width="30" height="30"
                                        transform="translate(-1894 -3457)" fill="none" />
                                    <g id="Group_23746" data-name="Group 23746" transform="translate(-1599.983 686.845)">
                                        <path id="Path_26918" data-name="Path 26918"
                                            d="M2077.33,84.3v.4q0,3.482,0,6.963a1.069,1.069,0,0,1-.7,1.137,1.082,1.082,0,0,1-1.236-.336c-.411-.424-.836-.834-1.273-1.268-.4.4-.806.792-1.206,1.191a1.126,1.126,0,0,1-1.887-.009c-.392-.393-.791-.78-1.208-1.192-.46.464-.9.934-1.371,1.375a1.071,1.071,0,0,1-1.789-.482,1.63,1.63,0,0,1-.036-.43q0-3.465,0-6.93V84.3h-.363q-2.409,0-4.819,0a2.166,2.166,0,0,0-2.317,2.325q0,10.529,0,21.058a2.17,2.17,0,0,0,2.343,2.333q4.183,0,8.366,0a1.07,1.07,0,0,1,.3,2.1,1.345,1.345,0,0,1-.363.038c-2.867,0-5.734.008-8.6,0a4.261,4.261,0,0,1-4.181-4.194q-.008-10.8,0-21.593a4.254,4.254,0,0,1,4.2-4.2q10.792-.007,21.584,0a4.259,4.259,0,0,1,4.192,4.182c.008,2.868,0,5.736,0,8.6a1.071,1.071,0,1,1-2.138,0q0-4.134,0-8.269a2.177,2.177,0,0,0-2.365-2.378h-5.133m-2.163,4.811V84.324h-6.387v4.842c.063-.051.1-.074.125-.1.709-.676,1.2-.671,1.884.017.392.392.789.78,1.194,1.179.459-.458.909-.9,1.357-1.353a.991.991,0,0,1,1.1-.271,3.98,3.98,0,0,1,.726.472"
                                            transform="translate(-2351 -4226)" fill="#0277bd" />
                                        <path id="Path_26919" data-name="Path 26919"
                                            d="M2276.429,310.26a8.566,8.566,0,1,1,8.554,8.574,8.552,8.552,0,0,1-8.554-8.574m14.992,0a6.426,6.426,0,1,0-6.388,6.431,6.451,6.451,0,0,0,6.388-6.431"
                                            transform="translate(-2557.593 -4432.681)" fill="#0277bd" />
                                        <path id="Path_26920" data-name="Path 26920"
                                            d="M2352.663,396.855c.43-.437.848-.866,1.271-1.292q1.072-1.08,2.148-2.155a1.083,1.083,0,1,1,1.531,1.519q-2.064,2.073-4.137,4.139a1.071,1.071,0,0,1-1.672,0q-1-.99-1.986-1.986a1.085,1.085,0,1,1,1.538-1.513l1.305,1.29"
                                            transform="translate(-2626.31 -4518.65)" fill="#0277bd" />
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="col">
                            <p class="small text-muted mb-0">
                                <span class="fe fe-arrow-down fe-12"></span>
                                <span class="fs-13 text-primary fw-600">{{ translate('Delivered') }}</span>
                            </p>
                            <h3 class="mb-0 text-info">
                                {{ \App\Models\OrderDetail::where('seller_id', Auth::user()->id)->where('delivery_status', 'delivered')->count() }}
                            </h3>
                        </div>
                    </div>

                </div>
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

                    backgroundColor: ['#0277BD', '#0277BD', '#0277BD', '#0277BD', '#0277BD', '#0277BD',
                        '#0277BD'
                    ],
                    borderColor: ['#0277BD', '#0277BD', '#0277BD', '#0277BD', '#0277BD', '#0277BD',
                        '#0277BD'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        gridLines: {
                            color: '#D2E7F2',
                            zeroLineColor: '#f2f3f8'
                        },
                        ticks: {
                            fontColor: "#84A8BD",
                            fontFamily: 'Roboto',
                            fontSize: 10,
                            beginAtZero: true
                        },
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            fontColor: "#8b8b8b",
                            fontFamily: 'Roboto',
                            fontSize: 10
                        },
                        barThickness: 7,
                        barPercentage: .5,
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
