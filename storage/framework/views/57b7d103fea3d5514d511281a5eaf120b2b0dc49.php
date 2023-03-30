

<?php $__env->startSection('panel_content'); ?>
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Transaction Password')); ?></h1>
            <?php if($user->tpwd != ""): ?> 
            <?php echo e(translate('If you forget the transaction password, please contact customer service to retrieve the transaction password')); ?>

            <?php endif; ?>
        </div>
      </div>
    </div>
    <form action="<?php echo e(route('seller.transaction.update')); ?>" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="POST">
        <?php echo csrf_field(); ?>
        
        <?php if($user->tpwd != ""): ?> 
        <input name="type" type="hidden"  value="2">
        <?php else: ?>
        <input name="type" type="hidden"  value="1">
        <?php endif; ?>

        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Transaction Password')); ?></h5>
            </div>
            <div class="card-body">
<?php if($user->tpwd != ""): ?> 
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="spwd"><?php echo e(translate('original password')); ?></label>
                    <div class="col-md-10">
                        <input type="password" name="spwd" id="spwd" class="form-control"  placeholder="<?php echo e(translate('original password')); ?>" required>

                    </div>
                </div>
  <?php endif; ?>               
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="password"><?php echo e(translate('Transaction Password')); ?></label>
                    <div class="col-md-10">
                        <input type="password" name="password" id="password" class="form-control"  placeholder="<?php echo e(translate('Transaction Password')); ?>" required>

                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="confirm_password"><?php echo e(translate('Confirm Password')); ?></label>
                    <div class="col-md-10">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="<?php echo e(translate('Confirm Password')); ?>" >
  
                    </div>
                </div>
         

         
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
        </div>
            </div>
            
        </div>
</form>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/transaction/index.blade.php ENDPATH**/ ?>