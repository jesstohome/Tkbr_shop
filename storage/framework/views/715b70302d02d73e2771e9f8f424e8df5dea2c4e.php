<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0"><?php echo e(translate('Order Details')); ?></h1>
        </div>

        <div class="card-body">
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
                        Name: <?php echo e(json_decode($order->manual_payment_data)->name); ?>, Amount:
                        <?php echo e(single_price(json_decode($order->manual_payment_data)->amount)); ?>, TRX ID:
                        <?php echo e(json_decode($order->manual_payment_data)->trx_id); ?>

                        <br>
                        <a href="<?php echo e(uploaded_asset(json_decode($order->manual_payment_data)->photo)); ?>"
                            target="_blank"><img
                                src="<?php echo e(uploaded_asset(json_decode($order->manual_payment_data)->photo)); ?>" alt=""
                                height="100"></a>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 ml-auto">
                    <table class="table-bordered aiz-table table">
                        <tbody>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Order #')); ?></td>
                                <td class="text-info text-bold text-right"><?php echo e($order->code); ?></td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold"><?php echo e(translate('Order Status')); ?></td>
                                <?php
                                    $status = $order->orderDetails->first()->delivery_status;
                                ?>
                                <td class="text-right">
                                    <?php if($status == 'delivered'): ?>
                                        <span
                                            class="badge badge-inline badge-success"><?php echo e(translate(ucfirst(str_replace('_', ' ', $status)))); ?></span>
                                    <?php else: ?>
                                        <span
                                            class="badge badge-inline badge-info"><?php echo e(translate(ucfirst(str_replace('_', ' ', $status)))); ?></span>
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
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="invoice-bill row">
                <div class="col-sm-6">

                </div>
                <div class="col-sm-6">

                </div>
            </div>
            <hr class="new-section-sm bord-no">
            <div class="">
                <table class="table-bordered aiz-table table">
                    <thead>
                        <tr class="bg-trans-dark">
                            <th data-breakpoints="lg" class="min-col">#</th>
                            <th width="10%"><?php echo e(translate('Photo')); ?></th>
                            <th class="text-uppercase"><?php echo e(translate('Description')); ?></th>
                            <th data-breakpoints="lg" class="text-uppercase"><?php echo e(translate('Delivery Type')); ?></th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center"><?php echo e(translate('Qty')); ?>

                            </th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-center"><?php echo e(translate('Price')); ?>

                            </th>
                            <th data-breakpoints="lg" class="min-col text-uppercase text-right"><?php echo e(translate('Total')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $admin_user_id = \App\Models\User::where('user_type', 'admin')->first()->id;
                        ?>
                        <?php $__currentLoopData = $order->orderDetails->where('seller_id', '!=', $admin_user_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $orderDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($key + 1); ?></td>
                                <td>
                                    <?php if($orderDetail->product != null): ?>
                                        <a href="<?php echo e(route('product', $orderDetail->product->slug)); ?>"
                                            target="_blank"><img height="50px"
                                                src="<?php echo e(uploaded_asset($orderDetail->product->thumbnail_img)); ?>"></a>
                                    <?php else: ?>
                                        <strong><?php echo e(translate('N/A')); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($orderDetail->product != null): ?>
                                        <strong><a href="<?php echo e(route('product', $orderDetail->product->slug)); ?>"
                                                target="_blank"
                                                class="text-muted"><?php echo e($orderDetail->product->getTranslation('name')); ?></a></strong>
                                        <small><?php echo e($orderDetail->variation); ?></small>
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
                                    <?php echo e(single_price($orderDetail->price / $orderDetail->quantity)); ?>

                                </td>
                                <td class="text-center"><?php echo e(single_price($orderDetail->price)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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
                            <td><strong class="text-muted"><?php echo e(translate('Sub Total')); ?> :</strong></td>
                            <td>
                                <?php echo e(single_price($order->orderDetails->sum('price'))); ?>

                            </td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted"><?php echo e(translate('Tax')); ?> :</strong></td>
                            <td><?php echo e(single_price($order->orderDetails->sum('tax'))); ?></td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted"> <?php echo e(translate('Shipping')); ?> :</strong></td>
                            <td><?php echo e(single_price($order->orderDetails->sum('shipping_cost'))); ?></td>
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
                            <td><strong class="text-muted"><?php echo e(translate('TOTAL')); ?> :</strong></td>
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
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sales/seller_orders/show.blade.php ENDPATH**/ ?>