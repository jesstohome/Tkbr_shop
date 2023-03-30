

<?php $__env->startSection('content'); ?>

<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4"><?php echo e(translate('Flash Deals')); ?></h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="<?php echo e(route('home')); ?>">
                            <?php echo e(translate('Home')); ?>

                        </a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="<?php echo e(route('flash-deals')); ?>">
                            "<?php echo e(translate('Flash Deals')); ?>"
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        <div class="row row-cols-1 row-cols-lg-2 gutters-10">
            <?php $__currentLoopData = $all_flash_deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col">
                <div class="bg-white rounded shadow-sm mb-3">
                    <a href="<?php echo e(route('flash-deal-details', $single->slug)); ?>" class="d-block text-reset">
                        <img
                            src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>"
                            data-src="<?php echo e(uploaded_asset($single->banner)); ?>"
                            alt="<?php echo e($single->title); ?>"
                            class="img-fluid lazyload rounded w-100">
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/flash_deal/all_flash_deal_list.blade.php ENDPATH**/ ?>