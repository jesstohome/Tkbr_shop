<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-avatar_original-wrap">
            <div class="d-block text-center my-3">
                <?php if(Auth::user()->avatar_original != null): ?>
                    <img class="mw-100 mb-3" src="<?php echo e(uploaded_asset(Auth::user()->avatar_original)); ?>" onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';">
                <?php else: ?>
                    <img class="mw-100 mb-3" src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" class="image rounded-circle" onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';">
                <?php endif; ?>
                <h3 class="fs-16  m-0 text-primary"><?php echo e(Auth::user()->name); ?></h3>
                <p class="text-primary"><?php echo e(Auth::user()->email); ?></p>
            </div>
        </div>
        <div class="aiz-side-nav-wrap">
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('salesman.sellers_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.sellers_index', 'shops.create'])); ?>">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Seller')); ?></span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('salesman.orders.index')); ?>"
                        class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.orders.index', 'salesman.orders.show'])); ?>">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Orders')); ?></span> </a>
                </li>
                <!-- POS Addon-->
                <?php if(addon_is_activated('pos_system')): ?>
                    <?php if(get_setting('pos_activation_for_seller') != null && get_setting('pos_activation_for_seller') != 0): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-tasks aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('POS System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('salesman.poin-of-sales.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.poin-of-sales.index', 'salesman.poin-of-sales.create'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('POS Manager')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


            <!-- Offline Payment Addon-->
                <?php if(addon_is_activated('offline_payment')): ?>
                    <?php if(Auth::user()->user_type == 'salesman'): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-money-check-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offline Payment System')); ?></span>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">





                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('salesman.offline_wallet_recharge_request.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.offline_wallet_recharge_request.index'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Offline Wallet Recharge')); ?></span>
                                    </a>
                                </li>
                                <?php if(addon_is_activated('seller_subscription')): ?>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('salesman.offline_seller_package_payment_request.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.offline_seller_package_payment_request.index'])); ?>">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Offline Seller Package Payments')); ?></span>
                                            <?php if(env("DEMO_MODE") == "On"): ?>
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('salesman.customers.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.customers.index', 'salesman.customers.create'])); ?>">
                        <i class="las la-user-friends aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Customers')); ?></span>
                    </a>
                </li>


                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('salesman.withdraw_requests_all')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['salesman.sellers.payment_histories', 'salesman.withdraw_requests_all'])); ?>">
                        <i class="las la-user-friends aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Payout Requests')); ?></span>
                    </a>
                </li>

            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
<script type="text/javascript">

    function getConversations(){
        $.ajax({
            type:"get",
            url:'<?php echo e(route('seller.conversations.message_count')); ?>',
            success: function(data){
                if(data.result > 0){
                    $('#conversations').show();
                }else{
                    $('#conversations').hide();
                }
            }
        });
    }
    // setInterval(function (){
    //     getConversations()
    // },1000)

</script>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/salesman/inc/salesman_sidenav.blade.php ENDPATH**/ ?>