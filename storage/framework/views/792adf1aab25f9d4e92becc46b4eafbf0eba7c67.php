<div class="card-columns">
    <?php $__currentLoopData = \App\Utility\CategoryUtility::get_immediate_children_ids($category->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $first_level_id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card shadow-none border-0">
            <ul class="list-unstyled mb-3">
                <li class="fw-600 border-bottom pb-2 mb-3">
                    <a class="text-reset" href="<?php echo e(route('products.category', \App\Models\Category::find($first_level_id)->slug)); ?>"><?php echo e(\App\Models\Category::find($first_level_id)->getTranslation('name')); ?></a>
                </li>
                <?php $__currentLoopData = \App\Utility\CategoryUtility::get_immediate_children_ids($first_level_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $second_level_id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="mb-2">
                        <a class="text-reset" href="<?php echo e(route('products.category', \App\Models\Category::find($second_level_id)->slug)); ?>"><?php echo e(\App\Models\Category::find($second_level_id)->getTranslation('name')); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH D:\wamp64\www\alicrossborder.com\resources\views/frontend/partials/category_elements.blade.php ENDPATH**/ ?>