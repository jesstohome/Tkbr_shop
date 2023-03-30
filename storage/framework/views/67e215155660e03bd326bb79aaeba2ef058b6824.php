<div class="modal-header">
    <h5 class="modal-title h6"><?php echo e(translate('Review')); ?></h5>
    <button type="button" class="close" data-dismiss="modal">
    </button>
</div>

<?php if($review == null): ?>
    <form action="<?php echo e(route('reviews.store')); ?>" method="POST" >
        <?php echo csrf_field(); ?>
        <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
        <div class="modal-body">
            <div class="form-group">
                <label class="opacity-60"><?php echo e(translate('Product')); ?></label>
                <p><?php echo e($product->getTranslation('name')); ?></p>
            </div>
            <div class="form-group">
                <label class="opacity-60"><?php echo e(translate('Rating')); ?></label>
                <div class="rating rating-input">
                    <label>
                        <input type="radio" name="rating" value="1" required>
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="2">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="3">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="4">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="5">
                        <i class="las la-star"></i>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="opacity-60"><?php echo e(translate('Comment')); ?></label>
                <textarea class="form-control" rows="4" name="comment" placeholder="<?php echo e(translate('Your review')); ?>" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Submit review')); ?></button>
            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
        </div>
    </form>
<?php else: ?>
<li class="media list-group-item d-flex">
    <div class="media-body text-left">
        <div class="form-group">
            <label class="opacity-60"><?php echo e(translate('Rating')); ?></label>
            <p class="rating rating-sm">
                <?php for($i=0; $i < $review->rating; $i++): ?>
                    <i class="las la-star active"></i>
                <?php endfor; ?>
                <?php for($i=0; $i < 5-$review->rating; $i++): ?>
                    <i class="las la-star"></i>
                <?php endfor; ?>
            </p>
        </div>
        <div class="form-group">
            <label class="opacity-60"><?php echo e(translate('Comment')); ?></label>
            <p class="comment-text">
                <?php echo e($review->comment); ?>

            </p>
        </div>
    </div>
</li>
<?php endif; ?>

<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/user/product_review_modal.blade.php ENDPATH**/ ?>