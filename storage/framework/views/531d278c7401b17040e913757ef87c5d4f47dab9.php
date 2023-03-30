<div class="modal-body p-4 added-to-cart">
    <div class="text-center text-success mb-4">
        <i class="las la-check-circle la-3x"></i>
        <h3><?php echo e(translate('Item added to your cart!')); ?></h3>
    </div>
    <div class="media mb-4">
        <img src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>" data-src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>" class="mr-3 lazyload size-100px img-fit rounded" alt="Product Image">
        <div class="media-body pt-3 text-left">
            <h6 class="fw-600">
                <?php echo e($product->getTranslation('name')); ?>

            </h6>
            <div class="row mt-3">
                <div class="col-sm-2 opacity-60">
                    <div><?php echo e(translate('Price')); ?>:</div>
                </div>
                <div class="col-sm-10">
                    <div class="h6 text-primary">
                        <strong>
                            <?php echo e(single_price(($data['price'] + $data['tax']) * $data['quantity'])); ?>

                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded shadow-sm">
        <div class="border-bottom p-3">
            <h3 class="fs-16 fw-600 mb-0">
                <span class="mr-4"><?php echo e(translate('Frequently Bought Together')); ?></span>
            </h3>
        </div>
        <div class="p-3">
            <div class="aiz-carousel gutters-5 half-outside-arrow" data-items="2" data-xl-items="3" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                <?php $__currentLoopData = filter_products(\App\Models\Product::where('category_id', $product->category_id)->where('id', '!=', $product->id))->limit(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $related_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="carousel-box">
                    <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                        <div class="">
                            <a href="<?php echo e(route('product', $related_product->slug)); ?>" class="d-block">
                                <img
                                    class="img-fit lazyload mx-auto h-140px h-md-210px"
                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                    data-src="<?php echo e(uploaded_asset($related_product->thumbnail_img)); ?>"
                                    alt="<?php echo e($related_product->getTranslation('name')); ?>"
                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                >
                            </a>
                        </div>
                        <div class="p-md-3 p-2 text-left">
                            <div class="fs-15">
                                <?php if(home_base_price($related_product) != home_discounted_base_price($related_product)): ?>
                                    <del class="fw-600 opacity-50 mr-1"><?php echo e(home_base_price($related_product)); ?></del>
                                <?php endif; ?>
                                <span class="fw-700 text-primary"><?php echo e(home_discounted_base_price($related_product)); ?></span>
                            </div>
                            <div class="rating rating-sm mt-1">
                                <?php echo e(renderStarRating($related_product->rating)); ?>

                            </div>
                            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                <a href="<?php echo e(route('product', $related_product->slug)); ?>" class="d-block text-reset"><?php echo e($related_product->getTranslation('name')); ?></a>
                            </h3>
                            <?php if(addon_is_activated('club_point')): ?>
                                <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                    <?php echo e(translate('Club Point')); ?>:
                                    <span class="fw-700 float-right"><?php echo e($related_product->earn_point); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-outline-primary mb-3 mb-sm-0" data-dismiss="modal"><?php echo e(translate('Back to shopping')); ?></button>
        <a href="<?php echo e(route('cart')); ?>" class="btn btn-primary mb-3 mb-sm-0"><?php echo e(translate('Proceed to Checkout')); ?></a>
    </div>
</div>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/partials/addedToCart.blade.php ENDPATH**/ ?>