<a href="<?php echo e(route('wishlists.index')); ?>" class="d-flex align-items-center text-reset">
    <i class="la la-heart-o la-2x opacity-80"></i>
    <span class="flex-grow-1 ml-1">
        <?php if(Auth::check()): ?>
            <span class="badge badge-primary badge-inline badge-pill"><?php echo e(count(Auth::user()->wishlists)); ?></span>
        <?php else: ?>
            <span class="badge badge-primary badge-inline badge-pill">0</span>
        <?php endif; ?>
        <span class="nav-box-text d-none d-xl-block opacity-70"><?php echo e(translate('Wishlist')); ?></span>
    </span>
</a>
<?php /**PATH D:\wamp64\www\alicrossborder.com\resources\views/frontend/partials/wishlist.blade.php ENDPATH**/ ?>