<form class="" action="<?php echo e(route('seller.make_offline_payment')); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="package_id" value="<?php echo e($package_id); ?>">
    <div class="modal-body gry-bg px-3 pt-3 mx-auto">
        <div class="align-items-center gutters-5 row">
            <?php $__currentLoopData = \App\Models\ManualPaymentMethod::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-4">
                    <label class="aiz-megabox d-block mb-3">
                        <input value="<?php echo e($method->heading); ?>" id="payment_option" type="radio" name="payment_option" onchange="toggleManualPaymentData(<?php echo e($method->id); ?>)" checked>
                        <span class="d-block p-3 aiz-megabox-elem">
                            <img src="<?php echo e(uploaded_asset($method->photo)); ?>" class="img-fluid mb-2">
                            <span class="d-block text-center">
                                <span class="d-block fw-600 fs-15"><?php echo e($method->heading); ?></span>
                            </span>
                        </span>
                    </label>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="manual_payment_data">
            <div class="card mb-3 p-3 d-none">
                <div id="manual_payment_description">

                </div>
            </div>

            <div class="card mb-3 p-3">
                <div class="row">
                    <div class="col-md-3">
                        <label><?php echo e(translate('Transaction ID')); ?> <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control mb-3" name="trx_id" placeholder="<?php echo e(translate('Transaction ID')); ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><?php echo e(translate('Photo')); ?></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                            </div>
                            <div class="form-control file-amount"><?php echo e(translate('Choose image')); ?></div>
                            <input type="hidden" name="photo" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1"><?php echo e(translate('Confirm')); ?></button>
        </div>
    </div>
</form>

<?php $__currentLoopData = \App\Models\ManualPaymentMethod::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div id="manual_payment_info_<?php echo e($method->id); ?>" class="d-none">
      <div><?php echo $method->description ?></div>
      <?php if($method->bank_info != null): ?>
          <ul>
              <?php $__currentLoopData = json_decode($method->bank_info); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <li><?php echo e(translate('Bank Name')); ?> - <?php echo e($info->bank_name); ?>, <?php echo e(translate('Account Name')); ?> - <?php echo e($info->account_name); ?>, <?php echo e(translate('Account Number')); ?> - <?php echo e($info->account_number); ?>, <?php echo e(translate('Routing Number')); ?> - <?php echo e($info->routing_number); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
      <?php endif; ?>
  </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<script type="text/javascript">
    function toggleManualPaymentData(id){
        $('#manual_payment_description').parent().removeClass('d-none');
        $('#manual_payment_description').html($('#manual_payment_info_'+id).html());
    }
</script>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/manual_payment_methods/frontend/offline_seller_package_purchase_modal.blade.php ENDPATH**/ ?>