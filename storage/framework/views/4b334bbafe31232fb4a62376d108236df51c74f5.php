<?php $__env->startSection('content'); ?>
<script src='/My97DatePicker/WdatePicker.js'></script>
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0"><?php echo e(translate('Order Details')); ?></h1>
        </div>
        <div class="card-body">
            <div class="row gutters-3">
                <div class="col text-md-left text-center">
                </div>
                <?php
                    $delivery_status = $order->delivery_status;
                    $payment_status = $order->payment_status;
                ?>

                <?php if($order->product_storehouse_total > 0): ?>
                    <?php if(!$order->freeze_expired_at): ?>
                        <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                            <button type="button" class="btn btn-primary" disabled><?php echo e(translate('Free up frozen funds')); ?></button>
                        </div>
                    <?php else: ?>
                        <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                            <button id="free_up_btn" type="button" class="btn btn-primary confirm-alert" data-href="<?php echo e(route('product-storehouse-order-free-up', $order->id)); ?>" data-target="#free-up-modal"><?php echo e(translate('Free up frozen funds')); ?></button>
                        </div>
                    <?php endif; ?>
                    <?php if($order->product_storehouse_status): ?>
                        <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                            <button type="button" class="btn btn-info" disabled><?php echo e(translate('Picked up')); ?></button>
                        </div>
                    <?php else: ?>
                        <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                            <button type="button" class="btn btn-info" disabled><?php echo e(translate('Unpaid')); ?></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <!--Assign Delivery Boy-->
                <?php if(addon_is_activated('delivery_boy')): ?>
                    <div class="col-md-3 ml-auto">
                        <label for="assign_deliver_boy"><?php echo e(translate('Assign Deliver Boy')); ?></label>
                        <?php if($delivery_status == 'pending' || $delivery_status == 'confirmed' || $delivery_status == 'picked_up'): ?>
                            <select class="form-control aiz-selectpicker" data-live-search="true"
                                data-minimum-results-for-search="Infinity" id="assign_deliver_boy">
                                <option value=""><?php echo e(translate('Select Delivery Boy')); ?></option>
                                <?php $__currentLoopData = $delivery_boys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery_boy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($delivery_boy->id); ?>"
                                        <?php if($order->assign_delivery_boy == $delivery_boy->id): ?> selected <?php endif; ?>>
                                        <?php echo e($delivery_boy->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control" value="<?php echo e(optional($order->delivery_boy)->name); ?>"
                                disabled>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="col-md-3 ml-auto">
                    <label for="update_payment_status"><?php echo e(translate('Payment Status')); ?></label>
                    <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                        id="update_payment_status">
                        <option value="unpaid" <?php if($payment_status == 'unpaid'): ?> selected <?php endif; ?>><?php echo e(translate('Unpaid')); ?>

                        </option>
                        <option value="paid" <?php if($payment_status == 'paid'): ?> selected <?php endif; ?>><?php echo e(translate('Paid')); ?>

                        </option>
                    </select>
                </div>
                <div class="col-md-3 ml-auto">
                    <label for="update_delivery_status"><?php echo e(translate('Delivery Status')); ?></label>
                    <?php if($delivery_status != 'delivered' && $delivery_status != 'cancelled'): ?>
                        <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                            id="update_delivery_status">
                            <option value="pending" <?php if($delivery_status == 'pending'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('Pending')); ?></option>
                            <option value="confirmed" <?php if($delivery_status == 'confirmed'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('Confirmed')); ?></option>
                            <option value="picked_up" <?php if($delivery_status == 'picked_up'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('Picked Up')); ?></option>
                            <option value="on_the_way" <?php if($delivery_status == 'on_the_way'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('On The Way')); ?></option>
                            <option value="delivered" <?php if($delivery_status == 'delivered'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('Delivered')); ?></option>
                            <option value="cancelled" <?php if($delivery_status == 'cancelled'): ?> selected <?php endif; ?>>
                                <?php echo e(translate('Cancel')); ?></option>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control" value="<?php echo e($delivery_status); ?>" disabled>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 ml-auto">
                    <label for="update_tracking_code"><?php echo e(translate('Tracking Code (optional)')); ?></label>
                    <input type="text" class="form-control" id="update_tracking_code"
                        value="<?php echo e($order->tracking_code); ?>">
                </div>
            </div>
            
  
            
            <div class="mb-3">
                <?php
                    $removedXML = '<?xml version="1.0" encoding="UTF-8"';
                ?>
                <?php echo str_replace([$removedXML, '?>'], '', QrCode::size(100)->generate($order->code)); ?>

            </div>
            <div class="row gutters-5">
                <div class="col text-md-left text-center">
                    <?php if(json_decode($order->shipping_address)): ?>
                        <address>
                            <strong class="text-main">
                                <?php echo e(json_decode($order->shipping_address)->name); ?>

                            </strong><br>
                            <?php echo e(json_decode($order->shipping_address)->email); ?><br>
                            <?php echo e(json_decode($order->shipping_address)->phone); ?><br>
                            <?php echo e(json_decode($order->shipping_address)->address); ?>, <?php echo e(json_decode($order->shipping_address)->city); ?>, <?php echo e(json_decode($order->shipping_address)->postal_code); ?><br>
                            <?php echo e(json_decode($order->shipping_address)->country); ?>

                        </address>
                    <?php else: ?>
                        <address>
                            <strong class="text-main">
                                <?php echo e($order->user->name); ?>

                            </strong><br>
                            <?php echo e($order->user->email); ?><br>
                            <?php echo e($order->user->phone); ?><br>
                        </address>
                    <?php endif; ?>
                    <?php if($order->manual_payment && is_array(json_decode($order->manual_payment_data, true))): ?>
                        <br>
                        <strong class="text-main"><?php echo e(translate('Payment Information')); ?></strong><br>
                        <?php echo e(translate('Name')); ?>: <?php echo e(json_decode($order->manual_payment_data)->name); ?>,
                        <?php echo e(translate('Amount')); ?>:
                        <?php echo e(single_price(json_decode($order->manual_payment_data)->amount)); ?>,
                        <?php echo e(translate('TRX ID')); ?>: <?php echo e(json_decode($order->manual_payment_data)->trx_id); ?>

                        <br>
                        <a href="<?php echo e(uploaded_asset(json_decode($order->manual_payment_data)->photo)); ?>"
                            target="_blank"><img
                                src="<?php echo e(uploaded_asset(json_decode($order->manual_payment_data)->photo)); ?>" alt=""
                                height="100"></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 ml-auto">
                    <table>
                        <tbody>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Order #')); ?></td>
                                <td class="text-info text-bold text-right"> <?php echo e($order->code); ?></td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Order Status')); ?></td>
                                <td class="text-right">
                                    <?php if($delivery_status == 'delivered'): ?>
                                        <span
                                            class="badge badge-inline badge-success"><?php echo e(translate(ucfirst(str_replace('_', ' ', $delivery_status)))); ?></span>
                                    <?php else: ?>
                                        <span
                                            class="badge badge-inline badge-info"><?php echo e(translate(ucfirst(str_replace('_', ' ', $delivery_status)))); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Order Date')); ?> </td>
                                <td class="text-right"><?php echo e(date('d-m-Y h:i A', $order->date)); ?></td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">
                                    <?php echo e(translate('Total amount')); ?>

                                </td>
                                <td class="text-right">
                                    <?php echo e(single_price($order->grand_total)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Payment method')); ?></td>
                                <td class="text-right">
                                    <?php echo e(translate(ucfirst(str_replace('_', ' ', $order->payment_type)))); ?></td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Additional Info')); ?></td>
                                <td class="text-right"><?php echo e($order->additional_info); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table-bordered aiz-table invoice-summary table">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th data-breakpoints="lg" class="min-col">#</th>
                                <th width="10%"><?php echo e(translate('Photo')); ?></th>
                                <th class="text-uppercase"><?php echo e(translate('Description')); ?></th>
                                <th data-breakpoints="lg" class="text-uppercase"><?php echo e(translate('Delivery Type')); ?></th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    <?php echo e(translate('Qty')); ?>

                                </th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">
                                    <?php echo e(translate('Price')); ?></th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-right">
                                    <?php echo e(translate('Total')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->orderDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $orderDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($key + 1); ?></td>
                                    <td>
                                        <?php if($orderDetail->product != null && $orderDetail->product->auction_product == 0): ?>
                                            <a href="<?php echo e(route('product', $orderDetail->product->slug)); ?>"
                                                target="_blank"><img height="50"
                                                    src="<?php echo e(uploaded_asset($orderDetail->product->thumbnail_img)); ?>"></a>
                                        <?php elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1): ?>
                                            <a href="<?php echo e(route('auction-product', $orderDetail->product->slug)); ?>"
                                                target="_blank"><img height="50"
                                                    src="<?php echo e(uploaded_asset($orderDetail->product->thumbnail_img)); ?>"></a>
                                        <?php else: ?>
                                            <strong><?php echo e(translate('N/A')); ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($orderDetail->product != null && $orderDetail->product->auction_product == 0): ?>
                                            <strong><a href="<?php echo e(route('product', $orderDetail->product->slug)); ?>"
                                                    target="_blank"
                                                    class="text-muted"><?php echo e($orderDetail->product->getTranslation('name')); ?></a></strong>
                                            <small><?php echo e($orderDetail->variation); ?></small>
                                        <?php elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1): ?>
                                            <strong><a href="<?php echo e(route('auction-product', $orderDetail->product->slug)); ?>"
                                                    target="_blank"
                                                    class="text-muted"><?php echo e($orderDetail->product->getTranslation('name')); ?></a></strong>
                                        <?php else: ?>
                                            <strong><?php echo e(translate('Product Unavailable')); ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order->shipping_type != null && $order->shipping_type == 'home_delivery'): ?>
                                            <?php echo e(translate('Home Delivery')); ?>

                                        <?php elseif($order->shipping_type == 'pickup_point'): ?>
                                            <?php if($order->pickup_point != null): ?>
                                                <?php echo e($order->pickup_point->getTranslation('name')); ?>

                                                (<?php echo e(translate('Pickup Point')); ?>)
                                            <?php else: ?>
                                                <?php echo e(translate('Pickup Point')); ?>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo e($orderDetail->quantity); ?></td>
                                    <td class="text-center">
                                        <?php echo e(single_price($orderDetail->price / $orderDetail->quantity)); ?></td>
                                    <td class="text-center"><?php echo e(single_price($orderDetail->price)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                        <?php if($order->product_storehouse_total > 0): ?>
                            <tr>
                                <td>
                                    <strong class="text-muted"><?php echo e(translate('Storehouse Price')); ?> :</strong>
                                </td>
                                <td>
                                    <?php echo e(single_price($order->product_storehouse_total)); ?>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-muted"><?php echo e(translate('Profit')); ?> :</strong>
                                </td>
                                <td>
                                    <?php echo e(single_price($order->grand_total - $order->product_storehouse_total)); ?>

                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>
                                <strong class="text-muted"><?php echo e(translate('Sub Total')); ?> :</strong>
                            </td>
                            <td>
                                <?php echo e(single_price($order->orderDetails->sum('price'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted"><?php echo e(translate('Tax')); ?> :</strong>
                            </td>
                            <td>
                                <?php echo e(single_price($order->orderDetails->sum('tax'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted"><?php echo e(translate('Shipping')); ?> :</strong>
                            </td>
                            <td>
                                <?php echo e(single_price($order->orderDetails->sum('shipping_cost'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted"><?php echo e(translate('Coupon')); ?> :</strong>
                            </td>
                            <td>
                                <?php echo e(single_price($order->coupon_discount)); ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong class="text-muted"><?php echo e(translate('TOTAL')); ?> :</strong>
                            </td>
                            <td class="text-muted h5">
                                <?php echo e(single_price($order->grand_total)); ?>

                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="no-print text-right">
                    <a href="<?php echo e(route('invoice.download', $order->id)); ?>" type="button" class="btn btn-icon btn-light"><i
                            class="las la-print"></i></a>
                </div>
            </div>


          <style>
                #wuliu .form-control{ width:150px; display:inline-block; margin-top:10px;}
                #wuliu .btn-add{   margin-left:10px; width:50px;}
            </style>
                 
        <?php error_reporting(0); ?>
        <div class="row gutters-5" id="wuliu">
            <div class="col-md-12 ml-auto" style="margin-top:10px;">
                <label for="update_tracking_code">物流信息</label>
                <!--<textarea class="form-control" id="update_shipping_info" rows="10"><?php echo e($order->shipping_info); ?></textarea>  -->
                <div style="border:1px solid #f2f3f8; border-radius:10px;;padding:10px;margin-bottom:10px;" id="expessdiv">
                    <form id="form1">
                    <div class="dv">物流公司：<input type="text" class="form-control" name="express_name" value="<?php echo e($express->express_name); ?>" />
                        <input type="hidden" name="order_id" value="<?php echo e($order->id); ?>" />
                    </div>
                    <div class="dv">物流单号：<input type="text" class="form-control" name="express_code" value="<?php echo e($express->express_code); ?>" />
                    </div>
                    <div class="dv">
                        <br><br>
                        物流信息：<br><br>
                        <div class="exp">
                                <?php if( $express->express_info ): ?> 
                                <?php $__currentLoopData = $express->express_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               
                              信息：<input type="text" class="form-control" name="express_info[]"  value="<?php echo e($ex); ?>"/> &nbsp;&nbsp;快递时间：<input class="form-control"  onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" readonly type="text" value="<?php echo e($express->express_stime[$key]); ?>" name="express_stime[]" />&nbsp;&nbsp;显示时间：<input class="form-control" onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" readonly type="text" value="<?php echo e($express->express_time[$key]); ?>" name="express_time[]" /><input type="button"  value="+" onclick="addinfo()" class="btn btn-primary btn-add" />
                               <br><br>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              <?php else: ?>
                              信息：<input type="text" class="form-control" name="express_info[]"  value="<?php echo e($ex); ?>"/> &nbsp;&nbsp;&nbsp;&nbsp;显示时间：<input  onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" readonly type="text" class="form-control" value="<?php echo e($express->express_stime[$key]); ?>" name="express_stime[]" />&nbsp;&nbsp;显示时间：<input  onclick="WdatePicker({dateFmt:'yyyy:MM:dd HH:mm:ss'})" readonly type="text" class="form-control" value="<?php echo e($express->express_time[$key]); ?>" name="express_time[]" /><input type="button" value="+" onclick="addinfo()" class="btn btn-primary btn-add" />
                               <br><br>
                              
                              <?php endif; ?>
                        </div>
                    </div>
                    <br>
                        <input type="button" value="保存物流信息" class="btn btn-info" onclick="save_express_info()" />
                    </form>
                </div>
              </div>
        </div>
            
        </div>
        
        
        
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('modal'); ?>
    <div id="free-up-modal" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6"><?php echo e(translate('Free Up Of Freeze Funds Confirmation')); ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body text-center">
                    <h5 class="mt-1 text-"><?php echo e(translate('Are you sure to free this up?')); ?></h5>
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                    <a href="" id="comfirm-link" class="btn btn-primary mt-2"><?php echo e(translate('Free Up')); ?></a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
    
    
      function addinfo()
        {
            var html = '<div>信息：<input type="text" class="form-control" name="express_info[]" /> &nbsp;&nbsp;快递时间：<input class="form-control" type="text"  onclick="WdatePicker({dateFmt:\'yyyy:MM:dd HH:mm:ss\'})" name="express_stime[]" readonly />&nbsp;&nbsp;显示时间：<input class="form-control" onclick="WdatePicker({dateFmt:\'yyyy:MM:dd HH:mm:ss\'})" readonly type="text" value="<?php echo e($express->express_time[$key]); ?>" name="express_time[]" /><input class="btn btn-primary btn-add" type="button" value="+" onclick="addinfo()" /></div>';
            $('.exp').append( html );
        }
        
        function save_express_info()
        {
             $.post('<?php echo e(route('orders.update_delivery_info')); ?>', {
                    _token:'<?php echo e(@csrf_token()); ?>',
                    data:$("#form1").serialize() 
                }, function(data){
                    alert( data )
                },'text');
                    
                    
        }
        
        
        $('#assign_deliver_boy').on('change', function() {
            var order_id = <?php echo e($order->id); ?>;
            var delivery_boy = $('#assign_deliver_boy').val();
            $.post('<?php echo e(route('orders.delivery-boy-assign')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                delivery_boy: delivery_boy
            }, function(data) {
                AIZ.plugins.notify('success', '<?php echo e(translate('Delivery boy has been assigned')); ?>');
            });
        });

        $('#update_delivery_status').on('change', function() {
            var order_id = <?php echo e($order->id); ?>;
            var status = $('#update_delivery_status').val();
            $.post('<?php echo e(route('orders.update_delivery_status')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '<?php echo e(translate('Delivery status has been updated')); ?>');
            });
        });

        $('#update_payment_status').on('change', function() {
            var order_id = <?php echo e($order->id); ?>;
            var status = $('#update_payment_status').val();
            $.post('<?php echo e(route('orders.update_payment_status')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '<?php echo e(translate('Payment status has been updated')); ?>');
            });
        });

        $('#update_tracking_code').on('change', function() {
            var order_id = <?php echo e($order->id); ?>;
            var tracking_code = $('#update_tracking_code').val();
            $.post('<?php echo e(route('orders.update_tracking_code')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                tracking_code: tracking_code
            }, function(data) {
                AIZ.plugins.notify('success', '<?php echo e(translate('Order tracking code has been updated')); ?>');
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sales/all_orders/show.blade.php ENDPATH**/ ?>