<?php $__env->startSection('content'); ?>
<section class="pt-4 mb-4">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4"><?php echo e(translate('Register your shop')); ?></h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="<?php echo e(route('home')); ?>"><?php echo e(translate('Home')); ?></a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="<?php echo e(route('shops.create')); ?>">"<?php echo e(translate('Register your shop')); ?>"</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section class="pt-4 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <form id="shop" class="" action="<?php echo e(route('shops.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php if(!Auth::check()): ?>
                        <div class="bg-white rounded shadow-sm mb-3">
                            <div class="fs-15 fw-600 p-3 border-bottom">
                                <?php echo e(translate('Personal Info')); ?>

                            </div>
                            <div class="p-3">
                                <div class="form-group">
                                    <label><?php echo e(translate('Your Name')); ?> <span class="text-primary">*</span></label>
                                    <input type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(translate('Name')); ?>" name="name">
                                </div>
                                <div class="form-group">
                                    <label><?php echo e(translate('Your Email')); ?> <span class="text-primary">*</span></label>
                                    <input type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email">
                                </div>
                                <div class="form-group">
                                    <label><?php echo e(translate('Your Password')); ?> <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="<?php echo e(translate('Password')); ?>" name="password">
                                </div>
                                <div class="form-group">
                                    <label><?php echo e(translate('Repeat Password')); ?> <span class="text-primary">*</span></label>
                                    <input type="password" class="form-control" placeholder="<?php echo e(translate('Confirm Password')); ?>" name="password_confirmation">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="bg-white rounded shadow-sm mb-4">
                        <div class="fs-15 fw-600 p-3 border-bottom">
                            <?php echo e(translate('Basic Info')); ?>

                        </div>
                        <div class="p-3">
                            <div class="form-group">
                                <label><?php echo e(translate('Shop Name')); ?> <span class="text-primary">*</span></label>
                                <input type="text" class="form-control" placeholder="<?php echo e(translate('Shop Name')); ?>" name="name" required>
                            </div>
                            <div class="form-group">
                                <label><?php echo e(translate('Address')); ?> <span class="text-primary">*</span></label>
                                <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Address')); ?>" name="address" required>
                            </div>
                            
                             <div class="form-group">
                                <label><?php echo e(translate('Certificates Type')); ?> <span class="text-primary">*</span></label>
                                 <select class="form-control" name="certtype">
                                     <option value="idcard"> <?php echo e(translate('id card')); ?></option>
                                     <option value="passport"> <?php echo e(translate('passport')); ?></option>
                                     <option value="driving license"> <?php echo e(translate('driving license')); ?></option>
                                     <option value="social security card"> <?php echo e(translate('Social Security Card')); ?></option>
                                 </select>
                                 
                                
                                </div>
                            <div class="form-group">
                                <label><?php echo e(translate('Certificates Front')); ?> <span class="text-primary">*</span></label>
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                                    </div>
                                    <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                                    <input type="hidden" name="identity_card_front" value="" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                            
                            
                            <div class="form-group">
                                <label><?php echo e(translate('Certificates Back')); ?> <span class="text-primary">*</span></label>
                             
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                                    </div>
                                    <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                                    <input type="hidden" name="identity_card_back" value="" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(get_setting('google_recaptcha') == 1): ?>
                        <div class="form-group mt-2 mx-auto row">
                            <div class="g-recaptcha" data-sitekey="<?php echo e(env('CAPTCHA_KEY')); ?>"></div>
                        </div>
                    <?php endif; ?>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary fw-600"><?php echo e(translate('Register Your Shop')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script type="text/javascript">
    // making the CAPTCHA  a required field for form submission
    $(document).ready(function(){
        // alert('helloman');
        $("#shop").on("submit", function(evt)
        {
            var response = grecaptcha.getResponse();
            if(response.length == 0)
            {
            //reCaptcha not verified
                alert("please verify you are humann!");
                evt.preventDefault();
                return false;
            }
            //captcha verified
            //do the rest of your validations here
            $("#reg-form").submit();
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/seller_form.blade.php ENDPATH**/ ?>