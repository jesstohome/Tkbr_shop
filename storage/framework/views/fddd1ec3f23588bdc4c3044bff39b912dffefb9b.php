<?php $__env->startSection('panel_content'); ?>

    <div class="card">
        <form id="sort_orders" action="" method="GET">
          <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
              <h5 class="mb-md-0 h6"><?php echo e(translate('Orders')); ?></h5>
            </div>
              <div class="col-md-3 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="<?php echo e(translate('Filter by Payment Status')); ?>" name="payment_status" onchange="sort_orders()">
                      <option value=""><?php echo e(translate('Filter by Payment Status')); ?></option>
                      <option value="paid" <?php if(isset($payment_status)): ?> <?php if($payment_status == 'paid'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Paid')); ?></option>
                      <option value="unpaid" <?php if(isset($payment_status)): ?> <?php if($payment_status == 'unpaid'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Un-Paid')); ?></option>
                  </select>
              </div>

              <div class="col-md-3 ml-auto">
                <select class="form-control aiz-selectpicker" data-placeholder="<?php echo e(translate('Filter by Payment Status')); ?>" name="delivery_status" onchange="sort_orders()">
                    <option value=""><?php echo e(translate('Filter by Deliver Status')); ?></option>
                    <option value="pending" <?php if(isset($delivery_status)): ?> <?php if($delivery_status == 'pending'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Pending')); ?></option>
                    <option value="confirmed" <?php if(isset($delivery_status)): ?> <?php if($delivery_status == 'confirmed'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Confirmed')); ?></option>
                    <option value="on_delivery" <?php if(isset($delivery_status)): ?> <?php if($delivery_status == 'on_delivery'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('On delivery')); ?></option>
                    <option value="delivered" <?php if(isset($delivery_status)): ?> <?php if($delivery_status == 'delivered'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Delivered')); ?></option>
                </select>
              </div>
              <div class="col-md-3">
                <div class="from-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" <?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type Order code & hit Enter')); ?>">
                </div>
              </div>
          </div>
        </form>

        <?php if(count($orders) > 0): ?>
            <div class="card-body p-3">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo e(translate('Order Code')); ?></th>
                            <th data-breakpoints="lg"><?php echo e(translate('Num. of Products')); ?></th>
                            <th data-breakpoints="lg"><?php echo e(translate('Customer')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Amount')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Profit')); ?></th>
                            <th data-breakpoints="md"><?php echo e(translate('Pick Up Status')); ?></th>
                            <th data-breakpoints="lg"><?php echo e(translate('Delivery Status')); ?></th>
                            <th><?php echo e(translate('Payment Status')); ?></th>
                            <th class="text-right"><?php echo e(translate('Options')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order_id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $order = \App\Models\Order::find($order_id->id);
                            ?>
                            <?php if($order != null): ?>
                                <tr>
                                    <td>
                                        <?php echo e($key+1); ?>

                                    </td>
                                    <td>
                                        <a href="#<?php echo e($order->code); ?>" onclick="show_order_details(<?php echo e($order->id); ?>)"><?php echo e($order->code); ?></a>
                                    </td>
                                    <td>
                                        <?php echo e(count($order->orderDetails->where('seller_id', Auth::user()->id))); ?>

                                    </td>
                                    <td>
                                        <?php if($order->user_id != null): ?>
                                            <?php echo e(optional($order->user)->name); ?>

                                        <?php else: ?>
                                            <?php echo e(translate('Guest')); ?> (<?php echo e($order->guest_id); ?>)
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e(single_price($order->grand_total)); ?>

                                    </td>
                                    <td>
                                        <?php if($order->product_storehouse_total > 0): ?>
                                            <?php echo e(single_price($order->grand_total - $order->product_storehouse_total)); ?>

                                        <?php else: ?>
                                            <?php echo e(translate('None')); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order->product_storehouse_status): ?>
                                            <span class="badge badge-inline badge-success"><?php echo e(translate('Picked Up')); ?></span>
                                        <?php else: ?>
                                            <?php if($order->product_storehouse_total): ?>
                                                <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpicked Up')); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $status = $order->delivery_status;
                                        ?>
                                        <?php echo e(translate(ucfirst(str_replace('_', ' ', $status)))); ?>

                                    </td>
                                    <td>
                                        <?php if($order->payment_status == 'paid'): ?>
                                            <span class="badge badge-inline badge-success"><?php echo e(translate('Paid')); ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpaid')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo e(route('salesman.orders.show', encrypt($order->id))); ?>" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="<?php echo e(translate('Order Details')); ?>">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('salesman.invoice.download', $order->id)); ?>" class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="<?php echo e(translate('Download Invoice')); ?>">
                                            <i class="las la-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    <?php echo e($orders->links()); ?>

              	</div>
            </div>
        <?php endif; ?>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('salesman.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/salesman/orders/index.blade.php ENDPATH**/ ?>