

<?php $__env->startSection('content'); ?>
    <section class="gry-bg py-4">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    <?php echo e(translate('Create an account.')); ?>

                                </h1>
                            </div>
                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form id="reg-form" class="form-default" role="form" action="<?php echo e(route('register')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group">
                                            <input type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(translate('Full Name')); ?>" name="name">
                                            <?php if($errors->has('name')): ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($errors->first('name')); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if(addon_is_activated('otp_system')): ?>
                                            <div class="form-group phone-form-group mb-1">
                                                <input type="tel" id="phone-code" class="form-control<?php echo e($errors->has('phone') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('phone')); ?>" placeholder="" name="phone" autocomplete="off">
                                            </div>

                                            <input type="hidden" name="country_code" value="">

                                            <div class="form-group email-form-group mb-1 d-none">
                                                <input type="email" class="form-control <?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email"  autocomplete="off">
                                                <?php if($errors->has('email')): ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group text-right">
                                                <button class="btn btn-link p-0 opacity-50 text-reset" type="button" onclick="toggleEmailPhone(this)"><?php echo e(translate('Use Email Instead')); ?></button>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-group">
                                                <input type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(translate('Email')); ?>" name="email">
                                                <?php if($errors->has('email')): ?>
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <input type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="<?php echo e(translate('Password')); ?>" name="password">
                                            <?php if($errors->has('password')): ?>
                                                <span class="invalid-feedback" role="alert">
                                                    <strong><?php echo e($errors->first('password')); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="<?php echo e(translate('Confirm Password')); ?>" name="password_confirmation">
                                        </div>

                                        <?php if(get_setting('google_recaptcha') == 1): ?>
                                            <div class="form-group">
                                                <div class="g-recaptcha" data-sitekey="<?php echo e(env('CAPTCHA_KEY')); ?>"></div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" name="checkbox_example_1" required>
                                                <span class=opacity-60><?php echo e(translate('By signing up you agree to our terms and conditions.')); ?></span>
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>

                                        <div class="mb-5">
                                            <button type="submit" class="btn btn-primary btn-block fw-600"><?php echo e(translate('Create Account')); ?></button>
                                        </div>
                                    </form>
                                    <?php if(get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1): ?>
                                        <div class="separator mb-3">
                                            <span class="bg-white px-3 opacity-60"><?php echo e(translate('Or Join With')); ?></span>
                                        </div>
                                        <ul class="list-inline social colored text-center mb-5">
                                            <?php if(get_setting('facebook_login') == 1): ?>
                                                <li class="list-inline-item">
                                                    <a href="<?php echo e(route('social.login', ['provider' => 'facebook'])); ?>" class="facebook">
                                                        <i class="lab la-facebook-f"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if(get_setting('google_login') == 1): ?>
                                                <li class="list-inline-item">
                                                    <a href="<?php echo e(route('social.login', ['provider' => 'google'])); ?>" class="google">
                                                        <i class="lab la-google"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if(get_setting('twitter_login') == 1): ?>
                                                <li class="list-inline-item">
                                                    <a href="<?php echo e(route('social.login', ['provider' => 'twitter'])); ?>" class="twitter">
                                                        <i class="lab la-twitter"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                <div class="text-center">
                                    <p class="text-muted mb-0"><?php echo e(translate('Already have an account?')); ?></p>
                                    <a href="<?php echo e(route('user.login')); ?>"><?php echo e(translate('Log In')); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <?php if(get_setting('google_recaptcha') == 1): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>

    <script type="text/javascript">

        <?php if(get_setting('google_recaptcha') == 1): ?>
        // making the CAPTCHA  a required field for form submission
        $(document).ready(function(){
            // alert('helloman');
            $("#reg-form").on("submit", function(evt)
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
        <?php endif; ?>

        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if(country.iso2 == 'bd'){
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "<?php echo e(static_asset('assets/js/intlTelutils.js')); ?>?1590403638580",
            onlyCountries: <?php echo json_encode(\App\Models\Country::where('status', 1)->pluck('code')->toArray()) ?>,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if(selectedCountryData.iso2 == 'bd'){
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

        function toggleEmailPhone(el){
            if(isPhoneShown){
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                isPhoneShown = false;
                $(el).html('<?php echo e(translate('Use Phone Instead')); ?>');
            }
            else{
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                isPhoneShown = true;
                $(el).html('<?php echo e(translate('Use Email Instead')); ?>');
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/user_registration.blade.php ENDPATH**/ ?>