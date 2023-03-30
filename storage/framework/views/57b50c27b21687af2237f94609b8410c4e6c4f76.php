<?php $__env->startSection('content'); ?>

<div class="card">

        <form action="" class="card-header">
            <div class="col">
                <h5 class="mb-0 h6"><?php echo e(translate('Offline Wallet Recharge Requests')); ?></h5>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="name" name="name" <?php if(isset($name)): ?> value="<?php echo e($name); ?>" <?php endif; ?> placeholder="<?php echo e(translate('name')); ?>" onkeyup="filterProducts()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="operator" name="operator" <?php if(isset($operator)): ?> value="<?php echo e($operator); ?>" <?php endif; ?> placeholder="<?php echo e(translate('operator')); ?>" onkeyup="filterProducts()">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($date); ?>" name="date" placeholder="<?php echo e(translate('Filter by date')); ?>" data-format="Y-MM-DD" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-styled"><?php echo e(translate('Search')); ?></button>
        </form>


    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Name')); ?></th>
                    <th><?php echo e(translate('Operator')); ?></th>
                    <th><?php echo e(translate('Amount')); ?></th>
                    <th><?php echo e(translate('Method')); ?></th>
                    <th><?php echo e(translate('TXN ID')); ?></th>
                    <th><?php echo e(translate('Photo')); ?></th>
                    <th><?php echo e(translate('Approval')); ?></th>
                    <th><?php echo e(translate('Type')); ?></th>
                    <th><?php echo e(translate('Date')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $wallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $wallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($wallet->user != null): ?>
                        <tr>
                            <td><?php echo e(($key+1)); ?></td>
                            <td><?php echo e($wallet->user->name); ?></td>
                            <td><?php echo e($wallet->operator->user_type=='admin'?'admin':$wallet->operator->name); ?></td>
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
                            <td>
                                <?php if( $wallet->type == 1 ): ?>
                                <?php echo e(translate('Balance Recharge')); ?>

                                <?php else: ?>
                                <?php echo e(translate('Guarantee Recharge')); ?>

                            <?php endif; ?>
                            </td>
                            <td><?php echo e($wallet->created_at); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($wallets->appends(request()->input())->links()); ?>

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
            $.post('<?php echo e(route('offline_recharge_request.approved')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/manual_payment_methods/wallet_request.blade.php ENDPATH**/ ?>