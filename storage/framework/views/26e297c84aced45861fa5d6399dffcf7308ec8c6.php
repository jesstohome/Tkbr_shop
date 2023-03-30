<?php $__env->startSection('content'); ?>
<div class="aiz-titlebar mt-2 mb-3">
    <h5 class="mb-0 h6"><?php echo e(translate('Update Package Information')); ?></h5>
</div>

<div class="col-lg-10 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill border-light">
                <?php $__currentLoopData = \App\Models\Language::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item">
                        <a class="nav-link text-reset <?php if($language->code == $lang): ?> active <?php else: ?> bg-soft-dark border-light border-left-0 <?php endif; ?> py-3" href="<?php echo e(route('seller_packages.edit', ['id'=>$seller_package->id, 'lang'=> $language->code] )); ?>">
                            <img src="<?php echo e(static_asset('assets/img/flags/'.$language->code.'.png')); ?>" height="11" class="mr-1">
                            <span><?php echo e($language->name); ?></span>
                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <form class="p-4" action="<?php echo e(route('seller_packages.update', $seller_package->id)); ?>" method="POST">
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="lang" value="<?php echo e($lang); ?>">
            	<?php echo csrf_field(); ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="name"><?php echo e(translate('Package Name')); ?></label>
                    <div class="col-sm-10">
                        <input type="text" placeholder="<?php echo e(translate('Name')); ?>" value="<?php echo e($seller_package->getTranslation('name', $lang)); ?>" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="amount"><?php echo e(translate('Amount')); ?></label>
                    <div class="col-sm-10">
                        <input type="number" min="0" step="0.01" placeholder="<?php echo e(translate('Amount')); ?>" value="<?php echo e($seller_package->amount); ?>" id="amount" name="amount" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="product_upload_limit"><?php echo e(translate('Product Upload Limit')); ?></label>
                    <div class="col-sm-10">
                        <input type="number" min="0" step="1" placeholder="<?php echo e(translate('Product Upload Limit')); ?>" value="<?php echo e($seller_package->product_upload_limit); ?>" id="product_upload_limit" name="product_upload_limit" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="duration"><?php echo e(translate('Duration')); ?></label>
                    <div class="col-sm-10">
                        <input type="number" min="0" step="1" placeholder="<?php echo e(translate('Validity in number of days')); ?>" value="<?php echo e($seller_package->duration); ?>" id="duration" name="duration" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="signinSrEmail"><?php echo e(translate('Package Logo')); ?></label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                            </div>
                            <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                            <input type="hidden" name="logo" value="<?php echo e($seller_package->logo); ?>" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="duration"><?php echo e(translate('Max profit')); ?></label>
                    <div class="input-group col-sm-10">
                        <input type="number" min="0" step="1" placeholder="<?php echo e(translate('Max profit')); ?>" value="<?php echo e($seller_package->getTranslation('max_profit', $lang)); ?>" id="max_profit" name="max_profit" class="form-control" required>
                        <div class="input-group-append">
                            <span class="input-group-text" id="basic-addon2">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller_packages/edit.blade.php ENDPATH**/ ?>