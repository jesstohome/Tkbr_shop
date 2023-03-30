<?php $__env->startSection('panel_content'); ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6"><?php echo e(translate('Offline Wallet Recharge Requests')); ?></h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Name')); ?></th>
                    <th><?php echo e(translate('Amount')); ?></th>
                    <th><?php echo e(translate('Method')); ?></th>
                    <th><?php echo e(translate('TXN ID')); ?></th>
                    <th><?php echo e(translate('Photo')); ?></th>
                    <th><?php echo e(translate('Approval')); ?></th>
                    <th><?php echo e(translate('Date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($wallet->user != null): ?>
                        <tr>
                            <td><?php echo e(($key+1)); ?></td>
                            <td><?php echo e($wallet->user->name); ?></td>
                            <td><?php echo e($wallet->amount); ?></td>
                            <td><?php echo e($wallet->payment_method); ?></td>
                            <td><?php echo e($wallet->payment_details); ?></td>
                            <td>
                                <?php if($wallet->reciept != null): ?>
                                    <a href="<?php echo e(uploaded_asset($wallet->reciept)); ?>" target="_blank"><?php echo e(translate('Open Reciept')); ?></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="<?php echo e($wallet->id); ?>" type="checkbox" <?php if($wallet->approval == 1): ?> checked <?php endif; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td><?php echo e($wallet->created_at); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($wallets->links()); ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('salesman.offline_recharge_request.approved')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Money has been added successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('salesman.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/manual_payment_methods/wallet_request_salesman.blade.php ENDPATH**/ ?>