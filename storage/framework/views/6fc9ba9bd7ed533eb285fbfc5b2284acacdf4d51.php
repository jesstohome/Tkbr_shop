<?php $__env->startSection('content'); ?>

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3"><?php echo e(translate('All Salesmans')); ?></h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="<?php echo e(route('salesmans.create')); ?>" class="btn btn-circle btn-info">
                    <span><?php echo e(translate('Add New Salesmans')); ?></span>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Salesmans')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th data-breakpoints="lg" width="10%">#</th>
                    <th><?php echo e(translate('Name')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Email')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Phone')); ?></th>
                    <th width="10%"><?php echo e(translate('Options')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $salesmans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(($key+1) + ($salesmans->currentPage() - 1)*$salesmans->perPage()); ?></td>
                        <td><?php echo e($salesman->name); ?></td>
                        <td><?php echo e($salesman->email); ?></td>
                        <td><?php echo e($salesman->phone); ?></td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('salesmans.edit', encrypt($salesman->id))); ?>" title="<?php echo e(translate('Edit')); ?>">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('salesmans.destroy', $salesman->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($salesmans->appends(request()->input())->links()); ?>

            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/salesman/salesmans/index.blade.php ENDPATH**/ ?>