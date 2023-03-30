<?php $__env->startSection('panel_content'); ?>

    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3"><?php echo e(translate('Money Withdraw')); ?></h1>
            </div>
        </div>
    </div>

    <div class="row gutters-0">
        <div class="col-md-2 mb-3 mx-auto">
            <div class="bg-grad-3 text-white rounded-lg overflow-hidden">
              <span
                  class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                  <i class="las la-dollar-sign la-2x text-black-50"></i>
              </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center"><?php echo e(single_price(Auth::user()->shop->admin_to_pay)); ?></div>
                    <div class="opacity-50 text-center"><?php echo e(translate('Pending Balance')); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3 mx-auto">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
              <span
                  class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                  <i class="las la-dollar-sign la-2x  text-black-50"></i>
              </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center"><?php echo e(single_price(Auth::user()->balance)); ?></div>
                    <div class="opacity-50 text-center"><?php echo e(translate('Wallet Money')); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3 mx-auto">
            <div
                class="bg-grad-2 p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                onclick="show_request_modal()">
              <span
                  class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                  <i class="las la-plus la-3x text-white"></i>
              </span>
                <div class="fs-18 text-white"><?php echo e(translate('Send Withdraw Request')); ?></div>
            </div>
        </div>
        <?php if(addon_is_activated('offline_payment')): ?>
            <div class="col-md-2 mb-3 mr-auto">
                <div
                    class="bg-grad-4 p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                    onclick="show_make_wallet_recharge_modal(1)">
              <span
                  class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                  <i class="las la-plus la-3x text-white"></i>
              </span>
                    <div class="fs-18 text-white"><?php echo e(translate('Offline Recharge Wallet')); ?></div>
                </div>
            </div>
            
              <div class="col-md-2 mb-3 mr-auto">
                <div
                    class="bg-grad-4 p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                    onclick="show_make_wallet_recharge_modal(2)">
              <span
                  class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                  <i class="las la-plus la-3x text-white"></i>
              </span>
                    <div class="fs-18 text-white"><?php echo e(translate('Guarantee Recharge')); ?></div>
                </div>
            </div>
            
            
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Withdraw Request history')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Date')); ?></th>
                    <th><?php echo e(translate('Amount')); ?></th>
                    <th><?php echo e(translate('Type')); ?></th>
                    
                    <th data-breakpoints="lg"><?php echo e(translate('Status')); ?></th>
                    <th><?php echo e(translate('Withdraw Type')); ?></th>
                    <th><?php echo e(translate('Remarks')); ?></th>
                    <th data-breakpoints="lg" width="40%"><?php echo e(translate('Message')); ?></th>
                    
                    
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $seller_withdraw_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller_withdraw_request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($seller_withdraw_request->created_at))); ?></td>
                        <td><?php echo e(single_price($seller_withdraw_request->amount)); ?></td>
                        <td>
                            <?php if( $seller_withdraw_request->type == 1): ?>
                            
                            <?php echo e(translate('User Balance')); ?>

                            <?php else: ?>
                            
                              <?php echo e(translate('Guarantee')); ?>

                            <?php endif; ?>
                        </td>
                        <td> 
                            <?php if($seller_withdraw_request->status == 1): ?>
                                <span class=" badge badge-inline badge-success"><?php echo e(translate('Paid')); ?></span>
                             <?php elseif($seller_withdraw_request->status == 2): ?>
                                <span class=" badge badge-inline badge-danger"><?php echo e(translate('Refuse')); ?> </span>
                            <?php else: ?>
                                <span class=" badge badge-inline badge-info"><?php echo e(translate('Pending')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            
                            <?php if( $seller_withdraw_request->w_type == 1): ?>
                            
                            <?php echo e(translate('Cash')); ?>

                            <?php elseif( $seller_withdraw_request->w_type == 2): ?>
                            
                              <?php echo e(translate('Bank')); ?>

                            <?php elseif( $seller_withdraw_request->w_type == 3): ?>
                            <?php echo e(translate('USDT')); ?>

                            <?php endif; ?>
                        </td>
                             <td>
                            <?php echo e($seller_withdraw_request->remarks); ?>

                        </td>
                        <td>
                            <?php echo e($seller_withdraw_request->message); ?>

                        </td>
                   
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($seller_withdraw_requests->links()); ?>

            </div>
        </div>
    </div>

    <!-- 待解冻订单 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Froze Order')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo e(translate('Order Code')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Amount')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Profit')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Payment Status')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Pick Up Status')); ?></th>
                    <th><?php echo e(translate('Date')); ?></th>
                    <th><?php echo e(translate('Unfreeze Countdown')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $freezeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e($order->code); ?></td>
                        <td><?php echo e(single_price($order->grand_total)); ?></td>
                        <td><?php echo e(single_price($order->grand_total - $order->product_storehouse_total)); ?></td>
                        <td>
                            <?php if($order->payment_status == 'paid'): ?>
                                <span class="badge badge-inline badge-success"><?php echo e(translate('Paid')); ?></span>
                            <?php else: ?>
                                <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpaid')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($order->product_storehouse_status): ?>
                                <span class="badge badge-inline badge-success"><?php echo e(translate('Picked Up')); ?></span>
                            <?php else: ?>
                                <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpicked Up')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(date('d-m-Y', strtotime($order->created_at))); ?></td>
                        <td>
                            <?php if($order->freeze_expired_at): ?>
                                <?php echo e(round(($order->freeze_expired_at - now()->timestamp) / 86400)); ?> <?php echo e(translate('Days')); ?>

                            <?php else: ?>
                                <?php echo e(translate('Unpicked Up')); ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($freezeOrders->links()); ?>

            </div>
        </div>
    </div>

    <!-- 充值记录 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Wallet Recharge History')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md"><?php echo e(translate('Amount')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Payment method')); ?></th>
                    <th><?php echo e(translate('Payment Details')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Approval')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Offline payment')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Type')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Receipt')); ?></th>
                    <th><?php echo e(translate('Date')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $rechargeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e(single_price($list->amount)); ?></td>
                        <td><?php echo e($list->payment_method); ?></td>
                        <td><?php echo e($list->payment_details); ?></td>
                        <td>
                            <?php if($list->approval == 1): ?>
                                <span class="badge badge-inline badge-success"><?php echo e(translate('yes')); ?></span>
                            <?php else: ?>
                                <span class="badge badge-inline badge-danger"><?php echo e(translate('No')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($list->offline_payment == 1): ?>
                                <span class="badge badge-inline badge-success"><?php echo e(translate('yes')); ?></span>
                            <?php else: ?>
                                <span class="badge badge-inline badge-danger"><?php echo e(translate('No')); ?></span>
                            <?php endif; ?>
                        </td>
                        
                         <td>
                            <?php if( $list->type == 1): ?>
                            
                            <?php echo e(translate('User Balance')); ?>

                            <?php else: ?>
                            
                              <?php echo e(translate('Guarantee')); ?>

                            <?php endif; ?>
                        </td>
                        
                        
                        <td><?php echo e($list->reciept); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($list->created_at))); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($rechargeList->links()); ?>

            </div>
        </div>
    </div>
    
    
    
    
      <!-- 充值记录 -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Payment History')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th data-breakpoints="md"><?php echo e(translate('Amount')); ?></th>
                
                    <th><?php echo e(translate('Payment Details')); ?></th>
         
                    <th data-breakpoints="md"><?php echo e(translate('Payment method')); ?></th>
                    
                  
                    <th><?php echo e(translate('Date')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $paymentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($key+1); ?></td>
                        <td><?php echo e(single_price($list->amount)); ?></td>
                    
                        <td><?php echo e($list->payment_details); ?></td>
                        
                        <td>
                            <?php echo e(translate($list->payment_method)); ?>

                        </td>
                        
                      
                        
                        
                      
                        <td><?php echo e(date('d-m-Y', strtotime($list->created_at))); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($paymentList->links()); ?>

            </div>
        </div>
    </div>
    
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <!-- offline payment Modal -->
    <div class="modal fade" id="offline_wallet_recharge_modal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <?php echo e(translate('Offline Recharge Wallet')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="offline_wallet_recharge_modal_body"></div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('Send A Withdraw Request')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php if($balance > 5): ?>
                    <form class="" action="<?php echo e(route('seller.money_withdraw_request.store')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body gry-bg px-3 pt-3">
                            <div class="row">
                                <div class="col">
                                    <div class="alert alert-success" role="alert">
                                        <h6><?php echo e(translate('Your wallet balance :')); ?> $<?php echo e($balance); ?></h6>
                                    </div>
                                    
                                    <div class="alert alert-success" role="alert">
                                        <h6><?php echo e(translate('Your guarantee balance :')); ?> 
                                        $<?php echo e(Auth::user()->shop->bzj_money); ?> </h6>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label><?php echo e(translate('Amount')); ?> <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-9">
                                    <input type="number" lang="en" class="form-control mb-3" name="amount"
                                         
                                           placeholder="<?php echo e(translate('Amount')); ?>" required>
                                </div>
                            </div>
                             <div class="row" style="margin-bottom:5px;">
                                
                                 <div class="col-md-3">
                                    <label><?php echo e(translate('Opera Type')); ?></label>
                                </div>
                                 <div class="col-md-9">
                                     <select name="type" class="form-control">
                                         <option value="1"><?php echo e(translate('User Balance')); ?></option>
                                         <option value="2"><?php echo e(translate('guarantee')); ?></option>
                                     </select>
                                </div>
                                
                                </div>
                            <div class="row" style="margin-bottom:5px;">
                                
                                 <div class="col-md-3">
                                    <label><?php echo e(translate('Withdraw Type')); ?></label>
                                </div>
                                 <div class="col-md-9">
                                     <select name="w_type" class="form-control" id="p">
                                        <option value="1"><?php echo e(translate('Cash')); ?></option>
                                        <option value="2"><?php echo e(translate('Bank')); ?></option>
                                        <option value="3"><?php echo e(translate('USDT')); ?></option>
                            
                                     </select>
                                </div>
                                
                                </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label><?php echo e(translate('Message')); ?></label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="message" rows="8" class="form-control mb-3"></textarea>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Send')); ?></button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="p-5 heading-3">
                            <?php echo e(translate('You do not have enough balance to send withdraw request')); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function show_request_modal() {
            $('#request_modal').modal('show');
        }

        function show_message_modal(id) {
            $.post('<?php echo e(route('withdraw_request.message_modal')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                id: id
            }, function (data) {
                $('#message_modal .modal-content').html(data);
                $('#message_modal').modal('show', {backdrop: 'static'});
            });
        }
        function show_make_wallet_recharge_modal(type){
            if( type == 2 )
            {
                $("#exampleModalLabel").text('<?php echo e(translate('Guarantee Recharge')); ?>');
            }
            $.post('<?php echo e(route('offline_wallet_recharge_modal')); ?>', {type:type,_token:'<?php echo e(csrf_token()); ?>'}, function(data){
                $('#offline_wallet_recharge_modal_body').html(data);
                $('#offline_wallet_recharge_modal').modal('show');
            });
        }
        $("#p").change(function(){
            var usdt_status = <?php echo e($shop->usdt_payment_status); ?>

            var bank_payment_status = <?php echo e($shop->bank_payment_status); ?>

            var cash_on_delivery_status = <?php echo e($shop->cash_on_delivery_status); ?>

            var type = $(this).val();
            if (type == 3 && usdt_status == 0) {
                window.location.href = "/seller/profile#usdt"
                $(".btn").attr("disabled")
            }
            if (type == 2 && bank_payment_status == 0) {
                window.location.href = "/seller/profile#bank"
                 $(".btn").attr("disabled")
            }
            if (type == 1 && cash_on_delivery_status == 0) {
                window.location.href = "/seller/profile#cash"
                 $(".btn").attr("disabled")
            }
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/money_withdraw_requests/index.blade.php ENDPATH**/ ?>