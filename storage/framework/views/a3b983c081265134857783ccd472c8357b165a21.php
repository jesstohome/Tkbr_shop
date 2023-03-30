<?php $__env->startSection('panel_content'); ?>
<section class="py-8 bg-soft-primary">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto text-center">
                <h1 class="mb-0 fw-700"><?php echo e(translate('Premium Packages for Sellers')); ?></h1>
            </div>
        </div>
    </div>
</section>

<section class="py-4 py-lg-5">
    <div class="container">
        <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 gutters-10 justify-content-center">
            <?php $__currentLoopData = $seller_spread_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller_spread_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="text-center mb-4 mt-3">
                                <img class="mw-100 mx-auto mb-4" src="<?php echo e(uploaded_asset($seller_spread_package->logo)); ?>" height="100">
                                <h5 class="mb-3 h5 fw-600"><?php echo e($seller_spread_package->getTranslation('name')); ?></h5>
                            </div>
                            <ul class="list-group list-group-raw fs-15 mb-5">
                                <li class="list-group-item py-2">
                                    <i class="las la-check text-success mr-2"></i>
                                    <?php echo e($seller_spread_package->product_upload_limit); ?> <?php echo e(translate('Product Spread Limit')); ?>

                                </li>
                                <li class="list-group-item py-2">
                                    <i class="las la-check text-success mr-2"></i>
                                    <?php echo e($seller_spread_package->getTranslation('max_profit')); ?>

                                </li>
                            </ul>
                            <div class="mb-5 d-flex align-items-center justify-content-center">
                                <?php if($seller_spread_package->amount == 0): ?>
                                    <span class="display-4 fw-600 lh-1 mb-0"><?php echo e(translate('Free')); ?></span>
                                <?php else: ?>
                                    <span class="display-4 fw-600 lh-1 mb-0"><?php echo e(single_price($seller_spread_package->amount)); ?></span>
                                <?php endif; ?>
                                <span class="text-secondary border-left ml-2 pl-2"><?php echo e($seller_spread_package->duration); ?><br><?php echo e(translate('Days')); ?></span>
                            </div>

                            <div class="text-center">
                                <?php if($seller_spread_package->amount == 0): ?>
                                    <button class="btn btn-primary fw-600" onclick="get_free_package(<?php echo e($seller_spread_package->id); ?>)"><?php echo e(translate('Free Package')); ?></button>
                                <?php else: ?>
                                    <?php if(addon_is_activated('offline_payment')): ?>
                                        <button class="btn btn-primary fw-600" onclick="select_payment_type(<?php echo e($seller_spread_package->id); ?>)"><?php echo e(translate('Purchase Package')); ?></button>
                                    <?php else: ?>
                                        <button class="btn btn-primary fw-600" onclick="show_price_modal(<?php echo e($seller_spread_package->id); ?>)"><?php echo e(translate('Purchase Package')); ?></button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>


