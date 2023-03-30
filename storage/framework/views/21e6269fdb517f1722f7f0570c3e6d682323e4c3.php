

<?php $__env->startSection('panel_content'); ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Product Reviews')); ?></h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo e(translate('Product')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Customer')); ?></th>
                        <th><?php echo e(translate('Rating')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Comment')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Published')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $review = \App\Models\Review::find($value->id);
                        ?>
                        <?php if($review != null && $review->product != null && $review->user != null): ?>
                            <tr>
                                <td>
                                    <?php echo e($key+1); ?>

                                </td>
                                <td>
                                    <a href="<?php echo e(route('product', $review->product->slug)); ?>" target="_blank"><?php echo e($review->product->getTranslation('name')); ?></a>
                                </td>
                                <td><?php echo e($review->user->name); ?></td>
                                <td>
                                    <span class="rating rating-sm">
                                        <?php for($i=0; $i < $review->rating; $i++): ?>
                                            <i class="las la-star active"></i>
                                        <?php endfor; ?>
                                        <?php for($i=0; $i < 5-$review->rating; $i++): ?>
                                            <i class="las la-star"></i>
                                        <?php endfor; ?>
                                    </span>
                                </td>
                                <td><?php echo e($review->comment); ?></td>
                                <td>
                                    <?php if($review->status == 1): ?>
                                        <span class="badge badge-inline badge-success"><?php echo e(translate('Published')); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-inline badge-danger"><?php echo e(translate('Unpublished')); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($reviews->links()); ?>

          	</div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/reviews.blade.php ENDPATH**/ ?>