<div class="aiz-topbar px-15px px-lg-25px d-flex align-items-stretch justify-content-between">
    <div class="d-flex">
        <div class="aiz-topbar-nav-toggler d-flex align-items-center justify-content-start mr-2 mr-md-3 ml-0" data-toggle="aiz-mobile-nav">
            <button class="aiz-mobile-toggler">
                <span></span>
            </button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-stretch flex-grow-xl-1">
        <div class="d-flex justify-content-around align-items-center align-items-stretch">
            <div class="d-flex justify-content-around align-items-center align-items-stretch">
                <div class="aiz-topbar-item">
                    <div class="d-flex align-items-center">
                        <a class="btn btn-icon btn-circle btn-light" href="<?php echo e(route('home')); ?>" target="_blank" title="<?php echo e(translate('Browse Website')); ?>">
                            <i class="las la-globe"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php if(addon_is_activated('pos_system')): ?>
                <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                    <div class="aiz-topbar-item">
                        <div class="d-flex align-items-center">
                            <a class="btn btn-icon btn-circle btn-light" href="<?php echo e(route('poin-of-sales.index')); ?>" target="_blank" title="<?php echo e(translate('POS')); ?>">
                                <i class="las la-print"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-around align-items-center align-items-stretch ml-3">
                <div class="aiz-topbar-item">
                    <div class="d-flex align-items-center">
                        <a class="btn btn-soft-danger btn-sm d-flex align-items-center" href="<?php echo e(route('cache.clear')); ?>">
                            <i class="las la-hdd fs-20"></i>
                            <span class="fw-500 ml-1 mr-0 d-none d-md-block"><?php echo e(translate('Clear Cache')); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-around align-items-center align-items-stretch">

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon p-0 d-flex justify-content-center align-items-center">
                            <span class="d-flex align-items-center position-relative">
                                <i class="las la-bell fs-24"></i>
                                <?php if(Auth::user()->unreadNotifications->count() > 0): ?>
                                    <span class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-lg py-0">
                        <div class="p-3 bg-light border-bottom">
                            <h6 class="mb-0"><?php echo e(translate('Notifications')); ?></h6>
                        </div>
                        <div class="px-3 c-scrollbar-light overflow-auto " style="max-height:300px;">
                            <ul class="list-group list-group-flush">
                                <?php $__empty_1 = true; $__currentLoopData = Auth::user()->unreadNotifications->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                        <div class="media text-inherit">
                                            <div class="media-body">
                                                <?php if($notification->type == 'App\Notifications\OrderNotification'): ?>
                                                    <p class="mb-1 text-truncate-2">
                                                        <?php echo e(translate('Order code: ')); ?> <?php echo e($notification->data['order_code']); ?> <?php echo e(translate('has been '. ucfirst(str_replace('_', ' ', $notification->data['status'])))); ?>

                                                    </p>
                                                    <small class="text-muted">
                                                        <?php echo e(date("F j Y, g:i a", strtotime($notification->created_at))); ?>

                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li class="list-group-item">
                                        <div class="py-4 text-center fs-16">
                                            <?php echo e(translate('No notification found')); ?>

                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="text-center border-top">
                            <a href="<?php echo e(route('admin.all-notification')); ?>" class="text-reset d-block py-2">
                                <?php echo e(translate('View All Notifications')); ?>

                            </a>
                        </div>
                    </div>
                </div>
            </div>

            
            <?php
                if(Session::has('locale')){
                    $locale = Session::get('locale', Config::get('app.locale'));
                }
                else{
                    $locale = env('DEFAULT_LANGUAGE');
                }
            ?>
            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown " id="lang-change">
                    <a class="dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="btn btn-icon">
                            <img src="<?php echo e(static_asset('assets/img/flags/'.$locale.'.png')); ?>" height="11">
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-xs">

                        <?php $__currentLoopData = \App\Models\Language::where('status', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="javascript:void(0)" data-flag="<?php echo e($language->code); ?>" class="dropdown-item <?php if($locale == $language->code): ?> active <?php endif; ?>">
                                    <img src="<?php echo e(static_asset('assets/img/flags/'.$language->code.'.png')); ?>" class="mr-2">
                                    <span class="language"><?php echo e($language->name); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>

            <div class="aiz-topbar-item ml-2">
                <div class="align-items-stretch d-flex dropdown">
                    <a class="dropdown-toggle no-arrow text-dark" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <span class="avatar avatar-sm mr-md-2">
                                <img
                                    src="<?php echo e(uploaded_asset(Auth::user()->avatar_original)); ?>"
                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';"
                                >
                            </span>
                            <span class="d-none d-md-block">
                                <span class="d-block fw-500"><?php echo e(Auth::user()->name); ?></span>
                                <span class="d-block small opacity-60"><?php echo e(Auth::user()->user_type); ?></span>
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated dropdown-menu-md">
                        <a href="<?php echo e(route('profile.index')); ?>" class="dropdown-item">
                            <i class="las la-user-circle"></i>
                            <span><?php echo e(translate('Profile')); ?></span>
                        </a>

                        <a href="<?php echo e(route('logout')); ?>" class="dropdown-item">
                            <i class="las la-sign-out-alt"></i>
                            <span><?php echo e(translate('Logout')); ?></span>
                        </a>
                    </div>
                </div>
            </div><!-- .aiz-topbar-item -->
        </div>
    </div>
</div><!-- .aiz-topbar -->
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/inc/admin_nav.blade.php ENDPATH**/ ?>