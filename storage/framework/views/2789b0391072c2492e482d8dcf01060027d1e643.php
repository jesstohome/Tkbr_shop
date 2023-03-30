<?php $__env->startSection('content'); ?>
<div class="aiz-titlebar mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3"><?php echo e(translate('All Seller Packages')); ?></h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="<?php echo e(route('seller_spread_packages.create')); ?>" class="btn btn-circle btn-info">
				<span><?php echo e(translate('Add New Package')); ?></span>
			</a>
		</div>
	</div>
</div>


<div class="row">
    <?php $__currentLoopData = $seller_spread_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller_spread_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card">
                <div class="card-body text-center">
					<img alt="<?php echo e(translate('Package Logo')); ?>" src="<?php echo e(uploaded_asset($seller_spread_package->logo)); ?>" class="mw-100 mx-auto mb-4" height="150px">
					<p class="mb-3 h6 fw-600"><?php echo e($seller_spread_package->getTranslation('name')); ?></p>
                    <p class="h4"><?php echo e(single_price($seller_spread_package->amount)); ?></p>
                    <p class="fs-15"><?php echo e(translate('Product Spread Limit')); ?>:
                        <b class="text-bold"><?php echo e($seller_spread_package->product_upload_limit); ?></b>
                    </p>
					<p class="fs-15"><?php echo e(translate('Package Duration')); ?>:
                        <b class="text-bold"><?php echo e($seller_spread_package->duration); ?> <?php echo e(translate('days')); ?></b>
                    </p>
                    <p class="fs-15">
                        <b class="text-bold"><?php echo e($seller_spread_package->getTranslation('max_profit')); ?></b>
                    </p>
                    <div class="mar-top">
						<a href="<?php echo e(route('seller_spread_packages.edit', ['id'=>$seller_spread_package->id, 'lang'=>env('DEFAULT_LANGUAGE')] )); ?>" class="btn btn-sm btn-info"><?php echo e(translate('Edit')); ?></a>
                        <a href="#" data-href="<?php echo e(route('seller_spread_packages.destroy', $seller_spread_package->id)); ?>" class="btn btn-sm btn-danger confirm-delete"><?php echo e(translate('Delete')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller_spread_packages/index.blade.php ENDPATH**/ ?>