

<?php $__env->startSection('content'); ?>

    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block"><?php echo e(translate('1. My Cart')); ?></h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50"><?php echo e(translate('2. Shipping info')); ?>

                                </h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-truck"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50"><?php echo e(translate('3. Delivery info')); ?>

                                </h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50"><?php echo e(translate('4. Payment')); ?></h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50"><?php echo e(translate('5. Confirmation')); ?>

                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4" id="cart-summary">
        <div class="container">
            <?php if($carts && count($carts) > 0): ?>
                <div class="row">
                    <div class="col-xxl-8 col-xl-10 mx-auto">
                        <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                            <div class="mb-4">
                                <div class="row gutters-5 d-none d-lg-flex border-bottom mb-3 pb-3">
                                    <div class="col-md-5 fw-600"><?php echo e(translate('Product')); ?></div>
                                    <div class="col fw-600"><?php echo e(translate('Price')); ?></div>
                                    <div class="col fw-600"><?php echo e(translate('Tax')); ?></div>
                                    <div class="col fw-600"><?php echo e(translate('Quantity')); ?></div>
                                    <div class="col fw-600"><?php echo e(translate('Total')); ?></div>
                                    <div class="col-auto fw-600"><?php echo e(translate('Remove')); ?></div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <?php
                                        $total = 0;
                                    ?>
                                    <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $product = \App\Models\Product::find($cartItem['product_id']);
                                            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                                            // $total = $total + ($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity'];
                                            $total = $total + cart_product_price($cartItem, $product, false) * $cartItem['quantity'];
                                            $product_name_with_choice = $product->getTranslation('name');
                                            if ($cartItem['variation'] != null) {
                                                $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variation'];
                                            }
                                        ?>
                                        <li class="list-group-item px-0 px-lg-3">
                                            <div class="row gutters-5">
                                                <div class="col-lg-5 d-flex">
                                                    <span class="mr-2 ml-0">
                                                        <img src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>"
                                                            class="img-fit size-60px rounded"
                                                            alt="<?php echo e($product->getTranslation('name')); ?>">
                                                    </span>
                                                    <span class="fs-14 opacity-60"><?php echo e($product_name_with_choice); ?></span>
                                                </div>

                                                <div class="col-lg col-4 order-1 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none"><?php echo e(translate('Price')); ?></span>
                                                    <span
                                                        class="fw-600 fs-16"><?php echo e(cart_product_price($cartItem, $product, true, false)); ?></span>
                                                </div>
                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none"><?php echo e(translate('Tax')); ?></span>
                                                    <span
                                                        class="fw-600 fs-16"><?php echo e(cart_product_tax($cartItem, $product)); ?></span>
                                                </div>

                                                <div class="col-lg col-6 order-4 order-lg-0">
                                                    <?php if($cartItem['digital'] != 1 && $product->auction_product == 0): ?>
                                                        <div
                                                            class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="minus"
                                                                data-field="quantity[<?php echo e($cartItem['id']); ?>]">
                                                                <i class="las la-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity[<?php echo e($cartItem['id']); ?>]"
                                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                                placeholder="1" value="<?php echo e($cartItem['quantity']); ?>"
                                                                min="<?php echo e($product->min_qty); ?>"
                                                                max="<?php echo e($product_stock->qty); ?>"
                                                                onchange="updateQuantity(<?php echo e($cartItem['id']); ?>, this)">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="plus"
                                                                data-field="quantity[<?php echo e($cartItem['id']); ?>]">
                                                                <i class="las la-plus"></i>
                                                            </button>
                                                        </div>
                                                    <?php elseif($product->auction_product == 1): ?>
                                                        <span class="fw-600 fs-16">1</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none"><?php echo e(translate('Total')); ?></span>
                                                    <span
                                                        class="fw-600 fs-16 text-primary"><?php echo e(single_price(cart_product_price($cartItem, $product, false) * $cartItem['quantity'])); ?></span>
                                                </div>
                                                <div class="col-lg-auto col-6 order-5 order-lg-0 text-right">
                                                    <a href="javascript:void(0)"
                                                        onclick="removeFromCartView(event, <?php echo e($cartItem['id']); ?>)"
                                                        class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>

                            <div class="px-3 py-2 mb-4 border-top d-flex justify-content-between">
                                <span class="opacity-60 fs-15"><?php echo e(translate('Subtotal')); ?></span>
                                <span class="fw-600 fs-17"><?php echo e(single_price($total)); ?></span>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                    <a href="<?php echo e(route('home')); ?>" class="btn btn-link">
                                        <i class="las la-arrow-left"></i>
                                        <?php echo e(translate('Return to shop')); ?>

                                    </a>
                                </div>
                                <div class="col-md-6 text-center text-md-right">
                                    <?php if(Auth::check()): ?>
                                        <a href="<?php echo e(route('checkout.shipping_info')); ?>" class="btn btn-primary fw-600">
                                            <?php echo e(translate('Continue to Shipping')); ?>

                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-primary fw-600"
                                            onclick="showCheckoutModal()"><?php echo e(translate('Continue to Shipping')); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="shadow-sm bg-white p-4 rounded">
                            <div class="text-center p-3">
                                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                <h3 class="h4 fw-700"><?php echo e(translate('Your Cart is empty')); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" id="login-modal">
        <div class="modal-dialog modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600"><?php echo e(translate('Login')); ?></h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="<?php echo e(route('cart.login.submit')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php if(addon_is_activated('otp_system') && env('DEMO_MODE') != 'On'): ?>
                                <div class="form-group phone-form-group mb-1">
                                    <input type="tel" id="phone-code"
                                        class="form-control<?php echo e($errors->has('phone') ? ' is-invalid' : ''); ?>"
                                        value="<?php echo e(old('phone')); ?>" placeholder="" name="phone" autocomplete="off">
                                </div>

                                <input type="hidden" name="country_code" value="">

                                <div class="form-group email-form-group mb-1 d-none">
                                    <input type="email"
                                        class="form-control <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>"
                                        value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email"
                                        id="email" autocomplete="off">
                                    <?php if($errors->has('email')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group text-right">
                                    <button class="btn btn-link p-0 opacity-50 text-reset" type="button"
                                        onclick="toggleEmailPhone(this)"><?php echo e(translate('Use Email Instead')); ?></button>
                                </div>
                            <?php else: ?>
                                <div class="form-group">
                                    <input type="email"
                                        class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>"
                                        value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email"
                                        id="email" autocomplete="off">
                                    <?php if($errors->has('email')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <input type="password"
                                    class="form-control <?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>"
                                    placeholder="<?php echo e(translate('Password')); ?>" name="password" id="password">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                        <span class=opacity-60><?php echo e(translate('Remember Me')); ?></span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="<?php echo e(route('password.request')); ?>"
                                        class="text-reset opacity-60 fs-14"><?php echo e(translate('Forgot password?')); ?></a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit"
                                    class="btn btn-primary btn-block fw-600"><?php echo e(translate('Login')); ?></button>
                            </div>
                        </form>

                    </div>
                    <div class="text-center mb-3">
                        <p class="text-muted mb-0"><?php echo e(translate('Dont have an account?')); ?></p>
                        <a href="<?php echo e(route('user.registration')); ?>"><?php echo e(translate('Register Now')); ?></a>
                    </div>
                    <?php if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1): ?>
                        <div class="separator mb-3">
                            <span class="bg-white px-3 opacity-60"><?php echo e(translate('Or Login With')); ?></span>
                        </div>
                        <ul class="list-inline social colored text-center mb-3">
                            <?php if(get_setting('facebook_login') == 1): ?>
                                <li class="list-inline-item">
                                    <a href="<?php echo e(route('social.login', ['provider' => 'facebook'])); ?>"
                                        class="facebook">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(get_setting('google_login') == 1): ?>
                                <li class="list-inline-item">
                                    <a href="<?php echo e(route('social.login', ['provider' => 'google'])); ?>"
                                        class="google">
                                        <i class="lab la-google"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(get_setting('twitter_login') == 1): ?>
                                <li class="list-inline-item">
                                    <a href="<?php echo e(route('social.login', ['provider' => 'twitter'])); ?>"
                                        class="twitter">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromCart(key);
        }

        function updateQuantity(key, element) {
            $.post('<?php echo e(route('cart.updateQuantity')); ?>', {
                _token: AIZ.data.csrf,
                id: key,
                quantity: element.value
            }, function(data) {
                updateNavCart(data.nav_cart_view, data.cart_count);
                $('#cart-summary').html(data.cart_view);
            });
        }

        function showCheckoutModal() {
            $('#login-modal').modal();
        }

        // Country Code
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "<?php echo e(static_asset('assets/js/intlTelutils.js')); ?>?1590403638580",
            onlyCountries: <?php echo json_encode(\App\Models\Country::where('status', 1)); ?>,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                $('input[name=phone]').val(null);
                isPhoneShown = false;
                $(el).html('<?php echo e(translate('Use Phone Instead')); ?>');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                $('input[name=email]').val(null);
                isPhoneShown = true;
                $(el).html('<?php echo e(translate('Use Email Instead')); ?>');
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/view_cart.blade.php ENDPATH**/ ?>