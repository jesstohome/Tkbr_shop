<div class="modal-body">

  <div class="text-center">
      <span class="avatar avatar-xxl mb-3">
          <img src="<?php echo e(uploaded_asset($shop->user->avatar_original)); ?>" onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';">
      </span>
      <h1 class="h5 mb-1"><?php echo e($shop->user->name); ?></h1>
      <p class="text-sm text-muted"><?php echo e($shop->name); ?></p>

      <div class="pad-ver btn-groups">
          <a href="<?php echo e($shop->facebook); ?>" class="btn btn-icon demo-pli-facebook icon-lg add-tooltip" data-original-title="Facebook" data-container="body"></a>
          <a href="<?php echo e($shop->twitter); ?>" class="btn btn-icon demo-pli-twitter icon-lg add-tooltip" data-original-title="Twitter" data-container="body"></a>
          <a href="<?php echo e($shop->google); ?>" class="btn btn-icon demo-pli-google-plus icon-lg add-tooltip" data-original-title="Google+" data-container="body"></a>
      </div>
  </div>
  <hr>

  <!-- Profile Details -->
  <h6 class="mb-4"><?php echo e(translate('About')); ?> <?php echo e($shop->user->name); ?></h6>
  <p><i class="demo-pli-map-marker-2 icon-lg icon-fw mr-1"></i><?php echo e($shop->address); ?></p>
  <p><a href="<?php echo e(route('shop.visit', $shop->slug)); ?>" class="btn-link"><i class="demo-pli-internet icon-lg icon-fw mr-1"></i><?php echo e($shop->name); ?></a></p>
  <p><i class="demo-pli-old-telephone icon-lg icon-fw mr-1"></i><?php echo e($shop->user->phone); ?></p>

  <h6 class="mb-4"><?php echo e(translate('Payout Info')); ?></h6>
  <p><?php echo e(translate('Bank Name')); ?> : <?php echo e($shop->bank_name); ?></p>
  <p><?php echo e(translate('Bank Acc Name')); ?> : <?php echo e($shop->bank_acc_name); ?></p>
  <p><?php echo e(translate('Bank Acc Number')); ?> : <?php echo e($shop->bank_acc_no); ?></p>
  <p><?php echo e(translate('Bank Routing Number')); ?> : <?php echo e($shop->bank_routing_no); ?></p>

  <br>

  <div class="table-responsive">
      <table class="table table-striped mar-no">
          <tbody>
          <tr>
              <td><?php echo e(translate('Total Products')); ?></td>
              <td><?php echo e(App\Models\Product::where('user_id', $shop->user->id)->get()->count()); ?></td>
          </tr>
          <tr>
              <td><?php echo e(translate('Total Orders')); ?></td>
              <td><?php echo e(App\Models\OrderDetail::where('seller_id', $shop->user->id)->get()->count()); ?></td>
          </tr>
          <tr>
              <td><?php echo e(translate('Total Sold Amount')); ?></td>
              <?php
                  $orderDetails = \App\Models\OrderDetail::where('seller_id', $shop->user->id)->get();
                  $total = 0;
                  foreach ($orderDetails as $key => $orderDetail) {
                      if($orderDetail->order != null && $orderDetail->order->payment_status == 'paid'){
                          $total += $orderDetail->price;
                      }
                  }
              ?>
              <td><?php echo e(single_price($total)); ?></td>
          </tr>
          <tr>
              <td><?php echo e(translate('Wallet Balance')); ?></td>
              <td><?php echo e(single_price($shop->user->balance)); ?></td>
          </tr>
          <tr>
              <td style="line-height: 120px"><?php echo e(translate('Identity Card Front')); ?></td>
              <td>
                  <img height="120" src="<?php echo e(uploaded_asset($shop->user->identity_card_front)); ?>">
              </td>
          </tr>
          <tr>
              <td style="line-height: 120px"><?php echo e(translate('Identity Card Back')); ?></td>
              <td>
                  <img height="120" src="<?php echo e(uploaded_asset($shop->user->identity_card_back)); ?>">
              </td>
          </tr>
          </tbody>
      </table>
  </div>
</div>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sellers/profile_modal.blade.php ENDPATH**/ ?>