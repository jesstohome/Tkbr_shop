

<?php $__env->startSection('panel_content'); ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Purchase History')); ?></h5>
        </div>
        <?php if(count($orders) > 0): ?>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th><?php echo e(translate('Code')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Date')); ?></th>
                            <th><?php echo e(translate('Amount')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Delivery Status')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Payment Status')); ?></th>
                            <th class="text-right"><?php echo e(translate('Options')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($order->orderDetails) > 0): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('purchase_history.details', encrypt($order->id))); ?>"><?php echo e($order->code); ?></a>
                                    </td>
                                    <td><?php echo e(date('d-m-Y', $order->date)); ?></td>
                                    <td>
                                        <?php echo e(single_price($order->grand_total)); ?>

                                    </td>
                                    <td>
                                        <?php echo e(translate(ucfirst(str_replace('_', ' ', $order->delivery_status)))); ?>

                                        <?php if($order->delivery_viewed == 0): ?>
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order->payment_status == 'paid'): ?>
                                            <span class="badge badge-inline badge-success"><?php echo e(translate('Paid')); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpaid')); ?></span>
                                        <?php endif; ?>
                                        <?php if($order->payment_status_viewed == 0): ?>
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <?php if($order->delivery_status == 'pending' && $order->payment_status == 'unpaid'): ?>
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('purchase_history.destroy', $order->id)); ?>" title="<?php echo e(translate('Cancel')); ?>">
                                               <i class="las la-trash"></i>
                                           </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('purchase_history.details', encrypt($order->id))); ?>" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="<?php echo e(translate('Order Details')); ?>">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="<?php echo e(route('invoice.download', $order->id)); ?>" title="<?php echo e(translate('Download Invoice')); ?>">
                                            <i class="las la-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    <?php echo e($orders->links()); ?>

              	</div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/user/purchase_history.blade.php ENDPATH**/ ?>