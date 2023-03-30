<?php $__env->startSection('panel_content'); ?>

    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0"><?php echo e(translate('Order Details')); ?></h1>
        </div>

        <div class="card-body">
            <div class="row gutters-2">
                <div class="col text-md-left text-center">
                </div>
                <?php
                    $delivery_status = $order->delivery_status;
                    $payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
                ?>
                <?php if(get_setting('product_manage_by_admin') == 0): ?>
                    <?php if($order->product_storehouse_total > 0): ?>
                        <?php if(!$order->product_storehouse_status): ?>
                            <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                                <button id="payment_for_storehouse" type="button" class="btn btn-primary"><?php echo e(translate('Payment For Storehouse')); ?></button>
                            </div>
                        <?php else: ?>
                            <div class="col-md-2 d-flex flex-nowrap justify-content-end align-items-end ml-auto">
                                <button type="button" class="btn btn-primary" disabled><?php echo e(translate('Picked up')); ?></button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="col-md-3 ml-auto">
                        <label for="update_payment_status"><?php echo e(translate('Payment Status')); ?></label>
                        <?php if($order->payment_type == 'cash_on_delivery' && $payment_status == 'unpaid'): ?>
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity"
                                    id="update_payment_status">
                                <option value="unpaid" <?php if($payment_status == 'unpaid'): ?> selected <?php endif; ?>>
                                    <?php echo e(translate('Unpaid')); ?></option>
                                <option value="paid" <?php if($payment_status == 'paid'): ?> selected <?php endif; ?>>
                                    <?php echo e(translate('Paid')); ?></option>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control" value="<?php echo e(translate($payment_status)); ?>" disabled>
                        <?php endif; ?>
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
                <?php endif; ?>
            </div>









            <div class="row gutters-5 mt-2">
                <div class="col text-md-left text-center">
                    <?php if(json_decode($order->shipping_address)): ?>
                        <address>
                            <strong class="text-main">
                                <?php echo e(json_decode($order->shipping_address)->name); ?>

                            </strong><br>

                            <?php
                                $emailArray = str_split(json_decode($order->shipping_address)->email);
                                $email = '';
                                foreach ($emailArray as $key => $stock) {
                                    if($key < 3)
                                        $email .= $stock;
                                    else
                                        $email .= "*";

                                }
                                echo $email;
                            ?>
                            <br>

                            <?php
                                $phoneArray = str_split(json_decode($order->shipping_address)->phone);
                                $phone = '';
                                foreach ($phoneArray as $key => $stock) {
                                    if($key < 3)
                                        $phone .= $stock;
                                    else
                                        $phone .= "*";

                                }
                                echo $phone;
                            ?>
                            <br>

                            <?php
                                $addressArray = str_split(json_decode($order->shipping_address)->address);
                                $address = '';
                                foreach ($addressArray as $key => $stock) {
                                	$address .= "*";
                                }
                                echo $address;
                            ?>
                            ,

                            <?php
                                $cityArray = str_split(json_decode($order->shipping_address)->city);
                                $city= '';
                                foreach ($cityArray as $key => $stock) {
                                	$city .= "*";
                                }
                                echo $city;
                            ?>
                            ,

                            <?php
                                $postal_codeArray = str_split(json_decode($order->shipping_address)->postal_code);
                                $postal_code= '';
                                foreach ($postal_codeArray as $key => $stock) {
                                	$postal_code .= "*";
                                }
                                echo $postal_code;
                            ?>
                            <br>

                            <?php
                                $countryArray = str_split(json_decode($order->shipping_address)->country);
                                $country= '';
                                foreach ($countryArray as $key => $stock) {
                                	$country .= "*";
                                }
                                echo $country;
                            ?>
                        </address>
                    <?php else: ?>
                        <address>
                            <strong class="text-main">
                                <?php echo e($order->user->name); ?>

                            </strong><br>

                            <?php
                                $emailArray = str_split($order->user->email);
                                $email= '';
                                foreach ($emailArray as $key => $stock) {
                                	if($key < 3)
                                        $email .= $stock;
                                    else
                                        $email .= "*";
                                }
                                echo $email;
                            ?>
                            <br>

                            <?php
                                $phoneArray = str_split($order->user->phone);
                                $phone= '';
                                foreach ($phoneArray as $key => $stock) {
                                	if($key < 3)
                                        $phone .= $stock;
                                    else
                                        $phone .= "*";
                                }
                                echo $phone;
                            ?>
                            <br>
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
                            <td class="text-info text-bold text-right"><?php echo e($order->code); ?></td>
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
                            <td class="text-main text-bold"><?php echo e(translate('Order Date')); ?></td>
                            <td class="text-right"><?php echo e(date('d-m-Y h:i A', $order->date)); ?></td>
                        </tr>
                        <tr>
                            <td class="text-main text-bold"><?php echo e(translate('Total amount')); ?></td>
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
                        
                        <tr>
                            <td class="text-main text-bold"><?php echo e(translate('Time Left')); ?></td>
                            <td class="text-right"><span id="showtime"></span>
                            <span id="mydate" style="display:none"><?php echo e($order->order_type); ?></span>
                            <span id="mycreated" style="display:none"><?php echo e($order->created_at); ?></span>
                            </td>
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
                    <a href="<?php echo e(route('seller.invoice.download', $order->id)); ?>" type="button"
                       class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                </div>
            </div>

        </div>
    </div>


         <?php if( $express->express_info ): ?>
             <?php  date_default_timezone_set("PRC");?>
