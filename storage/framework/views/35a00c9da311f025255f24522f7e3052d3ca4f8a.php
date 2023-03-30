

<?php $__env->startSection('panel_content'); ?>
    <div class="card">
        <form class="" action="" id="sort_commission_history" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6"><?php echo e(translate('Commission History')); ?></h5>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"<?php if(isset($date_range)): ?> value="<?php echo e($date_range); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Daterange')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary"><?php echo e(translate('Filter')); ?></button>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="lg"><?php echo e(translate('Order Code')); ?></th>
                        <th><?php echo e(translate('Admin Commission')); ?></th>
                        <th><?php echo e(translate('Earning')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Created At')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $commission_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(($key+1)); ?></td>
                        <td>
                            <?php if(isset($history->order)): ?>
                                <?php echo e($history->order->code); ?>

                            <?php else: ?>
                                <span class="badge badge-inline badge-danger">
                                    <?php echo e(translate('Order Deleted')); ?>

                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($history->admin_commission); ?></td>
                        <td><?php echo e($history->seller_earning); ?></td>
                        <td><?php echo e($history->created_at); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">
                <?php echo e($commission_history->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function sort_commission_history(el){
        $('#sort_commission_history').submit();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/commission_history/index.blade.php ENDPATH**/ ?>