<!-- Payment For Storehouse Modal -->
    <div class="modal fade" id="payment_for_storehouse_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('Payment For Storehouse')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" value="<?php echo e(encrypt($order->id)); ?>">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-center"><?php echo e(translate('Pay with wallet')); ?>


                            <?php

                            $user = \App\Models\User::find(Auth::user()->id);
                            ?>
                            <?php echo e(single_price($user->balance)); ?></h5>
                        </div>
                    </div>
                    <div class="row">

                                <div class="col-md-9">
                                    <input type="password" lang="en" class="form-control mb-3" id="tpwd" name="tpwd"
                                   placeholder="<?php echo e(translate('Transaction password')); ?>" max=6 required>
                                </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-light transition-3d-hover mr-3" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                        <button id="payment_button" type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1"><?php echo e(translate('Payment')); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>


  <!-- Select Payment Type Modal -->
  <div class="modal fade" id="select_payment_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('Select Payment Type')); ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" id="package_id" name="package_id" value="">
                  <div class="row">
                      <div class="col-md-3">
                          <label><?php echo e(translate('Payment Type')); ?></label>
                      </div>
                      <div class="col-md-9">
                          <div class="mb-3">
                              <select class="form-control aiz-selectpicker" onchange="payment_type(this.value)"
                                      data-minimum-results-for-search="Infinity">
                                  <option value=""><?php echo e(translate('Select One')); ?></option>
                                  <option value="cash"><?php echo e(translate('Wallet')); ?></option>
                                  <option value="online"><?php echo e(translate('Online payment')); ?></option>
                                  <option value="offline"><?php echo e(translate('Offline payment')); ?></option>

                              </select>
                          </div>
                      </div>

                  </div>
                  <div class="form-group text-right">
                      <button type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1" id="select_type_cancel" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Online payment Modal-->
  <div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('Purchase Your Package')); ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
              </div>
              <form class="" id="package_payment_form" action="<?php echo e(route('seller_spread_packages.purchase')); ?>" method="post">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="seller_spread_package_id" value="">
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-md-2">
                              <label><?php echo e(translate('Payment Method')); ?></label>
                          </div>
                          <div class="col-md-10">
								<div class="mb-3">
									<select class="form-control aiz-selectpicker" data-live-search="true" name="payment_option">
										<?php if(get_setting('paypal_payment') == 1): ?>
											<option value="paypal"><?php echo e(translate('Paypal')); ?></option>
										<?php endif; ?>
										<?php if(get_setting('stripe_payment') == 1): ?>
											<option value="stripe"><?php echo e(translate('Stripe')); ?></option>
										<?php endif; ?>
										<?php if(get_setting('mercadopago_payment') == 1): ?>
                                            <option value="mercadopago"><?php echo e(translate('Mercadopago')); ?></option>
                                            <option value="paypal"><?php echo e(translate('Paypal')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('toyyibpay_payment') == 1): ?>
                                            <option value="toyyibpay"><?php echo e(translate('ToyyibPay')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('sslcommerz_payment') == 1): ?>
                                            <option value="sslcommerz"><?php echo e(translate('sslcommerz')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('instamojo_payment') == 1): ?>
                                            <option value="instamojo"><?php echo e(translate('Instamojo')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('razorpay') == 1): ?>
                                            <option value="razorpay"><?php echo e(translate('RazorPay')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('paystack') == 1): ?>
                                            <option value="paystack"><?php echo e(translate('PayStack')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('payhere') == 1): ?>
                                            <option value="payhere"><?php echo e(translate('Payhere')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('ngenius') == 1): ?>
                                            <option value="ngenius"><?php echo e(translate('Ngenius')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('iyzico') == 1): ?>
                                            <option value="iyzico"><?php echo e(translate('Iyzico')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('nagad') == 1): ?>
                                            <option value="nagad"><?php echo e(translate('Nagad')); ?></option>
                                        <?php endif; ?>
                                        <?php if(get_setting('bkash') == 1): ?>
                                            <option value="bkash"><?php echo e(translate('Bkash')); ?></option>
                                        <?php endif; ?>
										<?php if(get_setting('aamarpay') == 1): ?>
                                            <option value="aamarpay"><?php echo e(translate('Amarpay')); ?></option>
                                        <?php endif; ?>
                                        <?php if(addon_is_activated('african_pg')): ?>
                                            <?php if(get_setting('mpesa') == 1): ?>
                                                <option value="mpesa"><?php echo e(translate('Mpesa')); ?></option>
                                            <?php endif; ?>
                                            <?php if(get_setting('flutterwave') == 1): ?>
                                                <option value="flutterwave"><?php echo e(translate('Flutterwave')); ?></option>
                                            <?php endif; ?>
                                            <?php if(get_setting('payfast') == 1): ?>
                                              <option value="payfast"><?php echo e(translate('PayFast')); ?></option>
                                            <?php endif; ?>
                                        <?php endif; ?>

									</select>
								</div>
                          </div>
                      </div>
                      <div class="form-group text-right">
                          <button type="button" class="btn btn-sm btn-secondary transition-3d-hover mr-1" data-dismiss="modal"><?php echo e(translate('cancel')); ?></button>
                          <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1"><?php echo e(translate('Confirm')); ?></button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
  </div>

  <!-- offline payment Modal -->
  <div class="modal fade" id="offline_seller_spread_package_purchase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title strong-600 heading-5"><?php echo e(translate('Offline Package Payment')); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="offline_seller_spread_package_purchase_modal_body"></div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="/public/assets/js/md5.min.js"></script>
    <script type="text/javascript">

        function select_payment_type(id){
            $('input[name=package_id]').val(id);
            $('#select_payment_type_modal').modal('show');
        }

        function payment_type(type){
            var package_id = $('#package_id').val();
            if(type == 'online'){
                $("#select_type_cancel").click();
                show_price_modal(package_id);
            }

             else if(type == 'cash'){
                $("#select_type_cancel").click();
                $('#payment_for_storehouse_modal').modal('show');
            }

            else if (type == 'offline'){
                $("#select_type_cancel").click();
                $.post('<?php echo e(route('seller.offline_seller_spread_package_purchase_modal')); ?>', {_token:'<?php echo e(csrf_token()); ?>', package_id:package_id}, function(data){
                    $('#offline_seller_spread_package_purchase_modal_body').html(data);
                    $('#offline_seller_spread_package_purchase_modal').modal('show');
                });
            }
        }

        function show_price_modal(id){
            $('input[name=seller_spread_package_id]').val(id);
            $('#price_modal').modal('show');
        }

        function get_free_package(id){
            $('input[name=seller_spread_package_id]').val(id);
            $('#package_payment_form').submit();
        }


            $('#payment_button').on('click', function () {
            <?php
            $tpwd =  Auth::user()->tpwd;
            ?>
            var tpwd = '<?php echo $tpwd; ?>'
            var pwd = $("#tpwd").val();
            if (md5(pwd) != tpwd) {
                AIZ.plugins.notify('danger',
                        '<?php echo e(translate('password error')); ?>');
                return;
            }

            $.post('<?php echo e(route('orders.buy_spread_cash')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                package_id: $('#package_id').val()
            }, function (data) {
                console.log(data)
                if (data.success == 1) {
                    $('#order_details').modal('hide');
                    AIZ.plugins.notify('success',  data.message );
                    location.reload().setTimeOut(500);
                } else {
                    AIZ.plugins.notify('danger', data.message ? data.message : '出问题了！');
                }
            });
        })


    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller_spread_packages/frontend/seller_spread_packages_list.blade.php ENDPATH**/ ?>