

<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar mt-2 mb-3">
	<div class="text-md-right">
		<a href="<?php echo e(route('manual_payment_methods.create')); ?>" class="btn btn-circle btn-info">
			<span><?php echo e(translate('Add New Payment Method')); ?></span>
		</a>
  </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6"><?php echo e(translate('Manual Payment Method')); ?></h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Heading')); ?></th>
                    <th><?php echo e(translate('Logo')); ?></th>
                    <th width="10%"><?php echo e(translate('Options')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $manual_payment_methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $manual_payment_method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(($key+1)); ?></td>
                        <td><?php echo e($manual_payment_method->heading); ?></td>
                        <td><img class="w-50px" src="<?php echo e(uploaded_asset($manual_payment_method->photo)); ?>" alt="Logo"></td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('manual_payment_methods.edit', encrypt($manual_payment_method->id))); ?>" title="<?php echo e(translate('Edit')); ?>">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('manual_payment_methods.destroy', $manual_payment_method->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/manual_payment_methods/index.blade.php ENDPATH**/ ?>