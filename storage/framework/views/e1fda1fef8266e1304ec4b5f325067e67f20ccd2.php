<section class="bg-white border-top mt-auto">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="<?php echo e(route('terms')); ?>">
                    <i class="la la-file-text la-3x text-primary mb-2"></i>
                    <h4 class="h6"><?php echo e(translate('Terms & conditions')); ?></h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="<?php echo e(route('returnpolicy')); ?>">
                    <i class="la la-mail-reply la-3x text-primary mb-2"></i>
                    <h4 class="h6"><?php echo e(translate('Return Policy')); ?></h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="<?php echo e(route('supportpolicy')); ?>">
                    <i class="la la-support la-3x text-primary mb-2"></i>
                    <h4 class="h6"><?php echo e(translate('Support Policy')); ?></h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left border-right text-center p-4 d-block" href="<?php echo e(route('privacypolicy')); ?>">
                    <i class="las la-exclamation-circle la-3x text-primary mb-2"></i>
                    <h4 class="h6"><?php echo e(translate('Privacy Policy')); ?></h4>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-dark py-5 text-light footer-widget">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-xl-4 text-center text-md-left">
                <div class="mt-4">
                    <a href="<?php echo e(route('home')); ?>" class="d-block">
                        <?php if(get_setting('footer_logo') != null): ?>
                            <img class="lazyload" src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>" data-src="<?php echo e(uploaded_asset(get_setting('footer_logo'))); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="44">
                        <?php else: ?>
                            <img class="lazyload" src="<?php echo e(static_asset('assets/img/placeholder-rect.jpg')); ?>" data-src="<?php echo e(static_asset('assets/img/logo.png')); ?>" alt="<?php echo e(env('APP_NAME')); ?>" height="44">
                        <?php endif; ?>
                    </a>
                    <div class="my-3">
                        <?php echo get_setting('about_us_description',null,App::getLocale()); ?>

                    </div>
                    <div class="d-inline-block d-md-block mb-4">
                        <form class="form-inline" method="POST" action="<?php echo e(route('subscribers.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group mb-0">
                                <input type="email" class="form-control" placeholder="<?php echo e(translate('Your Email Address')); ?>" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <?php echo e(translate('Subscribe')); ?>

                            </button>
                        </form>
                    </div>
                    <div class="w-300px mw-100 mx-auto mx-md-0">
                        <?php if(get_setting('play_store_link') != null): ?>
                            <a href="<?php echo e(get_setting('play_store_link')); ?>" target="_blank" class="d-inline-block mr-3 ml-0">
                                <img src="<?php echo e(static_asset('assets/img/play.png')); ?>" class="mx-100 h-40px">
                            </a>
                        <?php endif; ?>
                        <?php if(get_setting('app_store_link') != null): ?>
                            <a href="<?php echo e(get_setting('app_store_link')); ?>" target="_blank" class="d-inline-block">
                                <img src="<?php echo e(static_asset('assets/img/app.png')); ?>" class="mx-100 h-40px">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 ml-xl-auto col-md-4 mr-0">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                        <?php echo e(translate('Contact Info')); ?>

                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                           <span class="d-block opacity-30"><?php echo e(translate('Address')); ?>:</span>
                           <span class="d-block opacity-70"><?php echo e(get_setting('contact_address',null,App::getLocale())); ?></span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block opacity-30"><?php echo e(translate('Phone')); ?>:</span>
                           <span class="d-block opacity-70"><?php echo e(get_setting('contact_phone')); ?></span>
                        </li>
                        <li class="mb-2">
                           <span class="d-block opacity-30"><?php echo e(translate('Email')); ?>:</span>
                           <span class="d-block opacity-70">
                               <a href="mailto:<?php echo e(get_setting('contact_email')); ?>" class="text-reset"><?php echo e(get_setting('contact_email')); ?></a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                        <?php echo e(get_setting('widget_one',null,App::getLocale())); ?>

                    </h4>
                    <ul class="list-unstyled">
                        <?php if( get_setting('widget_one_labels',null,App::getLocale()) !=  null ): ?>
                            <?php $__currentLoopData = json_decode( get_setting('widget_one_labels',null,App::getLocale()), true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="mb-2">
                                <a href="<?php echo e(json_decode( get_setting('widget_one_links'), true)[$key]); ?>" class="opacity-50 hov-opacity-100 text-reset">
                                    <?php echo e($value); ?>

                                </a>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                        <?php echo e(translate('My Account')); ?>

                    </h4>
                    <ul class="list-unstyled">
                        <?php if(Auth::check()): ?>
                            <li class="mb-2">
                                <a class="opacity-50 hov-opacity-100 text-reset" href="<?php echo e(route('logout')); ?>">
                                    <?php echo e(translate('Logout')); ?>

                                </a>
                            </li>
                        <?php else: ?>
                            <li class="mb-2">
                                <a class="opacity-50 hov-opacity-100 text-reset" href="<?php echo e(route('user.login')); ?>">
                                    <?php echo e(translate('Login')); ?>

                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="<?php echo e(route('purchase_history.index')); ?>">
                                <?php echo e(translate('Order History')); ?>

                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="<?php echo e(route('wishlists.index')); ?>">
                                <?php echo e(translate('My Wishlist')); ?>

                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="<?php echo e(route('orders.track')); ?>">
                                <?php echo e(translate('Track Order')); ?>

                            </a>
                        </li>
                        <?php if(addon_is_activated('affiliate_system')): ?>
                            <li class="mb-2">
                                <a class="opacity-50 hov-opacity-100 text-light" href="<?php echo e(route('affiliate.apply')); ?>"><?php echo e(translate('Be an affiliate partner')); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php if(get_setting('vendor_system_activation') == 1): ?>
                    <div class="text-center text-md-left mt-4">
                        <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                            <?php echo e(translate('Be a Seller')); ?>

                        </h4>
                        <a href="<?php echo e(route('shops.create')); ?>" class="btn btn-primary btn-sm shadow-md">
                            <?php echo e(translate('Apply Now')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="pt-3 pb-7 pb-xl-3 bg-black text-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="text-center text-md-left">
                </div>
            </div>
            <div class="col-lg-4">
                <?php if( get_setting('show_social_links') ): ?>
                <ul class="list-inline my-3 my-md-0 social colored text-center">
                    <?php if( get_setting('facebook_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('facebook_link')); ?>" target="_blank" class="facebook"><i class="lab la-facebook-f"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('twitter_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('twitter_link')); ?>" target="_blank" class="twitter"><i class="lab la-twitter"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('instagram_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('instagram_link')); ?>" target="_blank" class="instagram"><i class="lab la-instagram"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('youtube_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('youtube_link')); ?>" target="_blank" class="youtube"><i class="lab la-youtube"></i></a>
                    </li>
                    <?php endif; ?>
                    <?php if( get_setting('linkedin_link') !=  null ): ?>
                    <li class="list-inline-item">
                        <a href="<?php echo e(get_setting('linkedin_link')); ?>" target="_blank" class="linkedin"><i class="lab la-linkedin-in"></i></a>
                    </li>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="text-center text-md-right">
                    <ul class="list-inline mb-0">
                        <?php if( get_setting('payment_method_images') !=  null ): ?>
                            <?php $__currentLoopData = explode(',', get_setting('payment_method_images')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-inline-item">
                                    <img src="<?php echo e(uploaded_asset($value)); ?>" height="30" class="mw-100 h-auto" style="max-height: 30px">
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>


<div class="aiz-mobile-bottom-nav d-xl-none fixed-bottom bg-white shadow-lg border-top rounded-top" style="box-shadow: 0px -1px 10px rgb(0 0 0 / 15%)!important; ">
    <div class="row align-items-center gutters-5">
        <div class="col">
            <a href="<?php echo e(route('home')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                <i class="las la-home fs-20 opacity-60 <?php echo e(areActiveRoutes(['home'],'opacity-100 text-primary')); ?>"></i>
                <span class="d-block fs-10 fw-600 opacity-60 <?php echo e(areActiveRoutes(['home'],'opacity-100 fw-600')); ?>"><?php echo e(translate('Home')); ?></span>
            </a>
        </div>
        <div class="col">
            <a href="<?php echo e(route('categories.all')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                <i class="las la-list-ul fs-20 opacity-60 <?php echo e(areActiveRoutes(['categories.all'],'opacity-100 text-primary')); ?>"></i>
                <span class="d-block fs-10 fw-600 opacity-60 <?php echo e(areActiveRoutes(['categories.all'],'opacity-100 fw-600')); ?>"><?php echo e(translate('Categories')); ?></span>
            </a>
        </div>
        <?php
            if(auth()->user() != null) {
                $user_id = Auth::user()->id;
                $cart = \App\Models\Cart::where('user_id', $user_id)->get();
            } else {
                $temp_user_id = Session()->get('temp_user_id');
                if($temp_user_id) {
                    $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
                }
            }
        ?>
        <div class="col-auto">
            <a href="<?php echo e(route('cart')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                <span class="align-items-center bg-primary border border-white border-width-4 d-flex justify-content-center position-relative rounded-circle size-50px" style="margin-top: -33px;box-shadow: 0px -5px 10px rgb(0 0 0 / 15%);border-color: #fff !important;">
                    <i class="las la-shopping-bag la-2x text-white"></i>
                </span>
                <span class="d-block mt-1 fs-10 fw-600 opacity-60 <?php echo e(areActiveRoutes(['cart'],'opacity-100 fw-600')); ?>">
                    <?php echo e(translate('Cart')); ?>

                    <?php
                        $count = (isset($cart) && count($cart)) ? count($cart) : 0;
                    ?>
                    (<span class="cart-count"><?php echo e($count); ?></span>)
                </span>
            </a>
        </div>
        <div class="col">
            <a href="<?php echo e(route('all-notifications')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                <span class="d-inline-block position-relative px-2">
                    <i class="las la-bell fs-20 opacity-60 <?php echo e(areActiveRoutes(['all-notifications'],'opacity-100 text-primary')); ?>"></i>
                    <?php if(Auth::check() && count(Auth::user()->unreadNotifications) > 0): ?>
                        <span class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right" style="right: 7px;top: -2px;"></span>
                    <?php endif; ?>
                </span>
                <span class="d-block fs-10 fw-600 opacity-60 <?php echo e(areActiveRoutes(['all-notifications'],'opacity-100 fw-600')); ?>"><?php echo e(translate('Notifications')); ?></span>
            </a>
        </div>
        <div class="col">
        <?php if(Auth::check()): ?>
            <?php if(isAdmin()): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                    <span class="d-block mx-auto">
                        <?php if(Auth::user()->photo != null): ?>
                            <img src="<?php echo e(custom_asset(Auth::user()->avatar_original)); ?>" class="rounded-circle size-20px">
                        <?php else: ?>
                            <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" class="rounded-circle size-20px">
                        <?php endif; ?>
                    </span>
                    <span class="d-block fs-10 fw-600 opacity-60"><?php echo e(translate('Account')); ?></span>
                </a>
            <?php else: ?>
                <a href="javascript:void(0)" class="text-reset d-block text-center pb-2 pt-3 mobile-side-nav-thumb" data-toggle="class-toggle" data-backdrop="static" data-target=".aiz-mobile-side-nav">
                    <span class="d-block mx-auto">
                        <?php if(Auth::user()->photo != null): ?>
                            <img src="<?php echo e(custom_asset(Auth::user()->avatar_original)); ?>" class="rounded-circle size-20px">
                        <?php else: ?>
                            <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" class="rounded-circle size-20px">
                        <?php endif; ?>
                    </span>
                    <span class="d-block fs-10 fw-600 opacity-60"><?php echo e(translate('Account')); ?></span>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo e(route('user.login')); ?>" class="text-reset d-block text-center pb-2 pt-3">
                <span class="d-block mx-auto">
                    <img src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" class="rounded-circle size-20px">
                </span>
                <span class="d-block fs-10 fw-600 opacity-60"><?php echo e(translate('Account')); ?></span>
            </a>
        <?php endif; ?>
        </div>
    </div>
</div>
<?php if(Auth::check() && !isAdmin()): ?>
    <div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
        <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle" data-backdrop="static" data-target=".aiz-mobile-side-nav" data-same=".mobile-side-nav-thumb"></div>
        <div class="collapse-sidebar bg-white">
            <?php echo $__env->make('frontend.inc.user_side_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH D:\wamp64\www\alicrossborder.com\resources\views/frontend/inc/footer.blade.php ENDPATH**/ ?>