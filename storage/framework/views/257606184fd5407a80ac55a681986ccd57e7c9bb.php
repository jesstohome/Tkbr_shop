

<?php $__env->startSection('content'); ?>
<section class="pt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4"><?php echo e(translate('All Sellers')); ?></h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="<?php echo e(route('home')); ?>"><?php echo e(translate('Home')); ?></a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="<?php echo e(route('sellers')); ?>">"<?php echo e(translate('All Sellers')); ?>"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="mb-2">
    <div class="container">
            <div class="row gutters-10 row-cols-xxl-3 row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-1">
                <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($shop->user !=null): ?>
                        <div class="col">
                            <div class="row no-gutters bg-white align-items-center border border-light rounded hov-shadow-md mb-3 has-transition">
                                <div class="col-4">
                                    <a href="<?php echo e(route('shop.visit', $shop->slug)); ?>" class="d-block p-3" tabindex="0">
                                        <img
                                            src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>"
                                            data-src="<?php echo e(uploaded_asset($shop->logo)); ?>"
                                            alt="<?php echo e($shop->name); ?>"
                                            class="img-fluid lazyload"
                                            onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>';"
                                        >
                                    </a>
                                </div>
                                <div class="col-8 border-left border-light">
                                    <div class="p-3 text-left">
                                        <h2 class="h6 fw-600 text-truncate">
                                            <a href="<?php echo e(route('shop.visit', $shop->slug)); ?>" class="text-reset" tabindex="0"><?php echo e($shop->name); ?></a>
                                        </h2>
                                        <div class="rating rating-sm mb-2">
                                            <?php echo e(renderStarRating($shop->rating)); ?>

                                        </div>
                                        <a href="<?php echo e(route('shop.visit', $shop->slug)); ?>" class="btn btn-soft-primary btn-sm" tabindex="0">
                                            <?php echo e(translate('Visit Store')); ?>

                                            <i class="las la-angle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="aiz-pagination aiz-pagination-center mt-4">
                <?php echo e($shops->links()); ?>

            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/shop_listing.blade.php ENDPATH**/ ?>