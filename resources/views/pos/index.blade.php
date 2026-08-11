@extends('backend.layouts.app')

@section('content')

<section class="">
    <form class="" action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gutters-5">
            <div class="col-md">
                <div class="row gutters-5 mb-3">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input class="form-control form-control-lg" type="text" name="keyword" value="{{$product->name}}" placeholder="{{ translate('Search by Product Name/Barcode') }}" onkeyup="filterProducts()">
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="shop_id" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (filter_by_bloc(\App\Models\Shop::with('user')->where("is_show", 1))->get() as $key => $shop)
                                <option value="{{ $shop->user->id }}" {{!empty($seller_id) && $seller_id == $shop->user->id ? 'selected' : ''}}>{{ $shop->name }} @if($shop->user) ({{$shop->user->email}}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <select name="poscategory" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Categories') }}</option>
                            @foreach (\App\Models\Category::all() as $key => $category)
                                <option value="category-{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <select name="brand"  class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Brands') }}</option>
                            @foreach (\App\Models\Brand::all() as $key => $brand)
                                <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <select name="order_by_price"  class="form-control form-control-lg aiz-selectpicker" onchange="filterProducts()">
                            <option value="">{{ translate('Sort by price') }}</option>
                            <option value="ASC">{{ translate('Ascending order (from smallest to largest)') }}</option>
                            <option value="DESC">{{ translate('Descending order (from largest to smallest)') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <button class="btn btn-md btn-primary" type="reset" onclick="reset_form();setTimeout(filterProducts, 500)">{{ translate('Reset') }}</button>
                    </div>
                </div>
                <div class="aiz-pos-product-list c-scrollbar-light">
                    <div class="d-flex flex-wrap justify-content-center" id="product-list">

                    </div>
                    <div id="load-more" class="text-center">

                    </div>
                </div>
            </div>
            <div class="col-md-auto w-md-350px w-lg-400px w-xl-500px">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex border-bottom pb-3">
                            <div class="flex-grow-1">
                                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                    <option value="">{{translate('Walk In Customer')}}</option>
                                    @foreach ($customers as $key => $customer)
										<option value="{{ $customer->id }}" data-contact="{{ $customer->email }}" {{$customer->id == $customer_id ? 'selected' : ''}}>
										    @if ($customer->is_virtual_user == 1)   (<font color="red">{{translate('Virtual')}}</font>)@endif
											{{ $customer->name }}
                                            @if($customer->is_virtual == 1) (<font color="red">{{translate('Virtual')}}</font>) @endif
                                            @if($customer->total_conversation) (<font color="red">o</font>) @endif
                                            @if($customer->total_orders) (<font color="red">⭐</font>) @endif
										</option>
									@endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-icon btn-soft-dark ml-3 mr-0" data-target="#new-customer" data-toggle="modal">
								<i class="las la-truck"></i>
							</button>
                        </div>

                        <div class="d-flex border-bottom pb-3">
                            <div class="flex-grow-1">
                                <select name="order_type" id="order_type" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                                        <option value="">{{ translate('Order Type') }}</option>
										<option value="24">{{ translate('Regular Order') }}</option>
                                        <!-- <option value="24">{{ translate('Standard Order') }}</option> -->
                                        <option value="6" selected>{{ translate('Urgent Order') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="" id="cart-details">
                            <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
                                @php
                                    $subtotal = 0;
                                    $tax = 0;
                                    $total_shipping = 0;
                                @endphp
                                @if (Session::has('pos.cart'))
                                    <ul class="list-group list-group-flush">
                                    @forelse (Session::get('pos.cart') as $key => $cartItem)
                                        @php
                                            $stock = \App\Models\ProductStock::find($cartItem['stock_id']);
                                            if ($stock){
                                                $subtotal += $cartItem['price']*$cartItem['quantity'];
                                                $tax += $cartItem['tax']*$cartItem['quantity'];

                                                $total_shipping += (float) $stock->product->shipping_cost;
                                            }
                                        @endphp
                                            @if ($stock)
                                                <li class="list-group-item py-0 pl-2">
                                                    <div class="row gutters-5 align-items-center">
                                                        <div class="col-auto w-60px">
                                                            <div class="row no-gutters align-items-center flex-column aiz-plus-minus">
                                                                <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="plus" data-field="qty-{{ $key }}">
                                                                    <i class="las la-plus"></i>
                                                                </button>
                                                                <input type="text" name="qty-{{ $key }}" id="qty-{{ $key }}" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $cartItem['quantity'] }}" min="{{ $stock->product->min_qty }}" max="{{ $stock->qty }}" onchange="updateQuantity({{ $key }})">
                                                                <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="minus" data-field="qty-{{ $key }}">
                                                                    <i class="las la-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="text-truncate-2">{{ $stock->product->name }}</div>
                                                            <span class="span badge badge-inline fs-12 badge-soft-secondary">{{ $cartItem['variant'] }}</span>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="fs-12 opacity-60">{{ single_price($cartItem['price']) }} x {{ $cartItem['quantity'] }}</div>
                                                            <div class="fs-15 fw-600">{{ single_price($cartItem['price']*$cartItem['quantity']) }}</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0" onclick="removeFromCart({{ $key }})">
                                                                <i class="las la-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endif
                                    @empty
                                        <li class="list-group-item">
                                            <div class="text-center">
                                                <i class="las la-frown la-3x opacity-50"></i>
                                                <p>{{ translate('No Product Added') }}</p>
                                            </div>
                                        </li>
                                    @endforelse
                                    </ul>
                                @else
                                    <div class="text-center">
                                        <i class="las la-frown la-3x opacity-50"></i>
                                        <p>{{ translate('No Product Added') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Sub Total')}}</span>
                                    <span>{{ single_price($subtotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Tax')}}</span>
                                    <span>{{ single_price($tax) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Shipping')}}</span>
                                    <span>{{ single_price($total_shipping) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Discount')}}</span>
                                    <span>{{ single_price(Session::get('pos.discount', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
                                    <span>{{translate('Total')}}</span>
                                    <span>{{ single_price($subtotal+$tax+$total_shipping - Session::get('pos.discount', 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pos-footer mar-btm">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="d-flex">
                            <div class="dropdown dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Discount')}}
                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="text" placeholder="Coupon Code" name="coupon_code" class="form-control" value="" required onkeydown="useCoupon()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('Flat') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-2 my-md-0">
                            <button type="button" class="btn btn-primary btn-block" onclick="orderConfirmation()">{{ translate('Place Order') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection

@section('modal')
    <!-- Address Modal -->
    <div id="new-customer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Shipping Address')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="shipping_form">
                    <div class="modal-body" id="shipping_address">


                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal" id="close-button">{{translate('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-styled btn-base-1" id="confirm-address" data-dismiss="modal">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- new address modal -->
    <div id="new-address-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Shipping Address')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-horizontal" action="{{ route('addresses.store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="set_customer_id" value="">
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="address">{{translate('Address')}}</label>
                                <div class="col-sm-10">
                                    <textarea placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label">{{translate('Country')}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2 control-label">
                                    <label>{{ translate('State')}}</label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>{{ translate('City')}}</label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="postal_code">{{translate('Postal code')}}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{translate('Postal code')}}" id="postal_code" name="postal_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="phone">{{translate('Phone')}}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                        <button type="submit" class="btn btn-primary btn-styled btn-base-1">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="order-confirm" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-xl">
            <div class="modal-content" id="variants">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Order Summary')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="order-confirmation">
                    <div class="p-4 text-center">
                        <i class="las la-spinner la-spin la-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="submitOrder('wallet')" class="btn btn-base-1 btn-success">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Offline Payment Modal --}}
    <div id="offlin_payment" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Offline Payment Info')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_method">{{translate('Payment Method')}}</label>
                            <div class="col-sm-9">
                                <input placeholder="{{translate('Name')}}" id="offline_payment_method" name="offline_payment_method" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_amount">{{translate('Amount')}}</label>
                            <div class="col-sm-9">
                                <input placeholder="{{translate('Amount')}}" id="offline_payment_amount" name="offline_payment_amount" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-3 control-label" for="trx_id">{{translate('Transaction ID')}}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control mb-3" id="trx_id" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Payment Proof') }}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose image') }}</div>
                                <input type="hidden" name="payment_proof" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="button" onclick="submitOrder('offline_payment')" class="btn btn-styled btn-base-1 btn-success">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 咨询对话模态框 --}}
    <div class="modal fade" id="consult_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-lg" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5" id="consult_modal_title">{{ translate('Consultation') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body gry-bg px-3 pt-3" style="max-height: 50vh; overflow-y: auto;">
                    <ul class="list-group list-group-flush ticket" id="consult_messages" style="min-height: 150px;">
                        <li class="list-group-item text-center text-muted">{{ translate('Loading...') }}</li>
                    </ul>
                </div>
                <div class="modal-footer d-block">
                    <div class="input-group">
                        <textarea class="form-control" id="consult_reply_text" rows="2" placeholder="{{ translate('Type your message...') }}"></textarea>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" onclick="sendConsultMessage()">{{ translate('Send') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 产品提问模态框 --}}
    <div class="modal fade" id="comment_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Product Queries') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body gry-bg px-3 pt-3">
                    <input type="hidden" id="comment_product_id" value="">
                    <div class="form-group">
                        <label id="comment_product_name" class="fw-600"></label>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="comment_text" rows="5" required
                                  placeholder="{{ translate('Your message about this product...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary fw-600"
                            data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="button" class="btn btn-primary fw-600" onclick="submitComment()">{{ translate('Submit') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <style>
        #consult_messages { background-color: #ebedf2; padding: 10px; }
    </style>
    <script type="text/javascript">

        var products = null;

        $(document).ready(function() {
            $('body').addClass('side-menu-closed');
            $('#product-list').on('click','.add-plus:not(.c-not-allowed)',function(){
                var stock_id = $(this).data('stock-id');
                $.post('{{ route('pos.addToCart') }}',{_token:AIZ.data.csrf, stock_id:stock_id}, function(data){
                    if(data.success == 1){
                        updateCart(data.view);
                        useCoupon(1);
                    }else{
                        AIZ.plugins.notify('danger', data.message);
                    }

                });
            });
            filterProducts();
            getShippingAddress();

            $("#load-more").on("click", ".pagination a", function () {
                $.get($(this).attr("href"),{}, function(data){
                    products = data.products || [];
                    $('#product-list').html(null);
                    setProductList(data);
                });
                return false;
            });

            $("#load-more").on("click", "#btn-jump-page", function () {
                let page_num = $(this).prev('input[name=page]').val() || 1;
                $.get($(this).data("url") + "&page=" + page_num, {}, function(data){
                    products = data.products || [];
                    $('#product-list').html(null);
                    setProductList(data);
                });
                return false;
            });
        });

        $("#confirm-address").click(function (){
            var data = new FormData($('#shipping_form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': AIZ.data.csrf
                },
                method: "POST",
                url: "{{route('pos.set-shipping-address')}}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                }
            })
        });

        function updateCart(data){
            $('#cart-details').html(data);
            AIZ.extra.plusMinus();
        }

        function filterProducts(){
            var keyword = $('input[name=keyword]').val();
            var category = $('select[name=poscategory]').val();
            var brand = $('select[name=brand]').val();
            var user_id = $('select[name=shop_id]').val();
            var order_by_price = $('select[name=order_by_price]').val();
            $.get('{{ route('pos.search_product') }}',{keyword:keyword, category:category, brand:brand, user_id:user_id, customer_id: "{{$customer_id}}", order_by_price: order_by_price}, function(data){
                products = data.products || [];
                $('#product-list').html(null);
                setProductList(data);

                // 判断是否显示用户标记
                if (data.customerHtml != '') {
                    $("select[name=user_id]").html(data.customerHtml).selectpicker("refresh");
                }
            });
        }

        function setProductList(data) {
            var page_links = data.page_links || '';
            data = data.products || [];
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-140px w-xl-180px w-xxl-210px mx-2">
                        <div class="card bg-white product-card hov-container">
                            <div class="position-relative c-pointer">
                                <div class=""><span class="absolute-top-left mt-1 ml-1 mr-0">
                                    ${data.data[i].qty > 0
                        ? `<span class="badge badge-inline badge-success fs-13">{{ translate('In stock') }}`
                        : `<span class="badge badge-inline badge-danger fs-13">{{ translate('Out of stock') }}` }
                                    : ${data.data[i].qty}</span>
                                </span>
                                ${data.data[i].variant != null
                        ? `<span class="badge badge-inline badge-warning absolute-bottom-left mb-1 ml-1 mr-0 fs-13 text-truncate">${data.data[i].variant}</span>`
                        : '' }
                                <img src="${data.data[i].thumbnail_image }" class="card-img-top img-fit h-120px h-xl-180px h-xxl-210px mw-100 mx-auto" >
                                </div>
                                <div class="add-plus absolute-full rounded overflow-hidden hov-box ${data.data[i].qty <= 0 ? 'c-not-allowed' : '' }" data-stock-id="${data.data[i].stock_id}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                                <i class="las la-plus absolute-center la-6x text-white"></i>
                            </div>
                            </div>
                            <div class="card-body p-2 p-xl-3">
                                <div class="text-truncate fw-600 fs-14 mb-2">${data.data[i].name}</div>
                                <div class="">
                                    ${data.data[i].price != data.data[i].base_price
                                        ? `<del class="mr-2 ml-0">${data.data[i].base_price}</del><span>${data.data[i].price}</span>`
                                        : `<span>${data.data[i].base_price}</span>`
                                    }
                                </div>
                                <div class="mt-2">
                                    <a class="btn btn-soft-primary btn-sm" href="javascript:void(0);" onclick="product_consult('${data.data[i].id}', '${data.data[i].name2}', '${data.data[i].slug_url}')" title="{{ translate('Consult') }}">{{ translate('Consult') }}</a>
                                    <a class="btn btn-soft-info btn-sm" href="javascript:void(0);" onclick="product_comment('${data.data[i].id}', '${data.data[i].name2}')" title="{{ translate('Ask question') }}">{{ translate('Ask question') }}</a>
                                    <a class="btn btn-soft-secondary btn-sm" href="${data.data[i].slug_url}" target="_blank" title="{{ translate('View') }}">{{ translate('View') }}</a>
                </div>
</div>

        </div>
    </div>`
                );
            }
            /*if (data.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Load More.') }}');
            }
            else {
                $('#load-more').find('.btn').html('{{ translate('Nothing more found.') }}');
            }*/

            $('#load-more').html(page_links);
        }

        function removeFromCart(key){
            $.post('{{ route('pos.removeFromCart') }}', {_token:AIZ.data.csrf, key:key}, function(data){
                updateCart(data);
                useCoupon(1);
            });
        }

        function addToCart(product_id, variant, quantity){
            $.post('{{ route('pos.addToCart') }}',{_token:AIZ.data.csrf, product_id:product_id, variant:variant, quantity, quantity}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateQuantity(key){
            $.post('{{ route('pos.updateQuantity') }}',{_token:AIZ.data.csrf, key:key, quantity: $('#qty-'+key).val()}, function(data){
                if(data.success == 1){
                    updateCart(data.view);
                    useCoupon(1);
                }else{
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }

        function setDiscount(){
            var discount = $('input[name=discount]').val();
            $.post('{{ route('pos.setDiscount') }}',{_token:AIZ.data.csrf, discount:discount}, function(data){
                updateCart(data);
            });
        }

        function useCoupon(update) {
            if (event.keyCode == 13 || update) {
                var coupon_code = $('input[name=coupon_code]').val();
                if (coupon_code) {
                    $.post('{{ route('pos.useCoupon') }}',{_token:AIZ.data.csrf, coupon_code:coupon_code}, function(data) {
                        if (!data.success) {
                            AIZ.plugins.notify('warning', data.message || '使用错误');
                        }

                        updateCart(data.html);
                    });
                }
            }
        }

        function setShipping(){
            var shipping = $('input[name=shipping]').val();
            $.post('{{ route('pos.setShipping') }}',{_token:AIZ.data.csrf, shipping:shipping}, function(data){
                updateCart(data);
                useCoupon(1);
            });
        }

        function getShippingAddress(){
            console.log($('select[name=user_id]').val(),88888)
            if($('select[name=user_id]').val() !=''){
                $.post('{{ route('pos.getShippingAddress') }}',{_token:AIZ.data.csrf, id:$('select[name=user_id]').val()}, function(data){
                    if (data) {
                        $('#new-customer').modal('show');
                        $('#shipping_address').html(data);
                    }
                });
            }
        }

        function add_new_address(){
            var customer_id = $('#customer_id').val();
            $('#set_customer_id').val(customer_id);
            $('#new-address-modal').modal('show');
            $("#close-button").click();
        }

        function orderConfirmation(){
            let user_id = $("select[name=user_id]").val();
            if (!user_id) {
                AIZ.plugins.notify('danger', '请选择一个买家');
                return false;
            }

            $('#order-confirmation').html(`<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $('#order-confirm').modal('show');
            $.post('{{ route('pos.getOrderSummary') }}',{_token:AIZ.data.csrf}, function(data){
                $('#order-confirmation').html(data);
            });
        }

        function oflinePayment(){
            $('#offlin_payment').modal('show');
        }

        // 咨询对话
        var currentConsultProductId = null;
        var consultPollTimer = null;

        function product_consult(product_id, product_name, slug) {
            let customer_id = $("select[name=user_id]").val();
            if (!customer_id) {
                AIZ.plugins.notify('danger', '请先在右侧选择一个客户');
                return false;
            }

            currentConsultProductId = product_id;
            $('#consult_modal_title').text(product_name);
            $('#consult_messages').html('<li class="list-group-item text-center text-muted">{{ translate("Loading...") }}</li>');
            $('#consult_modal').modal('show');

            loadConsultMessages();
            if (consultPollTimer) clearInterval(consultPollTimer);
            consultPollTimer = setInterval(loadConsultMessages, 5000);
        }

        function loadConsultMessages() {
            if (!currentConsultProductId) return;
            let customer_id = $("select[name=user_id]").val();
            $.post('{{ route('pos.consult.messages') }}', {
                _token: AIZ.data.csrf,
                product_id: currentConsultProductId,
                customer_id: customer_id
            }, function(res) {
                if (res.success) {
                    renderConsultMessages(res.messages);
                }
            });
        }

        function renderConsultMessages(messages) {
            var html = '';
            for (var i = 0; i < messages.length; i++) {
                var msg = messages[i];
                var isMine = msg.is_mine;
                var nameHtml = msg.user_name || '';
                if (msg.shop_name) nameHtml += ' <small class="text-muted">(' + msg.shop_name + ')</small>';
                html += '<li class="list-group-item px-0 ' + (isMine ? 'mine' : '') + '">';
                if (isMine) {
                    html += '<div style="display:flex;flex-direction:column;align-items:flex-end;">';
                    html += '<div class="mb-1"><small class="text-muted mr-2">' + msg.created_at + '</small><strong>' + nameHtml + '</strong></div>';
                    html += '<div class="p-2 rounded" style="background:#d9fdd3;max-width:80%;word-break:break-word;">' + (msg.message || '') + '</div>';
                    html += '</div>';
                } else {
                    html += '<div style="display:flex;flex-direction:column;align-items:flex-start;">';
                    html += '<div class="mb-1"><strong>' + nameHtml + '</strong><small class="text-muted ml-2">' + msg.created_at + '</small></div>';
                    html += '<div class="p-2 rounded" style="background:#fff;max-width:80%;word-break:break-word;">' + (msg.message || '') + '</div>';
                    html += '</div>';
                }
                html += '</li>';
            }
            if (!messages.length) {
                html = '<li class="list-group-item text-center text-muted">{{ translate("No messages yet. Start a conversation!") }}</li>';
            }
            $('#consult_messages').html(html);
            var el = document.getElementById('consult_messages');
            if (el && el.parentElement) el.parentElement.scrollTop = el.parentElement.scrollHeight;
        }

        function sendConsultMessage() {
            let msg = $('#consult_reply_text').val().trim();
            if (!msg || !currentConsultProductId) return;
            let customer_id = $("select[name=user_id]").val();

            $.post('{{ route('pos.consult.send') }}', {
                _token: AIZ.data.csrf,
                product_id: currentConsultProductId,
                customer_id: customer_id,
                message: msg
            }, function(res) {
                if (res.success) {
                    $('#consult_reply_text').val('');
                    loadConsultMessages();
                }
            }).fail(function(xhr) {
                var emsg = '发送失败';
                try { var r = JSON.parse(xhr.responseText); if (r.message) emsg = r.message; } catch(e) {}
                AIZ.plugins.notify('danger', emsg);
            });
        }

        $('#consult_modal').on('hidden.bs.modal', function () {
            if (consultPollTimer) { clearInterval(consultPollTimer); consultPollTimer = null; }
            currentConsultProductId = null;
        });

        $('#consult_reply_text').on('keydown', function(e) {
            if (e.keyCode === 13 && !e.shiftKey) { e.preventDefault(); sendConsultMessage(); }
        });

        // 产品提问
        function product_comment(product_id, product_name) {
            let customer_id = $("select[name=user_id]").val();
            if (!customer_id) {
                AIZ.plugins.notify('danger', '请先在右侧选择一个客户');
                return false;
            }
            $('#comment_product_id').val(product_id);
            $('#comment_product_name').text(product_name);
            $('#comment_text').val('');
            $('#comment_modal').modal('show');
        }

        function submitComment() {
            let product_id = $('#comment_product_id').val();
            let customer_id = $("select[name=user_id]").val();
            let message = $('#comment_text').val().trim();
            if (!message) {
                AIZ.plugins.notify('danger', '{{ translate("Please enter a message") }}');
                return false;
            }

            $.post('{{ route('pos.product.comment') }}', {
                _token: AIZ.data.csrf,
                product_id: product_id,
                customer_id: customer_id,
                message: message
            }, function(res) {
                if (res.success) {
                    AIZ.plugins.notify('success', res.message || '{{ translate("Comment submitted") }}');
                    $('#comment_modal').modal('hide');
                }
            }).fail(function() {
                AIZ.plugins.notify('danger', '提交失败，请重试');
            });
        }

        var order_submitting = false;
        function submitOrder(payment_type){
            var user_id = $('select[name=user_id]').val();
            var shipping = $('input[name=shipping]:checked').val();
            var discount = $('input[name=discount]').val();
            var shipping_address = $('input[name=address_id]:checked').val();
            var offline_payment_method = $('input[name=offline_payment_method]').val();
            var offline_payment_amount = $('input[name=offline_payment_amount]').val();
            var offline_trx_id = $('input[name=trx_id]').val();
            var offline_payment_proof = $('input[name=payment_proof]').val();
            var order_type = $('select[name=order_type]').val();

            if(order_type==""){

                AIZ.plugins.notify('danger', '订单类型不能为空');
                return false;

            }

            if (order_submitting) {
                return;
            }

            order_submitting = true;
            $('#order-confirmation').html(`<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $.post('{{ route('pos.order_place') }}',{
                _token                  : AIZ.data.csrf,
                user_id                 : user_id,
                shipping_address        : shipping_address,
                payment_type            : payment_type,
                shipping                : shipping,
                discount                : discount,
                offline_payment_method  : offline_payment_method,
                offline_payment_amount  : offline_payment_amount,
                offline_trx_id          : offline_trx_id,
                offline_payment_proof   : offline_payment_proof,
                order_type              : order_type

            }, function(data){
                if(data.success == 1){
                    AIZ.plugins.notify('success', data.message );
                    location.reload();
                }
                else{
                    order_submitting = false;
                    AIZ.plugins.notify('danger', data.message );
                }
            });
        }


        //address
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    </script>
@endsection
