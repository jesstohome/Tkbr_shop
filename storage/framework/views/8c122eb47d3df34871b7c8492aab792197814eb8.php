

<?php $__env->startSection('panel_content'); ?>

    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3"><?php echo e(translate('Refund Requests')); ?></h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header row gutters-5">
            <h5 class="mb-0 h6"><?php echo e(translate('All Refund Request')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="lg"><?php echo e(translate('Date')); ?></th>
                        <th><?php echo e(translate('Order id')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Product')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Amount')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Status')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Reason')); ?></th>
                        <th><?php echo e(translate('Approval')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Reject')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key+1); ?></td>
                            <td><?php echo e(date('d-m-Y', strtotime($refund->created_at))); ?></td>
                            <td>
                                <?php if($refund->order != null): ?>
                                    <?php echo e($refund->order->code); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($refund->orderDetail != null && $refund->orderDetail->product != null): ?>
                                    <?php echo e($refund->orderDetail->product->getTranslation('name')); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($refund->orderDetail != null): ?>
                                    <?php echo e(single_price($refund->orderDetail->price)); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($refund->refund_status == 1): ?>
                                  <span class="badge badge-inline badge-success"><strong><?php echo e(translate('Approved')); ?></strong></span>
                                <?php elseif($refund->refund_status == 2): ?>
                                  <span class="badge badge-inline badge-danger"><strong><?php echo e(translate('Rejected')); ?></strong></span>
                                <?php else: ?>
                                  <span class="badge badge-inline badge-warning"><strong><?php echo e(translate('PENDING')); ?></strong></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('reason_show', $refund->id)); ?>"><span class="badge badge-inline badge-success"><?php echo e(translate('Show')); ?></span></a>
                            </td>
                            <td>
                              <?php if($refund->refund_status != 2 && $refund->seller_approval != 2): ?>
                                <?php if($refund->seller_approval == 1): ?>
                                    <label class="aiz-switch aiz-switch-success mb-0 ">
                                        <input type="checkbox" <?php if($refund->seller_approval == 1): ?> checked  <?php endif; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                <?php else: ?>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_refund_approval('<?php echo e($refund->id); ?>')" type="checkbox" <?php if($refund->seller_approval == 1): ?> checked <?php endif; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                <?php endif; ?>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if($refund->refund_status == 0 && $refund->seller_approval == 0): ?>
                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="reject_refund_request(<?php echo e($refund->id); ?>)" title="<?php echo e(translate('Reject Refund Request')); ?>">
                                    <i class="las la-trash"></i>
                                </a>
                              <?php elseif($refund->seller_approval == 2 || $refund->refund_status == 2): ?>
                                <a href="javascript:void(0);" onclick="refund_reject_reason_show('<?php echo e(route('reject_reason_show', $refund->id )); ?>')" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="<?php echo e(translate('Reject Reason')); ?>">
                                    <i class="las la-eye"></i>
                                </a>
                              <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($refunds->links()); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
  <div class="modal fade reject_refund_request" id="modal-basic">
    	<div class="modal-dialog">
    		<div class="modal-content">
            <form class="form-horizontal member-block" action="<?php echo e(route('reject_refund_request')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="refund_id" id="refund_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title h6"><?php echo e(translate('Reject Refund Request !')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label"><?php echo e(translate('Reject Reason')); ?></label>
                        <div class="col-md-9">
                            <textarea type="text" name="reject_reason" rows="5" class="form-control" placeholder="<?php echo e(translate('Reject Reason')); ?>" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Close')); ?></button>
                    <button type="submit" class="btn btn-success"><?php echo e(translate('Submit')); ?></button>
                </div>
            </form>
      	</div>
    	</div>
    </div>
    <div class="modal fade reject_reason_show_modal" id="modal-basic">
    	<div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title h6"><?php echo e(translate('Refund Request Reject Reason')); ?></h5>
                  <button type="button" class="close" data-dismiss="modal"></button>
              </div>
              <div class="modal-body reject_reason_show">
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Close')); ?></button>
              </div>
          </div>
    	</div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">

        function update_refund_approval(el){
            $.post('<?php echo e(route('vendor_refund_approval')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', el:el}, function(data){
                if (data == 1) {
                    AIZ.plugins.notify('success', '<?php echo e(translate('Approval has been done successfully')); ?>');
                }
                else {
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function reject_refund_request(id) {
           $('.reject_refund_request').modal('show');
           $('#refund_id').val(id);
        }

        function refund_reject_reason_show(url){
            $.get(url, function(data){
                 $('.reject_reason_show').html(data);
                 $('.reject_reason_show_modal').modal('show');
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/refund_request/frontend/recieved_refund_request/index.blade.php ENDPATH**/ ?>