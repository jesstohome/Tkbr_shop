

<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col">
			<h1 class="h3"><?php echo e(translate('Guarantee')); ?></h1>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 mx-auto">
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0"><?php echo e(translate('Guarantee')); ?></h6>
			</div>
			<div class="card-body">
				<form action="<?php echo e(route('business_settings.update2')); ?>" method="POST" enctype="multipart/form-data">
					<?php echo csrf_field(); ?>
				 
	                
				     <div class="form-group row">
						<label class="col-md-3 col-from-label"><?php echo e(translate('Must Guarantee?')); ?></label>
						<div class="col-md-8">
							<label class="aiz-switch aiz-switch-success mb-0">
								<input type="hidden" name="types[]" value="must_guarantee">
								<input type="checkbox" name="must_guarantee" <?php if( get_setting('must_guarantee') == 'on'): ?> checked <?php endif; ?>>
								<span></span>
							</label>
						</div>
					</div>
                    <div class="border-top pt-3">
                        <div class="form-group row">
							<label class="col-md-3 col-from-label"><?php echo e(translate('Guarantee Money')); ?></label>
							<div class="col-md-8">
								<div class="form-group">
									<input type="hidden" name="types[]" value="guarantee_money">
									<input type="text" class="form-control" placeholder="<?php echo e(translate('Guarantee Money')); ?>" name="guarantee_money" value="<?php echo e(get_setting('guarantee_money')); ?>">
								</div>
							</div>
						</div>
                    </div>
				     <div class="form-group row">
						<label class="col-md-3 col-from-label"><?php echo e(translate('Guarantee Pay Close?')); ?></label>
						<div class="col-md-8">
							<label class="aiz-switch aiz-switch-success mb-0">
								<input type="hidden" name="types[]" value="must_guarantee_close">
								<input type="checkbox" name="must_guarantee_close" <?php if( get_setting('must_guarantee_close') == 'on'): ?> checked <?php endif; ?>>
								<span></span>
							</label>
						</div>
					</div>
					 
					</div>
					<div class="text-right">
						<button style=" margin-right:20px;" type="submit" class="btn btn-primary"><?php echo e(translate('Update')); ?></button>
					</div>
					<BR>
				</form>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/website_settings/guarantee.blade.php ENDPATH**/ ?>