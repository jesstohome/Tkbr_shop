<?php $__env->startSection('panel_content'); ?>

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Shop Settings')); ?>

                <a href="<?php echo e(route('shop.visit', $shop->slug)); ?>" class="btn btn-link btn-sm" target="_blank">(<?php echo e(translate('Visit Shop')); ?>)<i class="la la-external-link"></i>)</a>
            </h1>
        </div>
      </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Basic Info')); ?></h5>
        </div>
        <div class="card-body">
            <form class="" action="<?php echo e(route('seller.shop.update')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="<?php echo e($shop->id); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Shop Name')); ?><span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Shop Name')); ?>" name="name" value="<?php echo e($shop->name); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Shop Logo')); ?></label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                            </div>
                            <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                            <input type="hidden" name="logo" value="<?php echo e($shop->logo); ?>" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">
                        <?php echo e(translate('Shop Phone')); ?>

                    </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Phone')); ?>" name="phone" value="<?php echo e($shop->phone); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Shop Address')); ?> <span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Address')); ?>" name="address" value="<?php echo e($shop->address); ?>" required>
                    </div>
                </div>
                <?php if(get_setting('shipping_type') == 'seller_wise_shipping'): ?>
                    <div class="row">
                        <div class="col-md-2">
                            <label><?php echo e(translate('Shipping Cost')); ?> <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" lang="en" min="0" class="form-control mb-3" placeholder="<?php echo e(translate('Shipping Cost')); ?>" name="shipping_cost" value="<?php echo e($shop->shipping_cost); ?>" required>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Meta Title')); ?><span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Meta Title')); ?>" name="meta_title" value="<?php echo e($shop->meta_title); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Meta Description')); ?><span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <textarea name="meta_description" rows="3" class="form-control mb-3" required><?php echo e($shop->meta_description); ?></textarea>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>

    <form action="<?php echo e(route('seller.shop.online_service_update')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="shop_id" value="<?php echo e($shop->id); ?>">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Change Online Service')); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label><?php echo e(translate('customer service link')); ?></label>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group mb-3">
                            <input type="text" name="online_ervice" value="<?php echo e($shop->online_ervice); ?>" class="form-control mb-3" placeholder="<?php echo e(translate('Online Service')); ?>">
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php if(addon_is_activated('delivery_boy')): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Delivery Boy Pickup Point')); ?></h5>
            </div>
            <div class="card-body">
                <form class="" action="<?php echo e(route('seller.shop.update')); ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="shop_id" value="<?php echo e($shop->id); ?>">
                    <?php echo csrf_field(); ?>

                    <?php if(get_setting('google_map') == 1): ?>
                        <div class="row mb-3">
                            <input id="searchInput" class="controls" type="text" placeholder="<?php echo e(translate('Enter a location')); ?>">
                            <div id="map"></div>
                            <ul id="geoData">
                                <li style="display: none;"><?php echo e(translate('Full Address')); ?>: <span id="location"></span></li>
                                <li style="display: none;"><?php echo e(translate('Postal Code')); ?>: <span id="postal_code"></span></li>
                                <li style="display: none;"><?php echo e(translate('Country')); ?>: <span id="country"></span></li>
                                <li style="display: none;"><?php echo e(translate('Latitude')); ?>: <span id="lat"></span></li>
                                <li style="display: none;"><?php echo e(translate('Longitude')); ?>: <span id="lon"></span></li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname"><?php echo e(translate('Longitude')); ?></label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="longitude" name="delivery_pickup_longitude" readonly="" value="<?php echo e($shop->delivery_pickup_longitude); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname"><?php echo e(translate('Latitude')); ?></label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="latitude" name="delivery_pickup_latitude" readonly="" value="<?php echo e($shop->delivery_pickup_latitude); ?>">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname"><?php echo e(translate('Longitude')); ?></label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="longitude" name="delivery_pickup_longitude" value="<?php echo e($shop->delivery_pickup_longitude); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname"><?php echo e(translate('Latitude')); ?></label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="latitude" name="delivery_pickup_latitude" value="<?php echo e($shop->delivery_pickup_latitude); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Banner Settings')); ?></h5>
        </div>
        <div class="card-body">
            <form class="" action="<?php echo e(route('seller.shop.update')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="<?php echo e($shop->id); ?>">
                <?php echo csrf_field(); ?>

                <div class="row mb-3">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Banners')); ?> (1500x450)</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                            </div>
                            <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                            <input type="hidden" name="sliders" value="<?php echo e($shop->sliders); ?>" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted"><?php echo e(translate('We had to limit height to maintian consistancy. In some device both side of the banner might be cropped for height limitation.')); ?></small>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Social Media Link')); ?></h5>
        </div>
        <div class="card-body">
            <form class="" action="<?php echo e(route('seller.shop.update')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="<?php echo e($shop->id); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-box-content p-3">
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label"><?php echo e(translate('Facebook')); ?></label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="<?php echo e(translate('Facebook')); ?>" name="facebook" value="<?php echo e($shop->facebook); ?>">
                            <small class="text-muted"><?php echo e(translate('Insert link with https ')); ?></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label"><?php echo e(translate('Instagram')); ?></label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="<?php echo e(translate('Instagram')); ?>" name="instagram" value="<?php echo e($shop->instagram); ?>">
                            <small class="text-muted"><?php echo e(translate('Insert link with https ')); ?></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label"><?php echo e(translate('Twitter')); ?></label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="<?php echo e(translate('Twitter')); ?>" name="twitter" value="<?php echo e($shop->twitter); ?>">
                            <small class="text-muted"><?php echo e(translate('Insert link with https ')); ?></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label"><?php echo e(translate('Google')); ?></label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="<?php echo e(translate('Google')); ?>" name="google" value="<?php echo e($shop->google); ?>">
                            <small class="text-muted"><?php echo e(translate('Insert link with https ')); ?></small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label"><?php echo e(translate('Youtube')); ?></label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="<?php echo e(translate('Youtube')); ?>" name="youtube" value="<?php echo e($shop->youtube); ?>">
                            <small class="text-muted"><?php echo e(translate('Insert link with https ')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

    <?php if(get_setting('google_map') == 1): ?>

        <?php echo $__env->make('frontend.partials.google_map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/shop.blade.php ENDPATH**/ ?>