<style>
    .table {
  border-collapse: collapse;
  margin:20px 0px;
}

.table, th, td {
  border: 2px solid #f0fafe;
}

</style>
                     <table class="table">
                        <tr>
                            <td colspan="2" style="text-align:center; background-color:#f0fafe;"><b><?php echo e(translate('Express information')); ?> </b> </td>
                        </tr>
                        <tr>
                            <td style="width:100px;"> <?php echo e(translate('courier company')); ?>：</td>
                            <td>  <?php echo e($express->express_name); ?> </td>
                        </tr>

                        <tr>
                            <td> <?php echo e(translate('shipment number')); ?> ：</td>
                            <td>  <?php echo e($express->express_code); ?> </td>
                        </tr>


                        <tr>
                            <td colspan="2" style="text-align:center;background-color:#f0fafe;"><b><?php echo e(translate('Logistics tracking information')); ?></b></td>

                        </tr>

                         <?php if( $express->express_info ): ?>

                                <?php $__currentLoopData = $express->express_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <?php

                                 if( strtotime( $express->express_time[$key] ) < time()   ){ ?>
                          <tr><td colspan="2">     <?php echo e($express->express_stime[$key]); ?> <?php echo e($ex); ?>  </td></tr>
                                <?php  } ?>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                              <?php endif; ?>



                    </table>
                    <?php endif; ?>


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
                            <h5 class="text-center"><?php echo e(translate('Pay with wallet')); ?> <?php echo e(single_price($order->product_storehouse_total)); ?></h5>
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
    <script src="https://cdn.bootcss.com/blueimp-md5/2.10.0/js/md5.min.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        $('#payment_for_storehouse').on('click', function () {
            
             var tpwd = '<?php echo e($tpwd); ?>'
             if (!tpwd) {
                 location.href="/seller/transaction"
                 return
             } else {
                 $('#payment_for_storehouse_modal').modal('show');
             }
        })
        // 付款
        $('#payment_button').on('click', function () {
            
            var tpwd = '<?php echo e($tpwd); ?>'
            var pwd = $("#tpwd").val();
            if (md5(pwd) != tpwd) {
                AIZ.plugins.notify('danger',
                        '<?php echo e(translate('password error')); ?>');
                return;
            }
            
            $.post('<?php echo e(route('seller.orders.payment_for_storehouse_product')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: '<?php echo e(encrypt($order->id)); ?>'
            }, function (data) {
                console.log(data)
                if (data.success == 1) {
                    $('#order_details').modal('hide');
                    AIZ.plugins.notify('success', '<?php echo e(translate('Order status has been updated')); ?>');
                    location.reload().setTimeOut(500);
                } else {
                    AIZ.plugins.notify('danger', data.message ? data.message : '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        })
        $('#update_delivery_status').on('change', function () {
            return false;
            var order_id = <?php echo e($order->id); ?>;
            var status = $('#update_delivery_status').val();
            $.post('<?php echo e(route('seller.orders.update_delivery_status')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                status: status
            }, function (data) {
                $('#order_details').modal('hide');
                AIZ.plugins.notify('success', '<?php echo e(translate('Order status has been updated')); ?>');
                location.reload().setTimeOut(500);
            });
        });

        $('#update_payment_status').on('change', function () {
            var order_id = <?php echo e($order->id); ?>;
            var status = $('#update_payment_status').val();
            $.post('<?php echo e(route('seller.orders.update_payment_status')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                status: status
            }, function (data) {
                $('#order_details').modal('hide');
                //console.log(data);
                AIZ.plugins.notify('success', '<?php echo e(translate('Payment status has been updated')); ?>');
                location.reload().setTimeOut(500);
            });
        });
        
        $(document).ready(function () {
            var div = document.getElementById("showtime");
            var mydate = document.getElementById("mydate").innerHTML;
            var mycreated = document.getElementById("mycreated").innerHTML;
            setInterval (function () {
                div.innerHTML = showtime(mydate,mycreated);
            }, 1000);  //反复执行函数本身
        });

        var showtime = function (mydate,mycreated) {
           
               var nowtime = new Date();  //获取当前时间
               var created = new Date(mycreated);  //定义结束时间
               var addtime = created.getTime() + (mydate * 3600000);
               var lefttime = addtime - nowtime.getTime(),  //距离结束时间的毫秒数
               
               leftd = Math.floor(lefttime/(1000*60*60*24)),  //计算天数
               lefth = Math.floor(lefttime/(1000*60*60)%24),  //计算小时数
               leftm = Math.floor(lefttime/(1000*60)%60),  //计算分钟数
               lefts = Math.floor(lefttime/1000%60);  //计算秒数
                 //alert(lefttime);
            if(leftd <= 0 && lefth <= 0 && leftm <= 0 && lefts <= 0){
                    return "0";
                }else{
                    return leftd + "天" + lefth + ":" + leftm + ":" + lefts;  //返回倒计时的字符串
                }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/orders/show.blade.php ENDPATH**/ ?>