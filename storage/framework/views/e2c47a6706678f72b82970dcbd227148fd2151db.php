

<?php $__env->startSection('panel_content'); ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Payment History')); ?></h5>
        </div>
        <?php if(count($payments) > 0): ?>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(translate('Date')); ?></th>
                            <th><?php echo e(translate('Amount')); ?></th>
                            <th><?php echo e(translate('Payment Method')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e($key+1); ?>

                                </td>
                                <td><?php echo e(date('d-m-Y', strtotime($payment->created_at))); ?></td>
                                <td>
                                    <?php echo e(single_price($payment->amount)); ?>

                                </td>
                                <td>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?> <?php if($payment->txn_code != null): ?> (<?php echo e(translate('TRX ID')); ?> : <?php echo e($payment->txn_code); ?>) <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="aiz-pagination">
                	<?php echo e($payments->links()); ?>

              	</div>
            </div>
        <?php endif; ?>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/payment_history.blade.php ENDPATH**/ ?>