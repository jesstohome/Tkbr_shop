

<?php $__env->startSection('content'); ?>
    <section class="pt-4 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    <h1 class="fw-600 h4"><?php echo e(translate('Affiliate Informations')); ?></h1>
                </div>
                <div class="col-lg-6">
                    <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="<?php echo e(route('home')); ?>"><?php echo e(translate('Home')); ?></a>
                        </li>
                        <li class="text-dark fw-600 breadcrumb-item">
                            <a class="text-reset" href="<?php echo e(route('affiliate.apply')); ?>">"<?php echo e(translate('Affiliate')); ?>"</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form class="" action="<?php echo e(route('affiliate.store_affiliate_user')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php if(!Auth::check()): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 h6"><?php echo e(translate('User Info')); ?></h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group input-group--style-1">
                                                    <input type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(translate('Name')); ?>" name="name">
                                                    <span class="input-group-addon">
                                                        <i class="las la-user"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group input-group--style-1">
                                                    <input type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email">
                                                    <span class="input-group-addon">
                                                        <i class="las la-envelope"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group input-group--style-1">
                                                    <input type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="<?php echo e(translate('Password')); ?>" name="password">
                                                    <span class="input-group-addon">
                                                        <i class="las la-lock"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="input-group input-group--style-1">
                                                    <input type="password" class="form-control" placeholder="<?php echo e(translate('Confirm Password')); ?>" name="password_confirmation">
                                                    <span class="input-group-addon">
                                                        <i class=" las la-lock"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6"><?php echo e(translate('Verification info')); ?></h5>
                            </div>
                            <div class="card-body">
                                <?php
                                    $verification_form = \App\Models\AffiliateConfig::where('type', 'verification_form')->first()->value;
                                ?>
                                    <?php $__currentLoopData = json_decode($verification_form); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($element->type == 'text'): ?>
                                            <div class="row">
                                                <label class="col-md-2 col-form-label"><?php echo e($element->label); ?> <span class="text-danger">*</span></label>
                                                <div class="col-md-10">
                                                    <input type="<?php echo e($element->type); ?>" class="form-control mb-3" placeholder="<?php echo e($element->label); ?>" name="element_<?php echo e($key); ?>" required>
                                                </div>
                                            </div>
                                        <?php elseif($element->type == 'file'): ?>
                                            <div class="row">
                                                <label class="col-md-2 col-form-label"><?php echo e($element->label); ?></label>
                                                <div class="col-md-10">
                                                    <input type="<?php echo e($element->type); ?>" name="element_<?php echo e($key); ?>" id="file-<?php echo e($key); ?>" class="custom-input-file custom-input-file--4" data-multiple-caption="{count} files selected" required/>
                                                    <label for="file-<?php echo e($key); ?>" class="mw-100 mb-3">
                                                        <span></span>
                                                        <strong>
                                                            <i class="fa fa-upload"></i>
                                                            <?php echo e(translate('Choose file')); ?>

                                                        </strong>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php elseif($element->type == 'select' && is_array(json_decode($element->options))): ?>
                                            <div class="row">
                                                <label class="col-md-2 col-form-label"><?php echo e($element->label); ?></label>
                                                <div class="col-md-10">
                                                    <div class="mb-3">
                                                        <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_<?php echo e($key); ?>" required>
                                                            <?php $__currentLoopData = json_decode($element->options); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif($element->type == 'multi_select' && is_array(json_decode($element->options))): ?>
                                            <div class="row">
                                                <label class="col-md-2 col-form-label"><?php echo e($element->label); ?></label>
                                                <div class="col-md-10">
                                                    <div class="mb-3">
                                                        <select class="form-control selectpicker" data-minimum-results-for-search="Infinity" name="element_<?php echo e($key); ?>[]" multiple required>
                                                            <?php $__currentLoopData = json_decode($element->options); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($value); ?>"><?php echo e($value); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif($element->type == 'radio'): ?>
                                            <div class="row">
                                                <label class="col-md-2 col-form-label"><?php echo e($element->label); ?></label>
                                                <div class="col-md-10">
                                                    <div class="mb-3">
                                                        <?php $__currentLoopData = json_decode($element->options); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="radio radio-inline">
                                                                <input type="radio" name="element_<?php echo e($key); ?>" value="<?php echo e($value); ?>" id="<?php echo e($value); ?>" required>
                                                                <label for="<?php echo e($value); ?>"><?php echo e($value); ?></label>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary"><?php echo e(translate('Save')); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/affiliate/frontend/apply_for_affiliate.blade.php ENDPATH**/ ?>