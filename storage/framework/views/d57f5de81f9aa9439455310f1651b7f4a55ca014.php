<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6"><?php echo e(translate('Edit Seller Information')); ?></h5>
</div>

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Seller Information')); ?></h5>
        </div>

        <div class="card-body">
          <form action="<?php echo e(route('sellers.update', $shop->id)); ?>" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                <?php echo csrf_field(); ?>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name"><?php echo e(translate('Name')); ?></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="<?php echo e(translate('Name')); ?>" id="name" name="name" class="form-control" value="<?php echo e($shop->user->name); ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="email"><?php echo e(translate('Email Address')); ?></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="<?php echo e(translate('Email Address')); ?>" id="email" name="email" class="form-control" value="<?php echo e($shop->user->email); ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="password"><?php echo e(translate('Password')); ?></label>
                    <div class="col-sm-9">
                        <input type="password" placeholder="<?php echo e(translate('Password')); ?>" id="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="tpwd"><?php echo e(translate('Transaction Password')); ?></label>
                    <div class="col-sm-9">
                        <input type="password" placeholder="<?php echo e(translate('Transaction Password')); ?>" id="tpwd" name="tpwd" class="form-control" />
                    </div>
                </div>
              <div class="form-group row" style="display:none;">
                  <label class="col-sm-3 col-from-label" for="password"><?php echo e(translate('Views')); ?></label>
                  <div class="col-sm-9">
                      <input type="number" placeholder="<?php echo e(translate('Views')); ?>" value="<?php echo e($shop->views); ?>" id="views" name="views" class="form-control">
                  </div>
              </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary"><?php echo e(translate('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sellers/edit.blade.php ENDPATH**/ ?>