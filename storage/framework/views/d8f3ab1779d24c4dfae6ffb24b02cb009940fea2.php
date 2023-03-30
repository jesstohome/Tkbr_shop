
<?php $__env->startSection('panel_content'); ?>

    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3"><?php echo e(translate('Coupons')); ?></h1>
            </div>
        </div>
    </div>

    <div class="row gutters-10 justify-content-center">
        <div class="col-md-4 mx-auto mb-3" >
            <a href="<?php echo e(route('seller.coupon.create')); ?>">
            <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                    <i class="las la-plus la-3x text-white"></i>
                </span>
                <div class="fs-18 text-primary"><?php echo e(translate('Add New Coupon')); ?></div>
            </div>
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6"><?php echo e(translate('All Coupons')); ?></h5>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table p-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th><?php echo e(translate('Code')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Type')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Start Date')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('End Date')); ?></th>
                        <th width="10%"><?php echo e(translate('Options')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key+1); ?></td>
                            <td><?php echo e($coupon->code); ?></td>
                            <td><?php if($coupon->type == 'cart_base'): ?>
                                    <?php echo e(translate('Cart Base')); ?>

                                <?php elseif($coupon->type == 'product_base'): ?>
                                    <?php echo e(translate('Product Base')); ?>

                            <?php endif; ?></td>
                            <td><?php echo e(date('d-m-Y', $coupon->start_date)); ?></td>
                            <td><?php echo e(date('d-m-Y', $coupon->end_date)); ?></td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('seller.coupon.edit', encrypt($coupon->id) )); ?>" title="<?php echo e(translate('Edit')); ?>">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('seller.coupon.destroy', $coupon->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
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

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/coupons/index.blade.php ENDPATH**/ ?>