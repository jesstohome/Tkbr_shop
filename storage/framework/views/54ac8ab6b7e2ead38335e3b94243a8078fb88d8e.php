<?php $__env->startSection('meta_title'); ?><?php echo e($detailedProduct->meta_title); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('meta_description'); ?><?php echo e($detailedProduct->meta_description); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('meta_keywords'); ?><?php echo e($detailedProduct->tags); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('meta'); ?>
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?php echo e($detailedProduct->meta_title); ?>">
    <meta itemprop="description" content="<?php echo e($detailedProduct->meta_description); ?>">
    <meta itemprop="image" content="<?php echo e(uploaded_asset($detailedProduct->meta_img)); ?>">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="<?php echo e($detailedProduct->meta_title); ?>">
    <meta name="twitter:description" content="<?php echo e($detailedProduct->meta_description); ?>">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="<?php echo e(uploaded_asset($detailedProduct->meta_img)); ?>">
    <meta name="twitter:data1" content="<?php echo e(single_price($detailedProduct->unit_price)); ?>">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?php echo e($detailedProduct->meta_title); ?>" />
    <meta property="og:type" content="og:product" />
    <meta property="og:url" content="<?php echo e(route('product', $detailedProduct->slug)); ?>" />
    <meta property="og:image" content="<?php echo e(uploaded_asset($detailedProduct->meta_img)); ?>" />
    <meta property="og:description" content="<?php echo e($detailedProduct->meta_description); ?>" />
    <meta property="og:site_name" content="<?php echo e(get_setting('meta_title')); ?>" />
    <meta property="og:price:amount" content="<?php echo e(single_price($detailedProduct->unit_price)); ?>" />
    <meta property="product:price:currency"
        content="<?php echo e(\App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code); ?>" />
    <meta property="fb:app_id" content="<?php echo e(env('FACEBOOK_PIXEL_ID')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="mb-4 pt-3">
        <div class="container">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="row">
                    <div class="col-xl-5 col-lg-6 mb-4">
                        <div class="sticky-top z-3 row gutters-10">
                            <?php
                                $photos = explode(',', $detailedProduct->photos);
                            ?>
                            <div class="col order-1 order-md-2">
                                <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb'
                                    data-fade='true' data-auto-height='true'>
                                    <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="carousel-box img-zoom rounded">
                                            <img class="img-fluid lazyload"
                                                src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                data-src="<?php echo e(uploaded_asset($photo)); ?>"
                                                onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $detailedProduct->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($stock->image != null): ?>
                                            <div class="carousel-box img-zoom rounded">
                                                <img class="img-fluid lazyload"
                                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                    data-src="<?php echo e(uploaded_asset($stock->image)); ?>"
                                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <div class="col-12 col-md-auto w-md-80px order-2 order-md-1 mt-3 mt-md-0">
                                <div class="aiz-carousel product-gallery-thumb" data-items='5'
                                    data-nav-for='.product-gallery' data-vertical='true' data-vertical-sm='false'
                                    data-focus-select='true' data-arrows='true'>
                                    <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="carousel-box c-pointer border p-1 rounded">
                                            <img class="lazyload mw-100 size-50px mx-auto"
                                                src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                data-src="<?php echo e(uploaded_asset($photo)); ?>"
                                                onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $detailedProduct->stocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($stock->image != null): ?>
                                            <div class="carousel-box c-pointer border p-1 rounded"
                                                data-variation="<?php echo e($stock->variant); ?>">
                                                <img class="lazyload mw-100 size-50px mx-auto"
                                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                    data-src="<?php echo e(uploaded_asset($stock->image)); ?>"
                                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-7 col-lg-6">
                        <div class="text-left">
                            <h1 class="mb-2 fs-20 fw-600">
                                <?php echo e($detailedProduct->getTranslation('name')); ?>

                            </h1>

                            <div class="row align-items-center">
                                <div class="col-12">
                                    <?php
                                        $total = 0;
                                        $total += $detailedProduct->reviews->count();
                                    ?>
                                    <span class="rating">
                                        <?php echo e(renderStarRating($detailedProduct->rating)); ?>

                                    </span>
                                    <span class="ml-1 opacity-50">(<?php echo e($total); ?>

                                        <?php echo e(translate('reviews')); ?>)</span>
                                </div>
                                <?php if($detailedProduct->est_shipping_days): ?>
                                    <div class="col-auto ml">
                                        <small class="mr-2 opacity-50"><?php echo e(translate('Estimate Shipping Time')); ?>:
                                        </small><?php echo e($detailedProduct->est_shipping_days); ?> <?php echo e(translate('Days')); ?>

                                    </div>
                                    <!-\u6b63\u7248\u0071\u0071\u0034\u0039\u0035\u0032\u0020\u0038\u0038\u0038\u0037->
                                <?php endif; ?>
                            </div>
                            <hr>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <small class="mr-2 opacity-50"><?php echo e(translate('Sold by')); ?>: </small><br>
                                    <?php if($detailedProduct->added_by == 'seller' && get_setting('vendor_system_activation') == 1): ?>
                                        <a href="<?php echo e(route('shop.visit', $detailedProduct->user->shop->slug)); ?>"
                                            class="text-reset"><?php echo e($detailedProduct->user->shop->name); ?></a>
                                    <?php else: ?>
                                        <?php echo e(translate('Inhouse product')); ?>

                                    <?php endif; ?>
                                </div>
                                <?php if(get_setting('conversation_system') == 1): ?>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-soft-primary"
                                            onclick="show_chat_modal()"><?php echo e(translate('Message Seller')); ?></button>
                                    </div>
                                <?php endif; ?>


                                <?php if($detailedProduct->user->shop->online_ervice): ?>
                                    <a target="_blank" href="<?php echo e($detailedProduct->user->shop->online_ervice); ?>"  class="col-auto">
                                        <button class="btn btn-sm btn-soft-primary" ><?php echo e(translate('Online Service')); ?></button>
                                    </a>
                                <?php endif; ?>

                                <?php if($detailedProduct->brand != null): ?>
                                    <div class="col-auto">
                                        <a href="<?php echo e(route('products.brand', $detailedProduct->brand->slug)); ?>">
                                            <img src="<?php echo e(uploaded_asset($detailedProduct->brand->logo)); ?>"
                                                alt="<?php echo e($detailedProduct->brand->getTranslation('name')); ?>"
                                                height="30">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <?php if($detailedProduct->wholesale_product): ?>
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(translate('Min Qty')); ?></th>
                                            <th><?php echo e(translate('Max Qty')); ?></th>
                                            <th><?php echo e(translate('Unit Price')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $detailedProduct->stocks->first()->wholesalePrices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wholesalePrice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($wholesalePrice->min_qty); ?></td>
                                                <td><?php echo e($wholesalePrice->max_qty); ?></td>
                                                <td><?php echo e(single_price($wholesalePrice->price)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <?php if(home_price($detailedProduct) != home_discounted_price($detailedProduct)): ?>
                                    <div class="row no-gutters mt-3">
                                        <div class="col-sm-2">
                                            <div class="opacity-50 my-2"><?php echo e(translate('Price')); ?>:</div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="fs-20 opacity-60">
                                                <del>
                                                    <?php echo e(home_price($detailedProduct)); ?>

                                                    <?php if($detailedProduct->unit != null): ?>
                                                        <span>/<?php echo e($detailedProduct->getTranslation('unit')); ?></span>
                                                    <?php endif; ?>
                                                </del>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row no-gutters my-2">
                                        <div class="col-sm-2">
                                            <div class="opacity-50"><?php echo e(translate('Discount Price')); ?>:</div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="">
                                                <strong class="h2 fw-600 text-primary">
                                                    <?php echo e(home_discounted_price($detailedProduct)); ?>

                                                </strong>
                                                <?php if($detailedProduct->unit != null): ?>
                                                    <span
                                                        class="opacity-70">/<?php echo e($detailedProduct->getTranslation('unit')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="row no-gutters mt-3">
                                        <div class="col-sm-2">
                                            <div class="opacity-50 my-2"><?php echo e(translate('Price')); ?>:</div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="">
                                                <strong class="h2 fw-600 text-primary">
                                                    <?php echo e(home_discounted_price($detailedProduct)); ?>

                                                </strong>
                                                <?php if($detailedProduct->unit != null): ?>
                                                    <span
                                                        class="opacity-70">/<?php echo e($detailedProduct->getTranslation('unit')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(addon_is_activated('club_point') && $detailedProduct->earn_point > 0): ?>
                                <div class="row no-gutters mt-4">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2"><?php echo e(translate('Club Point')); ?>:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div
                                            class="d-inline-block rounded px-2 bg-soft-primary border-soft-primary border">
                                            <span class="strong-700"><?php echo e($detailedProduct->earn_point); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <form id="option-choice-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo e($detailedProduct->id); ?>">

                                <?php if($detailedProduct->choice_options != null): ?>
                                    <?php $__currentLoopData = json_decode($detailedProduct->choice_options); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $choice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="row no-gutters">
                                            <div class="col-sm-2">
                                                <div class="opacity-50 my-2">
                                                    <?php echo e(\App\Models\Attribute::find($choice->attribute_id)->getTranslation('name')); ?>:
                                                </div>
                                            </div>
                                            <div class="col-sm-10">
                                                <div class="aiz-radio-inline">
                                                    <?php $__currentLoopData = $choice->values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <label class="aiz-megabox pl-0 mr-2">
                                                            <input type="radio"
                                                                name="attribute_id_<?php echo e($choice->attribute_id); ?>"
                                                                value="<?php echo e($value); ?>"
                                                                <?php if($key == 0): ?> checked <?php endif; ?>>
                                                            <span
                                                                class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                                <?php echo e($value); ?>

                                                            </span>
                                                        </label>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>

                                <?php if(count(json_decode($detailedProduct->colors)) > 0): ?>
                                    <div class="row no-gutters">
                                        <div class="col-sm-2">
                                            <div class="opacity-50 my-2"><?php echo e(translate('Color')); ?>:</div>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="aiz-radio-inline">
                                                <?php $__currentLoopData = json_decode($detailedProduct->colors); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label class="aiz-megabox pl-0 mr-2" data-toggle="tooltip"
                                                        data-title="<?php echo e(\App\Models\Color::where('code', $color)->first()->name); ?>">
                                                        <input type="radio" name="color"
                                                            value="<?php echo e(\App\Models\Color::where('code', $color)->first()->name); ?>"
                                                            <?php if($key == 0): ?> checked <?php endif; ?>>
                                                        <span
                                                            class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center p-1 mb-2">
                                                            <span class="size-30px d-inline-block rounded"
                                                                style="background: <?php echo e($color); ?>;"></span>
                                                        </span>
                                                    </label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                <?php endif; ?>

                                <!-- Quantity + Add to cart -->
                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2"><?php echo e(translate('Quantity')); ?>:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="product-quantity d-flex align-items-center">
                                            <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                                style="width: 130px;">
                                                <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="minus" data-field="quantity"
                                                    disabled="">
                                                    <i class="las la-minus"></i>
                                                </button>
                                                <input type="number" name="quantity"
                                                    class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                    placeholder="1" value="<?php echo e($detailedProduct->min_qty); ?>"
                                                    min="<?php echo e($detailedProduct->min_qty); ?>" max="10"
                                                    lang="en">
                                                <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="plus" data-field="quantity">
                                                    <i class="las la-plus"></i>
                                                </button>
                                            </div>
                                            <?php
                                                $qty = 0;
                                                foreach ($detailedProduct->stocks as $key => $stock) {
                                                    $qty += $stock->qty;
                                                }
                                            ?>
                                            <div class="avialable-amount opacity-60">
                                                <?php if($detailedProduct->stock_visibility_state == 'quantity'): ?>
                                                    (<span id="available-quantity"><?php echo e($qty); ?></span>
                                                    <?php echo e(translate('available')); ?>)
                                                <?php elseif($detailedProduct->stock_visibility_state == 'text' && $qty >= 1): ?>
                                                    (<span id="available-quantity"><?php echo e(translate('In Stock')); ?></span>)
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2"><?php echo e(translate('Total Price')); ?>:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="product-price">
                                            <strong id="chosen_price" class="h4 fw-600 text-primary">

                                            </strong>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="mt-3">
                                <?php if($detailedProduct->external_link != null): ?>
                                    <a type="button" class="btn btn-primary buy-now fw-600"
                                        href="<?php echo e($detailedProduct->external_link); ?>">
                                        <i class="la la-share"></i> <?php echo e(translate($detailedProduct->external_link_btn)); ?>

                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                        onclick="addToCart()">
                                        <i class="las la-shopping-bag"></i>
                                        <span class="d-none d-md-inline-block"> <?php echo e(translate('Add to cart')); ?></span>
                                    </button>
                                    <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                        <i class="la la-shopping-cart"></i> <?php echo e(translate('Buy Now')); ?>

                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary out-of-stock fw-600 d-none" disabled>
                                    <i class="la la-cart-arrow-down"></i> <?php echo e(translate('Out of Stock')); ?>

                                </button>
                            </div>



                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    <!-- Add to wishlist button -->
                                    <button type="button" class="btn pl-0 btn-link fw-600"
                                        onclick="addToWishList(<?php echo e($detailedProduct->id); ?>)">
                                        <?php echo e(translate('Add to wishlist')); ?>

                                    </button>
                                    <!-- Add to compare button -->
                                    <button type="button" class="btn btn-link btn-icon-left fw-600"
                                        onclick="addToCompare(<?php echo e($detailedProduct->id); ?>)">
                                        <?php echo e(translate('Add to compare')); ?>

                                    </button>
                                    <?php if(Auth::check() && addon_is_activated('affiliate_system') && (\App\Models\AffiliateOption::where('type', 'product_sharing')->first()->status || \App\Models\AffiliateOption::where('type', 'category_wise_affiliate')->first()->status) && Auth::user()->affiliate_user != null && Auth::user()->affiliate_user->status): ?>
                                        <?php
                                            if (Auth::check()) {
                                                if (Auth::user()->referral_code == null) {
                                                    Auth::user()->referral_code = substr(Auth::user()->id . Str::random(10), 0, 10);
                                                    Auth::user()->save();
                                                }
                                                $referral_code = Auth::user()->referral_code;
                                                $referral_code_url = URL::to('/product') . '/' . $detailedProduct->slug . "?product_referral_code=$referral_code";
                                            }
                                        ?>
                                        <div>
                                            <button type=button id="ref-cpurl-btn" class="btn btn-sm btn-secondary"
                                                data-attrcpy="<?php echo e(translate('Copied')); ?>"
                                                onclick="CopyToClipboard(this)"
                                                data-url="<?php echo e($referral_code_url); ?>"><?php echo e(translate('Copy the Promote Link')); ?></button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <?php
                                $refund_sticker = get_setting('refund_sticker');
                            ?>
                            <?php if(addon_is_activated('refund_request')): ?>
                                <div class="row no-gutters mt-3">
                                    <div class="col-2">
                                        <div class="opacity-50 mt-2"><?php echo e(translate('Refund')); ?>:</div>
                                    </div>
                                    <div class="col-10">
                                        <a href="<?php echo e(route('returnpolicy')); ?>" target="_blank">
                                            <?php if($refund_sticker != null): ?>
                                                <img src="<?php echo e(uploaded_asset($refund_sticker)); ?>" height="36">
                                            <?php else: ?>
                                                <img src="<?php echo e(static_asset('assets/img/refund-sticker.jpg')); ?>"
                                                    height="36">
                                            <?php endif; ?>
                                        </a>
                                        <a href="<?php echo e(route('returnpolicy')); ?>" class="ml-2"
                                            target="_blank"><?php echo e(translate('View Policy')); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="row no-gutters mt-4">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2"><?php echo e(translate('Share')); ?>:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="aiz-share"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="container">
            <div class="row gutters-10">
                <div class="col-xl-3 order-1 order-xl-0">
                    <?php if($detailedProduct->added_by == 'seller' && $detailedProduct->user->shop != null): ?>
                        <div class="bg-white shadow-sm mb-3">
                            <div class="position-relative p-3 text-left">
                                <?php if($detailedProduct->user->shop->verification_status): ?>
                                    <div class="absolute-top-right p-2 bg-white z-1">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve"
                                            viewBox="0 0 287.5 442.2" width="22" height="34">
                                            <polygon style="fill:#F8B517;"
                                                points="223.4,442.2 143.8,376.7 64.1,442.2 64.1,215.3 223.4,215.3 " />
                                            <circle style="fill:#FBD303;" cx="143.8" cy="143.8"
                                                r="143.8" />
                                            <circle style="fill:#F8B517;" cx="143.8" cy="143.8"
                                                r="93.6" />
                                            <polygon style="fill:#FCFCFD;"
                                                points="143.8,55.9 163.4,116.6 227.5,116.6 175.6,154.3 195.6,215.3 143.8,177.7 91.9,215.3 111.9,154.3
                                            60,116.6 124.1,116.6 " />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="opacity-50 fs-12 border-bottom"><?php echo e(translate('Sold by')); ?></div>
                                <a href="<?php echo e(route('shop.visit', $detailedProduct->user->shop->slug)); ?>"
                                    class="text-reset d-block fw-600">
                                    <?php echo e($detailedProduct->user->shop->name); ?>

                                    <?php if($detailedProduct->user->shop->verification_status == 1): ?>
                                        <span class="ml-2"><i class="fa fa-check-circle"
                                                style="color:green"></i></span>
                                    <?php else: ?>
                                        <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                                    <?php endif; ?>
                                </a>
                                <div class="location opacity-70"><?php echo e($detailedProduct->user->shop->address); ?></div>
                                <div class="text-center border rounded p-2 mt-3">
                                    <div class="rating">
                                        <?php if($total > 0): ?>
                                            <?php echo e(renderStarRating($detailedProduct->user->shop->rating)); ?>

                                        <?php else: ?>
                                            <?php echo e(renderStarRating(0)); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="opacity-60 fs-12">(<?php echo e($total); ?>

                                        <?php echo e(translate('customer reviews')); ?>)</div>
                                </div>
                            </div>
                            <div class="row no-gutters align-items-center border-top">
                                <div class="col">
                                    <a href="<?php echo e(route('shop.visit', $detailedProduct->user->shop->slug)); ?>"
                                        class="d-block btn btn-soft-primary rounded-0"><?php echo e(translate('Visit Store')); ?></a>
                                </div>
                                <div class="col">
                                    <ul class="social list-inline mb-0">
                                        <li class="list-inline-item mr-0">
                                            <a href="<?php echo e($detailedProduct->user->shop->facebook); ?>" class="facebook"
                                                target="_blank">
                                                <i class="lab la-facebook-f opacity-60"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item mr-0">
                                            <a href="<?php echo e($detailedProduct->user->shop->google); ?>" class="google"
                                                target="_blank">
                                                <i class="lab la-google opacity-60"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item mr-0">
                                            <a href="<?php echo e($detailedProduct->user->shop->twitter); ?>" class="twitter"
                                                target="_blank">
                                                <i class="lab la-twitter opacity-60"></i>
                                            </a>
                                        </li>
                                        <li class="list-inline-item">
                                            <a href="<?php echo e($detailedProduct->user->shop->youtube); ?>" class="youtube"
                                                target="_blank">
                                                <i class="lab la-youtube opacity-60"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="bg-white rounded shadow-sm mb-3">
                        <div class="p-3 border-bottom fs-16 fw-600">
                            <?php echo e(translate('Top Selling Products')); ?>

                        </div>
                        <div class="p-3">
                            <ul class="list-group list-group-flush">
                                <?php $__currentLoopData = filter_products(\App\Models\Product::where('user_id', $detailedProduct->user_id)->orderBy('num_of_sale', 'desc'))->limit(6)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $top_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="py-3 px-0 list-group-item border-light">
                                        <div class="row gutters-10 align-items-center">
                                            <div class="col-5">
                                                <a href="<?php echo e(route('product', $top_product->slug)); ?>"
                                                    class="d-block text-reset">
                                                    <img class="img-fit lazyload h-xxl-110px h-xl-80px h-120px"
                                                        src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                        data-src="<?php echo e(uploaded_asset($top_product->thumbnail_img)); ?>"
                                                        alt="<?php echo e($top_product->getTranslation('name')); ?>"
                                                        onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                                </a>
                                            </div>
                                            <div class="col-7 text-left">
                                                <h4 class="fs-13 text-truncate-2">
                                                    <a href="<?php echo e(route('product', $top_product->slug)); ?>"
                                                        class="d-block text-reset"><?php echo e($top_product->getTranslation('name')); ?></a>
                                                </h4>
                                                <div class="rating rating-sm mt-1">
                                                    <?php echo e(renderStarRating($top_product->rating)); ?>

                                                </div>
                                                <div class="mt-2">
                                                    <span
                                                        class="fs-17 fw-600 text-primary"><?php echo e(home_discounted_base_price($top_product)); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 order-0 order-xl-1">
                    <div class="bg-white mb-3 shadow-sm rounded">
                        <div class="nav border-bottom aiz-nav-tabs">
                            <a href="#tab_default_1" data-toggle="tab"
                                class="p-3 fs-16 fw-600 text-reset active show"><?php echo e(translate('Description')); ?></a>
                            <?php if($detailedProduct->video_link != null): ?>
                                <a href="#tab_default_2" data-toggle="tab"
                                    class="p-3 fs-16 fw-600 text-reset"><?php echo e(translate('Video')); ?></a>
                            <?php endif; ?>
                            <?php if($detailedProduct->pdf != null): ?>
                                <a href="#tab_default_3" data-toggle="tab"
                                    class="p-3 fs-16 fw-600 text-reset"><?php echo e(translate('Downloads')); ?></a>
                            <?php endif; ?>
                            <a href="#tab_default_4" data-toggle="tab"
                                class="p-3 fs-16 fw-600 text-reset"><?php echo e(translate('Reviews')); ?></a>
                        </div>

                        <div class="tab-content pt-0">
                            <div class="tab-pane fade active show" id="tab_default_1">
                                <div class="p-4">
                                    <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                        <?php echo $detailedProduct->getTranslation('description'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab_default_2">
                                <div class="p-4">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <?php if($detailedProduct->video_provider == 'youtube' && isset(explode('=', $detailedProduct->video_link)[1])): ?>
                                            <iframe class="embed-responsive-item"
                                                src="https://www.youtube.com/embed/<?php echo e(get_url_params($detailedProduct->video_link, 'v')); ?>"></iframe>
                                        <?php elseif($detailedProduct->video_provider == 'dailymotion' && isset(explode('video/', $detailedProduct->video_link)[1])): ?>
                                            <iframe class="embed-responsive-item"
                                                src="https://www.dailymotion.com/embed/video/<?php echo e(explode('video/', $detailedProduct->video_link)[1]); ?>"></iframe>
                                        <?php elseif($detailedProduct->video_provider == 'vimeo' && isset(explode('vimeo.com/', $detailedProduct->video_link)[1])): ?>
                                            <iframe
                                                src="https://player.vimeo.com/video/<?php echo e(explode('vimeo.com/', $detailedProduct->video_link)[1]); ?>"
                                                width="500" height="281" frameborder="0" webkitallowfullscreen
                                                mozallowfullscreen allowfullscreen></iframe>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_default_3">
                                <div class="p-4 text-center ">
                                    <a href="<?php echo e(uploaded_asset($detailedProduct->pdf)); ?>"
                                        class="btn btn-primary"><?php echo e(translate('Download')); ?></a>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab_default_4">
                                <div class="p-4">
                                    <ul class="list-group list-group-flush">
                                        <?php $__currentLoopData = $detailedProduct->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($review->user != null): ?>
                                                <li class="media list-group-item d-flex">
                                                    <span class="avatar avatar-md mr-3">
                                                        <img class="lazyload"
                                                            src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                            onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                                            <?php if($review->user->avatar_original != null): ?> data-src="<?php echo e(uploaded_asset($review->user->avatar_original)); ?>"
                                                        <?php else: ?>
                                                            data-src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>" <?php endif; ?>>
                                                    </span>
                                                    <div class="media-body text-left">
                                                        <div class="d-flex justify-content-between">
                                                            <h3 class="fs-15 fw-600 mb-0"><?php echo e($review->user->name); ?>

                                                            </h3>
                                                            <span class="rating rating-sm">
                                                                <?php for($i = 0; $i < $review->rating; $i++): ?>
                                                                    <i class="las la-star active"></i>
                                                                <?php endfor; ?>
                                                                <?php for($i = 0; $i < 5 - $review->rating; $i++): ?>
                                                                    <i class="las la-star"></i>
                                                                <?php endfor; ?>
                                                            </span>
                                                        </div>
                                                        <div class="opacity-60 mb-2">
                                                            <?php echo e(date('d-m-Y', strtotime($review->created_at))); ?></div>
                                                        <p class="comment-text">
                                                            <?php echo e($review->comment); ?>

                                                        </p>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>

                                    <?php if(count($detailedProduct->reviews) <= 0): ?>
                                        <div class="text-center fs-18 opacity-70">
                                            <?php echo e(translate('There have been no reviews for this product yet.')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <!--\u76d7\u7248\u9632\u62a4\u0020\u0020\u0071\u0069\u0020\u0065\u0020\u0034\u0039\u0035\u0032\u0020\u0038\u0038\u0038\u0037-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded shadow-sm">
                        <div class="border-bottom p-3">
                            <h3 class="fs-16 fw-600 mb-0">
                                <span class="mr-4"><?php echo e(translate('Related products')); ?></span>
                            </h3>
                        </div>
                        <div class="p-3">
                            <div class="aiz-carousel gutters-5 half-outside-arrow" data-items="5" data-xl-items="3"
                                data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2"
                                data-arrows='true' data-infinite='true'>
                                <?php $__currentLoopData = filter_products(\App\Models\Product::where('category_id', $detailedProduct->category_id)->where('id', '!=', $detailedProduct->id))->limit(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $related_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="carousel-box">
                                        <div
                                            class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                                            <div class="">
                                                <a href="<?php echo e(route('product', $related_product->slug)); ?>"
                                                    class="d-block">
                                                    <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                                                        src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                        data-src="<?php echo e(uploaded_asset($related_product->thumbnail_img)); ?>"
                                                        alt="<?php echo e($related_product->getTranslation('name')); ?>"
                                                        onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';">
                                                </a>
                                            </div>
                                            <div class="p-md-3 p-2 text-left">
                                                <div class="fs-15">
                                                    <?php if(home_base_price($related_product) != home_discounted_base_price($related_product)): ?>
                                                        <del
                                                            class="fw-600 opacity-50 mr-1"><?php echo e(home_base_price($related_product)); ?></del>
                                                    <?php endif; ?>
                                                    <span
                                                        class="fw-700 text-primary"><?php echo e(home_discounted_base_price($related_product)); ?></span>
                                                </div>
                                                <div class="rating rating-sm mt-1">
                                                    <?php echo e(renderStarRating($related_product->rating)); ?>

                                                </div>
                                                <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                                    <a href="<?php echo e(route('product', $related_product->slug)); ?>"
                                                        class="d-block text-reset"><?php echo e($related_product->getTranslation('name')); ?></a>
                                                </h3>
                                                <?php if(addon_is_activated('club_point')): ?>
                                                    <div
                                                        class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                                        <?php echo e(translate('Club Point')); ?>:
                                                        <span
                                                            class="fw-700 float-right"><?php echo e($related_product->earn_point); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(get_setting('product_query_activation') == 1): ?>
                        <div class="bg-white rounded shadow-sm mt-3">
                            <div class="border-bottom p-3">
                                <h3 class="fs-18 fw-600 mb-0">
                                    <span><?php echo e(translate(' Product Queries ')); ?> (<?php echo e($total_query); ?>)</span>
                                </h3>
                            </div>
                            <?php if(auth()->guard()->guest()): ?>
                                <p class="fs-14 fw-400 mb-0 ml-3 mt-2"><a
                                        href="<?php echo e(route('user.login')); ?>"><?php echo e(translate('Login')); ?></a> or <a class="mr-1"
                                        href="<?php echo e(route('user.registration')); ?>"><?php echo e(translate('Register ')); ?></a><?php echo e(translate(' to submit your questions to seller')); ?>

                                </p>
                            <?php endif; ?>

                            <?php if(auth()->guard()->check()): ?>
                                <div class="query form p-3">
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger">
                                            <ul>
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('product-queries.store')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="product" value="<?php echo e($detailedProduct->id); ?>">
                                        <div class="form-group">
                                            <textarea class="form-control" rows="3" cols="40" name="question"
                                                placeholder="Write your question here..." style="resize: none;"></textarea>

                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                                <?php
                                    $own_product_queries = Auth::user()->product_queries->where('product_id',$detailedProduct->id);
                                ?>
                                <?php if($own_product_queries->count() > 0): ?>

                                    <div class="question-area my-4   mb-0 ml-3">

                                        <div class="border-bottom py-3">
                                            <h3 class="fs-18 fw-600 mb-0">
                                                <span class="mr-4"><?php echo e(translate('My Questions')); ?></span>
                                            </h3>
                                        </div>
                                        <?php $__currentLoopData = $own_product_queries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_query): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="produc-queries border-bottom">
                                                <div class="query d-flex my-4">
                                                    <span class="mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="24.994"
                                                            height="24.981" viewBox="0 0 24.994 24.981">
                                                            <g id="Group_23909" data-name="Group 23909"
                                                                transform="translate(18392.496 11044.037)">
                                                                <path id="Subtraction_90" data-name="Subtraction 90"
                                                                    d="M1830.569-117.742a.4.4,0,0,1-.158-.035.423.423,0,0,1-.252-.446c0-.84,0-1.692,0-2.516v-2.2a5.481,5.481,0,0,1-2.391-.745,5.331,5.331,0,0,1-2.749-4.711c-.034-2.365-.018-4.769,0-7.094l0-.649a5.539,5.539,0,0,1,4.694-5.513,5.842,5.842,0,0,1,.921-.065q3.865,0,7.73,0l5.035,0a5.539,5.539,0,0,1,5.591,5.57c.01,2.577.01,5.166,0,7.693a5.54,5.54,0,0,1-4.842,5.506,6.5,6.5,0,0,1-.823.046l-3.225,0c-1.454,0-2.753,0-3.97,0a.555.555,0,0,0-.435.182c-1.205,1.214-2.435,2.445-3.623,3.636l-.062.062-1.005,1.007-.037.037-.069.069A.464.464,0,0,1,1830.569-117.742Zm7.37-11.235h0l1.914,1.521.817-.754-1.621-1.273a3.517,3.517,0,0,0,1.172-1.487,5.633,5.633,0,0,0,.418-2.267v-.58a5.629,5.629,0,0,0-.448-2.323,3.443,3.443,0,0,0-1.282-1.525,3.538,3.538,0,0,0-1.93-.53,3.473,3.473,0,0,0-1.905.534,3.482,3.482,0,0,0-1.288,1.537,5.582,5.582,0,0,0-.454,2.314v.654a5.405,5.405,0,0,0,.471,2.261,3.492,3.492,0,0,0,1.287,1.5,3.492,3.492,0,0,0,1.9.527,3.911,3.911,0,0,0,.947-.112Zm-.948-.9a2.122,2.122,0,0,1-1.812-.9,4.125,4.125,0,0,1-.652-2.457v-.667a4.008,4.008,0,0,1,.671-2.4,2.118,2.118,0,0,1,1.78-.863,2.138,2.138,0,0,1,1.824.869,4.145,4.145,0,0,1,.639,2.473v.673a4.07,4.07,0,0,1-.655,2.423A2.125,2.125,0,0,1,1836.991-129.881Z"
                                                                    transform="translate(-20217 -10901.814)" fill="#e62e04"
                                                                    stroke="rgba(0,0,0,0)" stroke-miterlimit="10"
                                                                    stroke-width="1" />
                                                            </g>
                                                        </svg></span>

                                                    <div class="ml-3">
                                                        <div class="fs-14"><?php echo e(strip_tags($product_query->question)); ?></div>
                                                        <span class="text-secondary"><?php echo e($product_query->user->name); ?> </span>
                                                    </div>
                                                </div>
                                                <div class="answer d-flex my-4">
                                                    <span class="mt-1"> <svg xmlns="http://www.w3.org/2000/svg" width="24.99"
                                                            height="24.98" viewBox="0 0 24.99 24.98">
                                                            <g id="Group_23908" data-name="Group 23908"
                                                                transform="translate(17952.169 11072.5)">
                                                                <path id="Subtraction_89" data-name="Subtraction 89"
                                                                    d="M2162.9-146.2a.4.4,0,0,1-.159-.035.423.423,0,0,1-.251-.446q0-.979,0-1.958V-151.4a5.478,5.478,0,0,1-2.39-.744,5.335,5.335,0,0,1-2.75-4.712c-.034-2.355-.018-4.75,0-7.065l0-.678a5.54,5.54,0,0,1,4.7-5.513,5.639,5.639,0,0,1,.92-.064c2.527,0,5.029,0,7.437,0l5.329,0a5.538,5.538,0,0,1,5.591,5.57c.01,2.708.01,5.224,0,7.692a5.539,5.539,0,0,1-4.843,5.506,6,6,0,0,1-.822.046l-3.234,0c-1.358,0-2.691,0-3.96,0a.556.556,0,0,0-.436.182c-1.173,1.182-2.357,2.367-3.5,3.514l-1.189,1.192-.047.048-.058.059A.462.462,0,0,1,2162.9-146.2Zm5.115-12.835h3.559l.812,2.223h1.149l-3.25-8.494h-.98l-3.244,8.494h1.155l.8-2.222Zm3.226-.915h-2.888l1.441-3.974,1.447,3.972Z"
                                                                    transform="translate(-20109 -10901.815)" fill="#f7941d"
                                                                    stroke="rgba(0,0,0,0)" stroke-miterlimit="10"
                                                                    stroke-width="1" />
                                                            </g>
                                                        </svg></span>

                                                    <div class="ml-3">
                                                        <div class="fs-14">
                                                            <?php echo e(strip_tags($product_query->reply ? $product_query->reply : translate('Seller did not respond yet'))); ?>

                                                        </div>
                                                        <span class=" text-secondary">
                                                            <?php echo e($product_query->product->user->name); ?> </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>

                                <?php endif; ?>
                            <?php endif; ?>

                            <div class="pagination-area my-4 mb-0 ml-3">
                                <?php echo $__env->make('frontend.partials.product_query_pagination', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5"><?php echo e(translate('Any query about this product')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="<?php echo e(route('conversations.store')); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($detailedProduct->id); ?>">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3" name="title"
                                value="<?php echo e($detailedProduct->name); ?>" placeholder="<?php echo e(translate('Product Name')); ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required
                                placeholder="<?php echo e(translate('Your Question')); ?>"><?php echo e(route('product', $detailedProduct->slug)); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600"
                            data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                        <button type="submit" class="btn btn-primary fw-600"><?php echo e(translate('Send')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600"><?php echo e(translate('Login')); ?></h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="<?php echo e(route('cart.login.submit')); ?>"
                            method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <?php if(addon_is_activated('otp_system')): ?>
                                    <input type="text"
                                        class="form-control h-auto form-control-lg <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>"
                                        value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email Or Phone')); ?>"
                                        name="email" id="email">
                                <?php else: ?>
                                    <input type="email"
                                        class="form-control h-auto form-control-lg <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>"
                                        value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>"
                                        name="email">
                                <?php endif; ?>
                                <?php if(addon_is_activated('otp_system')): ?>
                                    <span class="opacity-60"><?php echo e(translate('Use country code before number')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg"
                                    placeholder="<?php echo e(translate('Password')); ?>">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                        <span class=opacity-60><?php echo e(translate('Remember Me')); ?></span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="<?php echo e(route('password.request')); ?>"
                                        class="text-reset opacity-60 fs-14"><?php echo e(translate('Forgot password?')); ?></a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit"
                                    class="btn btn-primary btn-block fw-600"><?php echo e(translate('Login')); ?></button>
                            </div>
                        </form>

                        <div class="text-center mb-3">
                            <p class="text-muted mb-0"><?php echo e(translate('Dont have an account?')); ?></p>
                            <a href="<?php echo e(route('user.registration')); ?>"><?php echo e(translate('Register Now')); ?></a>
                        </div>
                        <?php if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1): ?>
                            <div class="separator mb-3">
                                <span class="bg-white px-3 opacity-60"><?php echo e(translate('Or Login With')); ?></span>
                            </div>
                            <ul class="list-inline social colored text-center mb-5">
                                <?php if(get_setting('facebook_login') == 1): ?>
                                    <li class="list-inline-item">
                                        <a href="<?php echo e(route('social.login', ['provider' => 'facebook'])); ?>"
                                            class="facebook">
                                            <i class="lab la-facebook-f"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(get_setting('google_login') == 1): ?>
                                    <li class="list-inline-item">
                                        <a href="<?php echo e(route('social.login', ['provider' => 'google'])); ?>"
                                            class="google">
                                            <i class="lab la-google"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(get_setting('twitter_login') == 1): ?>
                                    <li class="list-inline-item">
                                        <a href="<?php echo e(route('social.login', ['provider' => 'twitter'])); ?>"
                                            class="twitter">
                                            <i class="lab la-twitter"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            getVariantPrice();
            <?php if($detailedProduct->source=="alibaba"): ?>
            imgReplace();
            <?php endif; ?>
        });

        function imgReplace(){
            $("#tab_default_1 img").each(function(index,element){
                var src = $(element).attr('data-src')
                if(src != undefined){
                    $(element).attr('src', src)
                }
            })
        }

        function CopyToClipboard(e) {
            var url = $(e).data('url');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            try {
                document.execCommand("copy");
                AIZ.plugins.notify('success', '<?php echo e(translate('Link copied to clipboard')); ?>');
            } catch (err) {
                AIZ.plugins.notify('danger', '<?php echo e(translate('Oops, unable to copy')); ?>');
            }
            $temp.remove();
            // if (document.selection) {
            //     var range = document.body.createTextRange();
            //     range.moveToElementText(document.getElementById(containerid));
            //     range.select().createTextRange();
            //     document.execCommand("Copy");

            // } else if (window.getSelection) {
            //     var range = document.createRange();
            //     document.getElementById(containerid).style.display = "block";
            //     range.selectNode(document.getElementById(containerid));
            //     window.getSelection().addRange(range);
            //     document.execCommand("Copy");
            //     document.getElementById(containerid).style.display = "none";

            // }
            // AIZ.plugins.notify('success', 'Copied');
        }

        function show_chat_modal() {
            <?php if(Auth::check()): ?>
                $('#chat_modal').modal('show');
            <?php else: ?>
                $('#login_modal').modal('show');
            <?php endif; ?>
        }

        // Pagination using ajax
        $(window).on('hashchange', function() {
            if (window.location.hash) {
                var page = window.location.hash.replace('#', '');
                if (page == Number.NaN || page <= 0) {
                    return false;
                } else {
                    getQuestions(page);
                }
            }
        });

        $(document).ready(function() {
            $(document).on('click', '.pagination a', function(e) {
                getQuestions($(this).attr('href').split('page=')[1]);
                e.preventDefault();
            });
        });

        function getQuestions(page) {
            $.ajax({
                url: '?page=' + page,
                dataType: 'json',
            }).done(function(data) {
                $('.pagination-area').html(data);
                location.hash = page;
            }).fail(function() {
                alert('Something went worng! Questions could not be loaded.');
            });
        }
        // Pagination end
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/product_details.blade.php ENDPATH**/ ?>