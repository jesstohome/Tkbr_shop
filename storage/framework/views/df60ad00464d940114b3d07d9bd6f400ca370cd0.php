<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6"><?php echo e(translate('Offline Seller Package Payment Requests')); ?></h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Name')); ?></th>
                    <th><?php echo e(translate('Package')); ?></th>
                    <th><?php echo e(translate('Method')); ?></th>
                    <th><?php echo e(translate('TXN ID')); ?></th>
                    <th><?php echo e(translate('Reciept')); ?></th>
                    <th><?php echo e(translate('Approval')); ?></th>
                    <th><?php echo e(translate('Date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $package_payment_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $package_payment_request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($package_payment_request->user != null): ?>
                        <tr>
                            <td><?php echo e(($key+1)); ?></td>
                            <td><?php echo e($package_payment_request->user->name); ?></td>
                            <td><?php echo e($package_payment_request->seller_spread_package->name); ?></td>
                            <td><?php echo e($package_payment_request->payment_method); ?></td>
                            <td><?php echo e($package_payment_request->payment_details); ?></td>
                            <td>
                                <?php if($package_payment_request->reciept != null): ?>
                                    <a href="<?php echo e(uploaded_asset($package_payment_request->reciept)); ?>" target="_blank"><?php echo e(translate('Open Reciept')); ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <?php if($package_payment_request->approval == 1): ?>
                                        <input type="checkbox" checked disabled>
                                    <?php else: ?>
                                        <input onchange="offline_payment_approval(this)" id="payment_approval" value="<?php echo e($package_payment_request->id); ?>" type="checkbox">
                                    <?php endif; ?>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td><?php echo e($package_payment_request->created_at); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($package_payment_requests->links()); ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function offline_payment_approval(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('offline_seller_spread_package_payment.approved')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    $( "#payment_approval" ).prop( "disabled", true );
                    AIZ.plugins.notify('success', '<?php echo e(translate('Offline Seller Package Payment approved successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/manual_payment_methods/seller_spread_package_payment_request.blade.php ENDPATH**/ ?>