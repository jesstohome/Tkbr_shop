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
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="{{ route('seller.money_withdraw_requests.index') }}"
                class="card mb-4 p-4 text-center bg-soft-primary h-180px">
                <div class="fs-16 fw-600 text-primary">
                    {{ translate('Money Withdraw') }}
                </div>
                <div class="m-3">
                    <svg id="Group_22725" data-name="Group 22725" xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                        viewBox="0 0 48 48">
                        <path id="Path_108"
                            d="M24,28.5A1.538,1.538,0,0,1,25.5,30v6a1.5,1.5,0,0,1-3,0V30A1.538,1.538,0,0,1,24,28.5"
                            fill="#0277bd" />
                        <path id="Path_109"
                            d="M36,21H33V43.5A1.538,1.538,0,0,1,31.5,45h-15A1.538,1.538,0,0,1,15,43.5V21H12V43.5A4.481,4.481,0,0,0,16.5,48h15A4.481,4.481,0,0,0,36,43.5Z"
                            fill="#0277bd" />
                        <path id="Path_110"
                            d="M43.5,0H4.5A4.481,4.481,0,0,0,0,4.5v9A4.481,4.481,0,0,0,4.5,18h39A4.481,4.481,0,0,0,48,13.5v-9A4.481,4.481,0,0,0,43.5,0M45,13.5A1.538,1.538,0,0,1,43.5,15H4.5A1.538,1.538,0,0,1,3,13.5v-9A1.538,1.538,0,0,1,4.5,3h39A1.538,1.538,0,0,1,45,4.5Z"
                            fill="#0277bd" />
                        <path id="Path_111" d="M28.5,21h-9a4.5,4.5,0,0,0,9,0" fill="#0277bd" />
                    </svg>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="{{ route('seller.product_storehouse.index') }}" class="card mb-4 p-4 text-center h-180px">
                <div class="fs-16 fw-600 text-primary">
                    {{ translate('Product Storehouse') }}
                </div>
                <div class="m-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                        <g id="Group_22724" data-name="Group 22724" transform="translate(-1284 -875)">
                            <rect id="Rectangle_17080" data-name="Rectangle 17080" width="2" height="48" rx="1"
                                transform="translate(1307 875)" fill="#0277bd" />
                            <rect id="Rectangle_17081" data-name="Rectangle 17081" width="2" height="48" rx="1"
                                transform="translate(1332 898) rotate(90)" fill="#0277bd" />
                        </g>
                    </svg>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="card mb-4 p-4 text-center bg-soft-primary">
                <div class="fs-16 fw-600 text-primary">
                    {{ translate('Shop Settings') }}
                </div>
                <div class=" m-3">
                    <svg id="Group_31" data-name="Group 31" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                        viewBox="0 0 32 32">
                        <path id="Path_78" data-name="Path 78"
                            d="M2,25.723a1,1,0,0,0,.629.928L16,32l3.361-1.344a.5.5,0,0,0-.186-.965.491.491,0,0,0-.185.036L16,30.923l-13-5.2v-11.6a4.428,4.428,0,0,1-1-.2Z"
                            fill="#0277bd" />
                        <path id="Path_79" data-name="Path 79"
                            d="M19.681,24.189a.5.5,0,0,0-.5-.5.493.493,0,0,0-.186.036l-3,1.2L7.432,21.5a.5.5,0,0,0-.65.278.512.512,0,0,0-.035.186.5.5,0,0,0,.314.464L16,26l3.367-1.347a.5.5,0,0,0,.314-.464"
                            fill="#0277bd" />
                        <path id="Path_80" data-name="Path 80"
                            d="M31.5,25.126h-.087a1.368,1.368,0,0,1-.967-2.336l.061-.061a.5.5,0,0,0,0-.707l-.265-.265-.264-.264a.5.5,0,0,0-.707,0l-.061.06a1.368,1.368,0,0,1-2.336-.967V20.5a.5.5,0,0,0-.5-.5h-.748a.5.5,0,0,0-.5.5v.086a1.368,1.368,0,0,1-2.336.967l-.061-.06a.5.5,0,0,0-.707,0l-.265.264-.265.265a.5.5,0,0,0,0,.707l.061.061a1.368,1.368,0,0,1-.967,2.336H20.5a.5.5,0,0,0-.5.5v.748a.5.5,0,0,0,.5.5h.086a1.368,1.368,0,0,1,.967,2.336l-.061.061a.5.5,0,0,0,0,.707l.265.264.265.265a.5.5,0,0,0,.707,0l.061-.061a1.368,1.368,0,0,1,2.336.968V31.5a.5.5,0,0,0,.5.5h.748a.5.5,0,0,0,.5-.5v-.086a1.368,1.368,0,0,1,2.336-.968l.061.061a.5.5,0,0,0,.707,0l.264-.265.265-.264a.5.5,0,0,0,0-.707l-.061-.061a1.368,1.368,0,0,1,.967-2.336H31.5a.5.5,0,0,0,.5-.5v-.748a.5.5,0,0,0-.5-.5M29.171,29a2.373,2.373,0,0,0,.118.285,2.368,2.368,0,0,0-3.171,1.078,2.22,2.22,0,0,0-.118.285,2.369,2.369,0,0,0-3-1.481,2.516,2.516,0,0,0-.285.118A2.367,2.367,0,0,0,21.348,26a2.369,2.369,0,0,0,1.48-3,2.344,2.344,0,0,0-.118-.285,2.37,2.37,0,0,0,3.172-1.077A2.516,2.516,0,0,0,26,21.348a2.367,2.367,0,0,0,3,1.48,2.28,2.28,0,0,0,.285-.118,2.37,2.37,0,0,0,1.077,3.172,2.457,2.457,0,0,0,.286.118,2.367,2.367,0,0,0-1.481,3"
                            fill="#0277bd" />
                        <path id="Path_81" data-name="Path 81" d="M27.5,26A1.5,1.5,0,1,0,26,27.5,1.5,1.5,0,0,0,27.5,26"
                            fill="#0277bd" />
                        <path id="Path_82" data-name="Path 82"
                            d="M16,0A46.43,46.43,0,0,1,0,8.4v2a3.451,3.451,0,0,0,5.333,2.133,3.452,3.452,0,0,0,5.333,2.134A3.453,3.453,0,0,0,16,16.8a3.451,3.451,0,0,0,5.333-2.133,3.451,3.451,0,0,0,5.333-2.134A3.454,3.454,0,0,0,32,10.4v-2A46.421,46.421,0,0,1,16,0M31.021,10.194a2.452,2.452,0,0,1-3.788,1.515,1,1,0,0,0-1.545.618A2.453,2.453,0,0,1,21.9,13.843a1,1,0,0,0-1.545.618A2.451,2.451,0,0,1,16,15.434a2.452,2.452,0,0,1-4.355-.973,1,1,0,0,0-1.545-.618,2.454,2.454,0,0,1-3.789-1.516,1,1,0,0,0-1.184-.772,1.015,1.015,0,0,0-.361.154A2.451,2.451,0,0,1,.978,10.194V9.148A47.458,47.458,0,0,0,16,1.277,47.442,47.442,0,0,0,31.021,9.148Z"
                            fill="#0277bd" />
                    </svg>
                </div>
                <a href="{{ route('seller.shop.index') }}" class="btn btn-primary">
                    {{ translate('Go to setting') }}
                </a>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="card mb-4 p-4 text-center bg-soft-primary">
                <div class="fs-16 fw-600 text-primary">
                    {{ translate('Payment Settings') }}
                </div>
                <div class=" m-3">
                    <svg id="Group_30" data-name="Group 30" xmlns="http://www.w3.org/2000/svg" width="31.999" height="32"
                        viewBox="0 0 31.999 32">
                        <path id="Path_83" data-name="Path 83"
                            d="M96.2,30.593a.5.5,0,0,1,.314-.464L103.6,27.3a.484.484,0,0,1,.185-.036.5.5,0,0,1,.185.965l-7.087,2.831a.5.5,0,0,1-.686-.464"
                            transform="translate(-92.946 -10)" fill="#0277bd" />
                        <path id="Path_84" data-name="Path 84"
                            d="M96.2,33.537a.5.5,0,0,1,.314-.464l4.615-1.844a.493.493,0,0,1,.186-.036.5.5,0,0,1,.186.964L96.887,34a.5.5,0,0,1-.686-.464"
                            transform="translate(-92.946 -10)" fill="#0277bd" />
                        <path id="Path_85" data-name="Path 85"
                            d="M117.171,10a2.017,2.017,0,0,0-.744.143L94.205,19.021a2,2,0,0,0-1.259,1.857V40a2,2,0,0,0,2.746,1.857l15.795-6.31a.5.5,0,1,0-.372-.929L95.32,40.928a.985.985,0,0,1-.372.072,1,1,0,0,1-1-1V28.7l24.225-9.679v8.306a.5.5,0,0,0,1,0V12a2,2,0,0,0-2-2m1,5.82L93.947,25.5v-4.62a1,1,0,0,1,.63-.928L116.8,11.071a1,1,0,0,1,1.373.929Z"
                            transform="translate(-92.946 -10)" fill="#0277bd" />
                        <path id="Path_86" data-name="Path 86"
                            d="M123.686,32.181,115.1,28.752a4.007,4.007,0,0,0-7.193-1.8l2.671,1.067a1,1,0,1,1-.744,1.857l-2.671-1.067a4.005,4.005,0,0,0,6.449,3.654L122.2,35.9a2,2,0,1,0,1.487-3.714m.186,2.228a1,1,0,0,1-1.3.557L113.4,31.3a3.006,3.006,0,0,1-5.08-.952l1.149.459a2,2,0,1,0,1.488-3.714l-1.149-.459a3,3,0,0,1,4.336,2.809l9.173,3.665a1,1,0,0,1,.558,1.3"
                            transform="translate(-92.946 -10)" fill="#0277bd" />
                    </svg>
                </div>
                <a href="{{ route('seller.profile.index') }}" class="btn btn-primary">
                    {{ translate('Configure Now') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title text-primary">
                <h6 class="mb-0">{{ translate('Top 12 Products') }}</h6>
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
