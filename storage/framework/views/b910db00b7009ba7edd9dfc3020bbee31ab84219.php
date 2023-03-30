<?php $__env->startSection('content'); ?>

<section class="">
    <form class="" action="" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row gutters-5">
            <div class="col-md">
                <div class="row gutters-5 mb-3">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input class="form-control form-control-lg" type="text" name="keyword" placeholder="<?php echo e(translate('Search by Product Name/Barcode')); ?>" onkeyup="filterProducts()">
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="shop_id" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value=""><?php echo e(translate('All Sellers')); ?></option>
                            <?php $__currentLoopData = \App\Models\Shop::with('user')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($shop->user->id); ?>"><?php echo e($shop->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="poscategory" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value=""><?php echo e(translate('All Categories')); ?></option>
                            <?php $__currentLoopData = \App\Models\Category::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="category-<?php echo e($category->id); ?>"><?php echo e($category->getTranslation('name')); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="brand"  class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value=""><?php echo e(translate('All Brands')); ?></option>
                            <?php $__currentLoopData = \App\Models\Brand::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($brand->id); ?>"><?php echo e($brand->getTranslation('name')); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="aiz-pos-product-list c-scrollbar-light">
                    <div class="d-flex flex-wrap justify-content-center" id="product-list">

                    </div>
                    <div id="load-more" class="text-center">
                        <div class="fs-14 d-inline-block fw-600 btn btn-soft-primary c-pointer" onclick="loadMoreProduct()"><?php echo e(translate('Loading..')); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-auto w-md-350px w-lg-400px w-xl-500px">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex border-bottom pb-3">
                            <div class="flex-grow-1">
                                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                    <option value=""><?php echo e(translate('Walk In Customer')); ?></option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($customer->id); ?>" data-contact="<?php echo e($customer->email); ?>">


										    <?php if($customer->is_virtual_user == 1): ?>   (<font color="red"><?php echo e(translate('Virtual')); ?></font>)<?php endif; ?>


											<?php echo e($customer->name); ?> <?php if($customer->is_virtual == 1): ?> (<font color="red"><?php echo e(translate('Virtual')); ?></font>) <?php endif; ?>
										</option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-icon btn-soft-dark ml-3 mr-0" data-target="#new-customer" data-toggle="modal">
								<i class="las la-truck"></i>
							</button>
                        </div>
                        
                        <div class="d-flex border-bottom pb-3">
                            <div class="flex-grow-1">
                                <select name="order_type" id="order_type" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                                        <option value="">订单类型</option>
										<option value="24">普通订单</option>
                                        <option value="24">常规订单</option>
                                        <option value="6">加急订单</option>
                                </select>
                            </div>
                        </div>

                        <div class="" id="cart-details">
                            <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
                                <?php
                                    $subtotal = 0;
                                    $tax = 0;
                                    /*if (Session::has('pos.cart')){
                                        dd(Session::get('pos.cart'));
                                    }*/
                                ?>
                                <?php if(Session::has('pos.cart')): ?>
                                    <ul class="list-group list-group-flush">
                                    <?php $__empty_1 = true; $__currentLoopData = Session::get('pos.cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $stock = \App\Models\ProductStock::find($cartItem['stock_id']);
                                            if ($stock){
                                            $subtotal += $cartItem['price']*$cartItem['quantity'];
                                            $tax += $cartItem['tax']*$cartItem['quantity'];
                                            }
                                        ?>
                                            <?php if($stock): ?>
                                                <li class="list-group-item py-0 pl-2">
                                                    <div class="row gutters-5 align-items-center">
                                                        <div class="col-auto w-60px">
                                                            <div class="row no-gutters align-items-center flex-column aiz-plus-minus">
                                                                <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="plus" data-field="qty-<?php echo e($key); ?>">
                                                                    <i class="las la-plus"></i>
                                                                </button>
                                                                <input type="text" name="qty-<?php echo e($key); ?>" id="qty-<?php echo e($key); ?>" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="<?php echo e($cartItem['quantity']); ?>" min="<?php echo e($stock->product->min_qty); ?>" max="<?php echo e($stock->qty); ?>" onchange="updateQuantity(<?php echo e($key); ?>)">
                                                                <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="minus" data-field="qty-<?php echo e($key); ?>">
                                                                    <i class="las la-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="text-truncate-2"><?php echo e($stock->product->name); ?></div>
                                                            <span class="span badge badge-inline fs-12 badge-soft-secondary"><?php echo e($cartItem['variant']); ?></span>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="fs-12 opacity-60"><?php echo e(single_price($cartItem['price'])); ?> x <?php echo e($cartItem['quantity']); ?></div>
                                                            <div class="fs-15 fw-600"><?php echo e(single_price($cartItem['price']*$cartItem['quantity'])); ?></div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0" onclick="removeFromCart(<?php echo e($key); ?>)">
                                                                <i class="las la-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <li class="list-group-item">
                                            <div class="text-center">
                                                <i class="las la-frown la-3x opacity-50"></i>
                                                <p><?php echo e(translate('No Product Added')); ?></p>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="text-center">
                                        <i class="las la-frown la-3x opacity-50"></i>
                                        <p><?php echo e(translate('No Product Added')); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span><?php echo e(translate('Sub Total')); ?></span>
                                    <span><?php echo e(single_price($subtotal)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span><?php echo e(translate('Tax')); ?></span>
                                    <span><?php echo e(single_price($tax)); ?></span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span><?php echo e(translate('Shipping')); ?></span>
                                    <span><?php echo e(single_price(Session::get('pos.shipping', 0))); ?></span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span><?php echo e(translate('Discount')); ?></span>
                                    <span><?php echo e(single_price(Session::get('pos.discount', 0))); ?></span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
                                    <span><?php echo e(translate('Total')); ?></span>
                                    <span><?php echo e(single_price($subtotal+$tax+Session::get('pos.shipping', 0) - Session::get('pos.discount', 0))); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pos-footer mar-btm">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="d-flex">
                            <div class="dropdown mr-3 ml-0 dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    <?php echo e(translate('Shipping')); ?>

                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="shipping" class="form-control" value="<?php echo e(Session::get('pos.shipping', 0)); ?>" required onchange="setShipping()">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?php echo e(translate('Flat')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    <?php echo e(translate('Discount')); ?>

                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="discount" class="form-control" value="<?php echo e(Session::get('pos.discount', 0)); ?>" required onchange="setDiscount()">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?php echo e(translate('Flat')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown dropup">
                                <div class="my-2 my-md-0">
                                    <div class="form-group">
                                        <input class="form-control" type="datetime-local" name="effectivetime" placeholder="<?php echo e(translate('Search by Product Name/Barcode')); ?>" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-2 my-md-0">
                            <button type="button" class="btn btn-primary btn-block" onclick="orderConfirmation()"><?php echo e(translate('Place Order')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- Address Modal -->
    <div id="new-customer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6"><?php echo e(translate('Shipping Address')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="shipping_form">
                    <div class="modal-body" id="shipping_address">


                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal" id="close-button"><?php echo e(translate('Close')); ?></button>
                    <button type="button" class="btn btn-primary btn-styled btn-base-1" id="confirm-address" data-dismiss="modal"><?php echo e(translate('Confirm')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- new address modal -->
    <div id="new-address-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6"><?php echo e(translate('Shipping Address')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-horizontal" action="<?php echo e(route('addresses.store')); ?>" method="POST" enctype="multipart/form-data">
                	<?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="set_customer_id" value="">
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="address"><?php echo e(translate('Address')); ?></label>
                                <div class="col-sm-10">
                                    <textarea placeholder="<?php echo e(translate('Address')); ?>" id="address" name="address" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label"><?php echo e(translate('Country')); ?></label>
                                <div class="col-sm-10">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="<?php echo e(translate('Select your country')); ?>" name="country_id" required>
                                        <option value=""><?php echo e(translate('Select your country')); ?></option>
                                        <?php $__currentLoopData = \App\Models\Country::where('status', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2 control-label">
                                    <label><?php echo e(translate('State')); ?></label>
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
                                    <label><?php echo e(translate('City')); ?></label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="postal_code"><?php echo e(translate('Postal code')); ?></label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="<?php echo e(translate('Postal code')); ?>" id="postal_code" name="postal_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="phone"><?php echo e(translate('Phone')); ?></label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="<?php echo e(translate('Phone')); ?>" id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal"><?php echo e(translate('Close')); ?></button>
                        <button type="submit" class="btn btn-primary btn-styled btn-base-1"><?php echo e(translate('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="order-confirm" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-xl">
            <div class="modal-content" id="variants">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6"><?php echo e(translate('Order Summary')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="order-confirmation">
                    <div class="p-4 text-center">
                        <i class="las la-spinner la-spin la-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal"><?php echo e(translate('Close')); ?></button>
                    <button type="button" onclick="oflinePayment()" class="btn btn-base-1 btn-warning"><?php echo e(translate('Offline Payment')); ?></button>
                    <button type="button" onclick="submitOrder('cash_on_delivery')" class="btn btn-base-1 btn-info"><?php echo e(translate('Confirm with COD')); ?></button>
                    <button type="button" onclick="submitOrder('cash')" class="btn btn-base-1 btn-success"><?php echo e(translate('Confirm with Cash')); ?></button>
                </div>
            </div>
        </div>
    </div>

    
    <div id="offlin_payment" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6"><?php echo e(translate('Offline Payment Info')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_method"><?php echo e(translate('Payment Method')); ?></label>
                            <div class="col-sm-9">
                                <input placeholder="<?php echo e(translate('Name')); ?>" id="offline_payment_method" name="offline_payment_method" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_amount"><?php echo e(translate('Amount')); ?></label>
                            <div class="col-sm-9">
                                <input placeholder="<?php echo e(translate('Amount')); ?>" id="offline_payment_amount" name="offline_payment_amount" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-3 control-label" for="trx_id"><?php echo e(translate('Transaction ID')); ?></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control mb-3" id="trx_id" name="trx_id" placeholder="<?php echo e(translate('Transaction ID')); ?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php echo e(translate('Payment Proof')); ?></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                                </div>
                                <div class="form-control file-amount"><?php echo e(translate('Choose image')); ?></div>
                                <input type="hidden" name="payment_proof" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal"><?php echo e(translate('Close')); ?></button>
                    <button type="button" onclick="submitOrder('offline_payment')" class="btn btn-styled btn-base-1 btn-success"><?php echo e(translate('Confirm')); ?></button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script type="text/javascript">

        var products = null;

        $(document).ready(function(){
            $('body').addClass('side-menu-closed');
            $('#product-list').on('click','.add-plus:not(.c-not-allowed)',function(){
                var stock_id = $(this).data('stock-id');
                $.post('<?php echo e(route('pos.addToCart')); ?>',{_token:AIZ.data.csrf, stock_id:stock_id}, function(data){
                    if(data.success == 1){
                        updateCart(data.view);
                    }else{
                        AIZ.plugins.notify('danger', data.message);
                    }

                });
            });
            filterProducts();
            getShippingAddress();
        });

        $("#confirm-address").click(function (){
            var data = new FormData($('#shipping_form')[0]);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': AIZ.data.csrf
                },
                method: "POST",
                url: "<?php echo e(route('pos.set-shipping-address')); ?>",
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
            $.get('<?php echo e(route('pos.search_product')); ?>',{keyword:keyword, category:category, brand:brand, user_id:user_id}, function(data){
                products = data;
                $('#product-list').html(null);
                setProductList(data);
            });
        }

        function loadMoreProduct(){
            if(products != null && products.links.next != null){
                $('#load-more').find('.btn').html('<?php echo e(translate('Loading..')); ?>');
                $.get(products.links.next,{}, function(data){
                    products = data;
                    setProductList(data);
                });
            }
        }

        function setProductList(data){
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-140px w-xl-180px w-xxl-210px mx-2">
                        <div class="card bg-white c-pointer product-card hov-container">
                            <div class="position-relative">
                                <span class="absolute-top-left mt-1 ml-1 mr-0">
                                    ${data.data[i].qty > 0
                                        ? `<span class="badge badge-inline badge-success fs-13"><?php echo e(translate('In stock')); ?>`
                                        : `<span class="badge badge-inline badge-danger fs-13"><?php echo e(translate('Out of stock')); ?>` }
                                    : ${data.data[i].qty}</span>
                                </span>
                                ${data.data[i].variant != null
                                    ? `<span class="badge badge-inline badge-warning absolute-bottom-left mb-1 ml-1 mr-0 fs-13 text-truncate">${data.data[i].variant}</span>`
                                    : '' }
                                <img src="${data.data[i].thumbnail_image }" class="card-img-top img-fit h-120px h-xl-180px h-xxl-210px mw-100 mx-auto" >
                            </div>
                            <div class="card-body p-2 p-xl-3">
                                <div class="text-truncate fw-600 fs-14 mb-2">${data.data[i].name}</div>
                                <div class="">
                                    ${data.data[i].price != data.data[i].base_price
                                        ? `<del class="mr-2 ml-0">${data.data[i].base_price}</del><span>${data.data[i].price}</span>`
                                        : `<span>${data.data[i].base_price}</span>`
                                    }
                                </div>
                            </div>
                            <div class="add-plus absolute-full rounded overflow-hidden hov-box ${data.data[i].qty <= 0 ? 'c-not-allowed' : '' }" data-stock-id="${data.data[i].stock_id}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                                <i class="las la-plus absolute-center la-6x text-white"></i>
                            </div>
                        </div>
                    </div>`
                );
            }
            if (data.links.next != null) {
                $('#load-more').find('.btn').html('<?php echo e(translate('Load More.')); ?>');
            }
            else {
                $('#load-more').find('.btn').html('<?php echo e(translate('Nothing more found.')); ?>');
            }
        }

        function removeFromCart(key){
            $.post('<?php echo e(route('pos.removeFromCart')); ?>', {_token:AIZ.data.csrf, key:key}, function(data){
                updateCart(data);
            });
        }

        function addToCart(product_id, variant, quantity){
            $.post('<?php echo e(route('pos.addToCart')); ?>',{_token:AIZ.data.csrf, product_id:product_id, variant:variant, quantity, quantity}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateQuantity(key){
            $.post('<?php echo e(route('pos.updateQuantity')); ?>',{_token:AIZ.data.csrf, key:key, quantity: $('#qty-'+key).val()}, function(data){
                if(data.success == 1){
                    updateCart(data.view);
                }else{
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }

        function setDiscount(){
            var discount = $('input[name=discount]').val();
            $.post('<?php echo e(route('pos.setDiscount')); ?>',{_token:AIZ.data.csrf, discount:discount}, function(data){
                updateCart(data);
            });
        }

        function setShipping(){
            var shipping = $('input[name=shipping]').val();
            $.post('<?php echo e(route('pos.setShipping')); ?>',{_token:AIZ.data.csrf, shipping:shipping}, function(data){
                updateCart(data);
            });
        }

        function getShippingAddress(){
            console.log($('select[name=user_id]').val(),88888)
            if($('select[name=user_id]').val() !=''){
                $.post('<?php echo e(route('pos.getShippingAddress')); ?>',{_token:AIZ.data.csrf, id:$('select[name=user_id]').val()}, function(data){
                    $('#new-customer').modal('show');
                    $('#shipping_address').html(data);
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
            
            $('#order-confirmation').html(`<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $('#order-confirm').modal('show');
            $.post('<?php echo e(route('pos.getOrderSummary')); ?>',{_token:AIZ.data.csrf}, function(data){
                $('#order-confirmation').html(data);
            });
        }

        function oflinePayment(){
            $('#offlin_payment').modal('show');
        }

        function submitOrder(payment_type){
            var user_id = $('select[name=user_id]').val();
            var shipping = $('input[name=shipping]:checked').val();
            var discount = $('input[name=discount]').val();
            var shipping_address = $('input[name=address_id]:checked').val();
            var offline_payment_method = $('input[name=offline_payment_method]').val();
            var offline_payment_amount = $('input[name=offline_payment_amount]').val();
            var offline_trx_id = $('input[name=trx_id]').val();
            var offline_payment_proof = $('input[name=payment_proof]').val();
            var effectivetime = $('input[name=effectivetime]').val();
            var order_type = $('select[name=order_type]').val();
            
            if(order_type==""){
                
                AIZ.plugins.notify('danger', '订单类型不能为空');
                return false;
                
            }
            
            $.post('<?php echo e(route('pos.order_place')); ?>',{
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
                effectivetime           : effectivetime,
                order_type              : order_type

            }, function(data){
                if(data.success == 1){
                    AIZ.plugins.notify('success', data.message );
                    location.reload();
                }
                else{
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
                url: "<?php echo e(route('get-state')); ?>",
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
                url: "<?php echo e(route('get-city')); ?>",
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/pos/index.blade.php ENDPATH**/ ?>