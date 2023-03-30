

<?php $__env->startSection('meta_title'); ?><?php echo e($shop->meta_title); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('meta_description'); ?><?php echo e($shop->meta_description); ?><?php $__env->stopSection(); ?>

<?php $__env->startSection('meta'); ?>
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?php echo e($shop->meta_title); ?>">
    <meta itemprop="description" content="<?php echo e($shop->meta_description); ?>">
    <meta itemprop="image" content="<?php echo e(uploaded_asset($shop->logo)); ?>">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="<?php echo e($shop->meta_title); ?>">
    <meta name="twitter:description" content="<?php echo e($shop->meta_description); ?>">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="<?php echo e(uploaded_asset($shop->meta_img)); ?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?php echo e($shop->meta_title); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo e(route('shop.visit', $shop->slug)); ?>" />
    <meta property="og:image" content="<?php echo e(uploaded_asset($shop->logo)); ?>" />
    <meta property="og:description" content="<?php echo e($shop->meta_description); ?>" />
    <meta property="og:site_name" content="<?php echo e($shop->name); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="pt-5 mb-4 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="d-flex justify-content-center">
                        <img
                            height="70"
                            class="lazyload"
                            src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                            data-src="<?php if($shop->logo !== null): ?> <?php echo e(uploaded_asset($shop->logo)); ?> <?php else: ?> <?php echo e(static_asset('assets/img/placeholder.jpg')); ?> <?php endif; ?>"
                            alt="<?php echo e($shop->name); ?>"
                        >
                        <div class="pl-4 text-left">
                            <h1 class="fw-600 h4 mb-0"><?php echo e($shop->name); ?>

                                <?php if($shop->verification_status == 1): ?>
                                    <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                                <?php else: ?>
                                    <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                                <?php endif; ?>
                            </h1>
                            <div class="rating rating-sm mb-1">
                                <?php echo e(renderStarRating($shop->rating)); ?>

                            </div>
                            <div class="location opacity-60"><?php echo e($shop->address); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-bottom mt-5"></div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-0">
                    <ul class="list-inline mb-0 text-center text-lg-left">
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 <?php if(!isset($type)): ?> border-bottom border-primary border-width-2 <?php endif; ?>" href="<?php echo e(route('shop.visit', $shop->slug)); ?>"><?php echo e(translate('Store Home')); ?></a>
                        </li>
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 <?php if(isset($type) && $type == 'top-selling'): ?> border-bottom border-primary border-width-2 <?php endif; ?>" href="<?php echo e(route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'top-selling'])); ?>"><?php echo e(translate('Top Selling')); ?></a>
                        </li>
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 <?php if(isset($type) && $type == 'all-products'): ?> border-bottom border-primary border-width-2 <?php endif; ?>" href="<?php echo e(route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'all-products'])); ?>"><?php echo e(translate('All Products')); ?></a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-0">
                    <ul class="text-center text-lg-right mt-4 mt-lg-0 social colored list-inline mb-0">
                        <?php if($shop->facebook != null): ?>
                            <li class="list-inline-item">
                                <a href="<?php echo e($shop->facebook); ?>" class="facebook" target="_blank">
                                    <i class="lab la-facebook-f"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($shop->instagram != null): ?>
                            <li class="list-inline-item">
                                <a href="<?php echo e($shop->instagram); ?>" class="instagram" target="_blank">
                                    <i class="lab la-instagram"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($shop->twitter != null): ?>
                            <li class="list-inline-item">
                                <a href="<?php echo e($shop->twitter); ?>" class="twitter" target="_blank">
                                    <i class="lab la-twitter"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($shop->google != null): ?>
                            <li class="list-inline-item">
                                <a href="<?php echo e($shop->google); ?>" class="google-plus" target="_blank">
                                    <i class="lab la-google"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($shop->youtube != null): ?>
                            <li class="list-inline-item">
                                <a href="<?php echo e($shop->youtube); ?>" class="youtube" target="_blank">
                                    <i class="lab la-youtube"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php if(!isset($type)): ?>
        <section class="mb-5">
            <div class="container">
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true" data-autoplay="true">
                    <?php if($shop->sliders != null): ?>
                        <?php $__currentLoopData = explode(',',$shop->sliders); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="carousel-box">
                                <img class="d-block w-100 lazyload rounded h-200px h-lg-380px img-fit" src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>" data-src="<?php echo e(uploaded_asset($slide)); ?>" alt="<?php echo e($key); ?> offer">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section class="mb-4">
            <div class="container">
                <div class="text-center mb-4">
                    <h3 class="h3 fw-600 border-bottom">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block"><?php echo e(translate('Featured Products')); ?></span>
                    </h3>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="aiz-carousel gutters-10" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-autoplay='true' data-infinute="true" data-dots="true">
                            <?php $__currentLoopData = $shop->user->products->where('published', 1)->where('approved', 1)->where('seller_featured', 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carousel-box">
                                    <?php echo $__env->make('frontend.partials.product_box_1',['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="mb-4">
        <div class="container">
            <div class="mb-4">
                <h3 class="h3 fw-600 border-bottom">
                    <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">
                        <?php if(!isset($type)): ?>
                            <?php echo e(translate('New Arrival Products')); ?>

                        <?php elseif($type == 'top-selling'): ?>
                            <?php echo e(translate('Top Selling')); ?>

                        <?php elseif($type == 'all-products'): ?>
                            <?php echo e(translate('All Products')); ?>

                        <?php endif; ?>
                    </span>
                </h3>
            </div>
            <div class="row gutters-5 row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
                <?php
                    if (!isset($type)){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->orderBy('created_at', 'desc')->paginate(24);
                    }
                    elseif ($type == 'top-selling'){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->orderBy('num_of_sale', 'desc')->paginate(24);
                    }
                    elseif ($type == 'all-products'){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->paginate(24);
                    }
                ?>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col mb-3">
                        <?php echo $__env->make('frontend.partials.product_box_1',['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="aiz-pagination aiz-pagination-center mb-4">
                <?php echo e($products->links()); ?>

            </div>
        </div>
    </section>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/seller_shop.blade.php ENDPATH**/ ?>