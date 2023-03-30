

<?php $__env->startSection('content'); ?>

<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4"><?php echo e(translate('Compare')); ?></h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="<?php echo e(route('home')); ?>"><?php echo e(translate('Home')); ?></a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="<?php echo e(route('compare.reset')); ?>">"<?php echo e(translate('Compare')); ?>"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container text-left">
        <div class="bg-white shadow-sm rounded">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="fs-15 fw-600"><?php echo e(translate('Comparison')); ?></div>
                <a href="<?php echo e(route('compare.reset')); ?>" style="text-decoration: none;" class="btn btn-soft-primary btn-sm fw-600"><?php echo e(translate('Reset Compare List')); ?></a>
            </div>
            <?php if(Session::has('compare')): ?>
                <?php if(count(Session::get('compare')) > 0): ?>
                    <div class="p-3">
                        <table class="table table-responsive table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:16%" class="font-weight-bold">
                                        <?php echo e(translate('Name')); ?>

                                    </th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th scope="col" style="width:28%" class="font-weight-bold">
                                            <a class="text-reset fs-15" href="<?php echo e(route('product', \App\Models\Product::find($item)->slug)); ?>"><?php echo e(\App\Models\Product::find($item)->getTranslation('name')); ?></a>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row"><?php echo e(translate('Image')); ?></th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td>
                                            <img loading="lazy" src="<?php echo e(uploaded_asset(\App\Models\Product::find($item)->thumbnail_img)); ?>" alt="<?php echo e(translate('Product Image')); ?>" class="img-fluid py-4">
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <tr>
                                    <th scope="row"><?php echo e(translate('Price')); ?></th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $product = \App\Models\Product::find($item);
                                        ?>
                                        <td>
                                            <?php if(home_base_price($product) != home_discounted_base_price($product)): ?>
                                                <del class="fw-600 opacity-50 mr-1"><?php echo e(home_base_price($product)); ?></del>
                                            <?php endif; ?>
                                            <span class="fw-700 text-primary"><?php echo e(home_discounted_base_price($product)); ?></span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <tr>
                                    <th scope="row"><?php echo e(translate('Brand')); ?></th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td>
                                            <?php if(\App\Models\Product::find($item)->brand != null): ?>
                                                <?php echo e(\App\Models\Product::find($item)->brand->getTranslation('name')); ?>

                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <tr>
                                    <th scope="row"><?php echo e(translate('Category')); ?></th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td>
                                            <?php if(\App\Models\Product::find($item)->category != null): ?>
                                                <?php echo e(\App\Models\Product::find($item)->category->getTranslation('name')); ?>

                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <?php $__currentLoopData = Session::get('compare'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center py-4">
                                            <button type="button" class="btn btn-primary fw-600" onclick="showAddToCartModal(<?php echo e($item); ?>)">
                                                <?php echo e(translate('Add to cart')); ?>

                                            </button>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center p-4">
                    <p class="fs-17"><?php echo e(translate('Your comparison list is empty')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/view_compare.blade.php ENDPATH**/ ?>