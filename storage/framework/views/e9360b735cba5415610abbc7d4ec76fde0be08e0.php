<?php
    $best_selers = Cache::remember('best_selers', 86400, function () {
        return \App\Models\Shop::where('verification_status', 1)->orderBy('num_of_sale', 'desc')->take(20)->get();
    });   
?>

<?php if(get_setting('vendor_system_activation') == 1): ?>
    <section class="mb-4">
        <div class="container">
            <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
                <div class="d-flex mb-3 align-items-baseline border-bottom">
                    <h3 class="h5 fw-700 mb-0">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block"><?php echo e(translate('Best Sellers')); ?></span>
                    </h3>
                    <a href="<?php echo e(route('sellers')); ?>" class="ml-auto mr-0 btn btn-primary btn-sm shadow-md"><?php echo e(translate('View All Sellers')); ?></a>
                </div>
                <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="3" data-lg-items="3"  data-md-items="2" data-sm-items="2" data-xs-items="1" data-rows="2">
                    <?php $__currentLoopData = $best_selers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($seller->user != null): ?>
                            <div class="carousel-box">
                                <div class="row no-gutters box-3 align-items-center border border-light rounded hov-shadow-md my-2 has-transition">
                                    <div class="col-4">
                                        <a href="<?php echo e(route('shop.visit', $seller->slug)); ?>" class="d-block p-3">
                                            <img
                                                src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                                data-src="<?php if($seller->logo !== null): ?> <?php echo e(uploaded_asset($seller->logo)); ?> <?php else: ?> <?php echo e(static_asset('assets/img/placeholder.jpg')); ?> <?php endif; ?>"
                                                alt="<?php echo e($seller->name); ?>"
                                                class="img-fluid lazyload"
                                            >
                                        </a>
                                    </div>
                                    <div class="col-8 border-left border-light">
                                        <div class="p-3 text-left">
                                            <h2 class="h6 fw-600 text-truncate">
                                                <a href="<?php echo e(route('shop.visit', $seller->slug)); ?>" class="text-reset"><?php echo e($seller->name); ?></a>
                                            </h2>
                                            <div class="rating rating-sm mb-2">
                                                <?php echo e(renderStarRating($seller->rating)); ?>

                                            </div>
                                            <a href="<?php echo e(route('shop.visit', $seller->slug)); ?>" class="btn btn-soft-primary btn-sm">
                                                <?php echo e(translate('Visit Store')); ?> <i class="las la-angle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/partials/best_sellers_section.blade.php ENDPATH**/ ?>