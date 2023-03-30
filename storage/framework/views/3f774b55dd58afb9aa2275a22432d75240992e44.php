

<?php $__env->startSection('content'); ?>

    <?php if($flash_deal->status == 1 && strtotime(date('Y-m-d H:i:s')) <= $flash_deal->end_date): ?> 
    <div style="background-color:<?php echo e($flash_deal->background_color); ?>">
        <section class="text-center mb-5">
            <img
                src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>"
                data-src="<?php echo e(uploaded_asset($flash_deal->banner)); ?>"
                alt="<?php echo e($flash_deal->title); ?>"
                class="img-fit w-100 lazyload"
            >
        </section>
        <section class="mb-4">
            <div class="container">
                <div class="text-center my-4 text-<?php echo e($flash_deal->text_color); ?>">
                    <h1 class="h2 fw-600"><?php echo e($flash_deal->title); ?></h1>
                    <div class="aiz-count-down aiz-count-down-lg ml-3 align-items-center justify-content-center" data-date="<?php echo e(date('Y/m/d H:i:s', $flash_deal->end_date)); ?>"></div>
                </div>
                <div class="row gutters-5 row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
                    <?php $__currentLoopData = $flash_deal->flash_deal_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $flash_deal_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $product = \App\Models\Product::find($flash_deal_product->product_id);
                        ?>
                        <?php if(isset($product) && $product->published != 0): ?>
                            <div class="col mb-2">
                                <?php echo $__env->make('frontend.partials.product_box_1',['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>
    </div>
    <?php else: ?>
        <div style="background-color:<?php echo e($flash_deal->background_color); ?>">
            <section class="text-center">
                <img src="<?php echo e(uploaded_asset($flash_deal->banner)); ?>" alt="<?php echo e($flash_deal->title); ?>" class="img-fit w-100">
            </section>
            <section class="pb-4">
                <div class="container">
                    <div class="text-center text-<?php echo e($flash_deal->text_color); ?>">
                        <h1 class="h3 my-4"><?php echo e($flash_deal->title); ?></h1>
                        <p class="h4"><?php echo e(translate('This offer has been expired.')); ?></p>
                    </div>
                </div>
            </section>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/flash_deal_details.blade.php ENDPATH**/ ?>