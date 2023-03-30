

<?php $__env->startSection('content'); ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start">
                <?php echo $__env->make('frontend.inc.user_side_nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="aiz-user-panel">
                    <div class="aiz-titlebar mt-2 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h1 class="h3"><?php echo e(translate('My Points')); ?></h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mx-auto">
                            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                                <div class="px-3 pt-3 pb-3">
                                    <div class="h3 fw-700 text-center"><?php echo e(get_setting('club_point_convert_rate')); ?> <?php echo e(translate(' Points')); ?> = <?php echo e(single_price(1)); ?> <?php echo e(translate('Wallet Money')); ?></div>
                                    <div class="opacity-50 text-center"><?php echo e(translate('Exchange Rate')); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6"><?php echo e(translate('Point Earning history')); ?></h5>
                        </div>
                          <div class="card-body">
                              <table class="table aiz-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo e(translate('Order Code')); ?></th>
                                        <th data-breakpoints="lg"><?php echo e(translate('Points')); ?></th>
                                        <th data-breakpoints="lg"><?php echo e(translate('Converted')); ?></th>
                                        <th data-breakpoints="lg"><?php echo e(translate('Date')); ?></th>
                                        <th class="text-right"><?php echo e(translate('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $club_points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $club_point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($key+1); ?></td>
                                            <td>
                                            <?php if($club_point->order != null): ?>
                                                    <?php echo e($club_point->order->code); ?>

                                                <?php else: ?>
                                                    <?php echo e(translate('Order not found')); ?>

                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($club_point->points); ?> <?php echo e(translate(' pts')); ?></td>
                                            <td>
                                                <?php if($club_point->convert_status == 1): ?>
                                                    <span class="badge badge-inline badge-success"><?php echo e(translate('Yes')); ?></strong></span>
                                                <?php else: ?>
                                                    <span class="badge badge-inline badge-info"><?php echo e(translate('No')); ?></strong></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(date('d-m-Y', strtotime($club_point->created_at))); ?></td>

                                            <td class="text-right">
                                                <?php if($club_point->convert_status == 0): ?>
                                                    <button onclick="convert_point(<?php echo e($club_point->id); ?>)" class="btn btn-sm btn-styled btn-primary"><?php echo e(translate('Convert Now')); ?></button>
                                                <?php else: ?>
                                                  <span class="badge badge-inline badge-success"><?php echo e(translate('Done')); ?></span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                              </table>
                              <div class="aiz-pagination">
                                  <?php echo e($club_points->links()); ?>

                              </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function convert_point(el)
        {
            $.post('<?php echo e(route('convert_point_into_wallet')); ?>',{_token:'<?php echo e(csrf_token()); ?>', el:el}, function(data){
                if (data == 1) {
                    location.reload();
                    AIZ.plugins.notify('success', '<?php echo e(translate('Convert has been done successfully Check your Wallets')); ?>');
                }
                else {
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
    		});
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/club_points/frontend/index.blade.php ENDPATH**/ ?>