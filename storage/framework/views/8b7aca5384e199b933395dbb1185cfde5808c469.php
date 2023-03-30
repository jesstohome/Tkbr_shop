<?php $__env->startSection('content'); ?>

<div class="card">
    <form class="" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6"><?php echo e(translate('Seller Orders')); ?></h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($date); ?>" name="date" placeholder="<?php echo e(translate('Filter by date')); ?>" data-format="DD-MM-Y" data-separator=" to " data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="seller_id" name="seller_id">
                        <option value=""><?php echo e(translate('All Sellers')); ?></option>
                        <?php $__currentLoopData = App\Models\User::where('user_type', '=', 'seller')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($seller->id); ?>" <?php if($seller->id == $seller_id): ?> selected <?php endif; ?>>
                                <?php echo e($seller->shop->name); ?> (<?php echo e($seller->name); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"<?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type Order code & hit Enter')); ?>">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary"><?php echo e(translate('Filter')); ?></button>
                </div>
            </div>
        </div>
    </form>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th width="20%"><?php echo e(translate('Order Code')); ?></th>
                    <th width="20%"><?php echo e(translate('Shop')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Num. of Products')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Customer')); ?></th>
                    <th><?php echo e(translate('Seller')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Amount')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Profit')); ?></th>
                    <th data-breakpoints="md"><?php echo e(translate('Pick Up Status')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Delivery Status')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Payment Method')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Payment Status')); ?></th>
                    <?php if(addon_is_activated('refund_request')): ?>
                        <th><?php echo e(translate('Refund')); ?></th>
                    <?php endif; ?>
                    <th class="text-right" width="15%"><?php echo e(translate('Options')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php echo e(($key+1) + ($orders->currentPage() - 1)*$orders->perPage()); ?>

                        </td>
                        <td>
                            <?php echo e($order->code); ?><?php if($order->viewed == 0): ?> <span class="badge badge-inline badge-info"><?php echo e(translate('New')); ?></span><?php endif; ?>
                        </td>
                        
                         <td>
                            <?php
                            $shop = App\Models\User::where('id',$order->seller_id)->first();
                            echo $shop['email'];
                            ?>
                            
                            
                        </td>
                        
                        
                        <td>
                            <?php echo e(count($order->orderDetails->where('seller_id', '!=', $admin_user_id))); ?>

                        </td>
                        <td>
                            <?php if($order->user != null): ?>
                                <?php echo e($order->user->name); ?>

                            <?php else: ?>
                                Guest (<?php echo e($order->guest_id); ?>)
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($order->shop): ?>
                                <?php echo e($order->shop->name); ?>

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
                                <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpicked Up')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $status = $order->delivery_status;
                            ?>
                            <?php echo e(translate(ucfirst(str_replace('_', ' ', $status)))); ?>

                        </td>
                        <td>
                            <?php echo e(translate(ucfirst(str_replace('_', ' ', $order->payment_type)))); ?>

                        </td>
                        <td>
                            <?php if($order->payment_status == 'paid'): ?>
                            <span class="badge badge-inline badge-success"><?php echo e(translate('Paid')); ?></span>
                            <?php else: ?>
                            <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpaid')); ?></span>
                            <?php endif; ?>
                        </td>
                        <?php if(addon_is_activated('refund_request')): ?>
                            <td>
                                <?php if(count($order->refund_requests) > 0): ?>
                                    <?php echo e(count($order->refund_requests)); ?> <?php echo e(translate('Refund')); ?>

                                <?php else: ?>
                                    <?php echo e(translate('No Refund')); ?>

                                <?php endif; ?>
                            </td>
                        <?php endif; ?>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('seller_orders.show', encrypt($order->id))); ?>" title="<?php echo e(translate('View')); ?>">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="<?php echo e(route('invoice.download', $order->id)); ?>" title="<?php echo e(translate('Download Invoice')); ?>">
                                <i class="las la-download"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('orders.destroy', $order->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($orders->appends(request()->input())->links()); ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function sort_orders(el){
            $('#sort_orders').submit();
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sales/seller_orders/index.blade.php ENDPATH**/ ?>