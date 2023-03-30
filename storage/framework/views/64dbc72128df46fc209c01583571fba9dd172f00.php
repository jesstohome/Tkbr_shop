

<?php $__env->startSection('content'); ?>
    <section class="mb-4 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-lg-left text-center">
                    <h1 class="fw-600 h4"><?php echo e(translate('All Brands')); ?></h1>
                </div>
                <div class="col-lg-6">
                    <ul class="breadcrumb justify-content-center justify-content-lg-end bg-transparent p-0">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="<?php echo e(route('home')); ?>"><?php echo e(translate('Home')); ?></a>
                        </li>
                        <li class="text-dark fw-600 breadcrumb-item">
                            <a class="text-reset" href="<?php echo e(route('brands.all')); ?>"><?php echo e(translate('All Brands')); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-4">
        <div class="container">
            <div class="rounded bg-white px-3 pt-3 shadow-sm">
                <div class="row row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-2 gutters-10">
                    <?php $__currentLoopData = \App\Models\Brand::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col text-center">
                            <a href="<?php echo e(route('products.brand', $brand->slug)); ?>"
                                class="d-block border-light hov-shadow-md mb-3 rounded border p-3">
                                <img src="<?php echo e(uploaded_asset($brand->logo)); ?>" class="lazyload h-70px mw-100 mx-auto"
                                    alt="<?php echo e($brand->getTranslation('name')); ?>">
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/all_brand.blade.php ENDPATH**/ ?>