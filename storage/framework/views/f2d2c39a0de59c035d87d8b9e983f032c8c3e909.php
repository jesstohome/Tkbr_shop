<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Salesman Information')); ?></h5>
            </div>

            <form class="form-horizontal" action="<?php echo e(route('salesmans.update', $user->id)); ?>" method="POST">
                <input name="_method" type="hidden" value="PATCH">
            	<?php echo csrf_field(); ?>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name"><?php echo e(translate('Full Name')); ?></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="<?php echo e(translate('Full Name')); ?>" id="name" name="name" class="form-control" required value="<?php echo e($user->name); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email"><?php echo e(translate('Email')); ?></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="<?php echo e(translate('Email')); ?>" id="email" name="email" class="form-control" required value="<?php echo e($user->email); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email"><?php echo e(translate('Phone')); ?></label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="<?php echo e(translate('Phone')); ?>" id="phone" name="phone" class="form-control" required value="<?php echo e($user->phone); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password"><?php echo e(translate('Password')); ?></label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="<?php echo e(translate('Password')); ?>" id="password" name="password" class="form-control">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/salesman/salesmans/edit.blade.php ENDPATH**/ ?>