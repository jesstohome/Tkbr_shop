<div class="">
    <?php $__currentLoopData = \App\Models\Address::where('user_id',$user_id)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label class="aiz-megabox d-block bg-white" style="display:block">
            <input type="radio" name="address_id" value="<?php echo e($address->id); ?>" <?php if($address->set_default): ?> checked <?php endif; ?> required>
            <span class="d-flex p-3 pad-all aiz-megabox-elem">
                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                <span class="flex-grow-1 pl-3 pad-lft">
                    <div>
                        <span class="alpha-6"><?php echo e(translate('Address')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->address); ?></span>
                    </div>
                    <div>
                        <span class="alpha-6"><?php echo e(translate('Postal Code')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->postal_code); ?></span>
                    </div>
                    <div>
                        <span class="alpha-6"><?php echo e(translate('City')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->city->name); ?></span>
                    </div>
                    <div>
                        <span class="alpha-6"><?php echo e(translate('State')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->state->name); ?></span>
                    </div>
                    <div>
                        <span class="alpha-6"><?php echo e(translate('Country')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->country->name); ?></span>
                    </div>
                    <div>
                        <span class="alpha-6"><?php echo e(translate('Phone')); ?>:</span>
                        <span class="strong-600 ml-2"><?php echo e($address->phone); ?></span>
                    </div>
                </span>
            </span>
        </label>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <input type="hidden" id="customer_id" value="<?php echo e($user_id); ?>" >
    <div class="" onclick="add_new_address()">
        <div class="border p-3 rounded mb-3 bord-all pad-all c-pointer text-center bg-white">
            <i class="fa fa-plus fa-2x"></i>
            <div class="alpha-7"><?php echo e(translate('Add New Address')); ?></div>
        </div>
    </div>
</div>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/pos/shipping_address.blade.php ENDPATH**/ ?>