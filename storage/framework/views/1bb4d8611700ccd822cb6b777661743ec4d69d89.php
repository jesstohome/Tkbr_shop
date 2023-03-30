<div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
    <?php
        $subtotal = 0;
        $tax = 0;
    ?>
    <?php if(Session::has('pos.cart')): ?>
        <ul class="list-group list-group-flush">
        <?php $__empty_1 = true; $__currentLoopData = Session::get('pos.cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $stock = \App\Models\ProductStock::find($cartItem['stock_id']);
                if ($stock){
                $subtotal += $cartItem['price']*$cartItem['quantity'];
                $tax += $cartItem['tax']*$cartItem['quantity'];
                    }
            ?>
                <?php if($stock): ?>
            <li class="list-group-item py-0 pl-2">
                <div class="row gutters-5 align-items-center">
                    <div class="col-auto w-60px">
                        <div class="row no-gutters align-items-center flex-column aiz-plus-minus">
                            <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="plus" data-field="qty-<?php echo e($key); ?>">
                                <i class="las la-plus"></i>
                            </button>
                            <input type="text" name="qty-<?php echo e($key); ?>" id="qty-<?php echo e($key); ?>" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="<?php echo e($cartItem['quantity']); ?>" min="<?php echo e($stock->product->min_qty); ?>" max="<?php echo e($stock->qty); ?>" onchange="updateQuantity(<?php echo e($key); ?>)">
                            <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="minus" data-field="qty-<?php echo e($key); ?>">
                                <i class="las la-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-truncate-2"><?php echo e($stock->product->name); ?></div>
                        <span class="span badge badge-inline fs-12 badge-soft-secondary"><?php echo e($cartItem['variant']); ?></span>
                    </div>
                    <div class="col-auto">
                        <div class="fs-12 opacity-60"><?php echo e(single_price($cartItem['price'])); ?> x <?php echo e($cartItem['quantity']); ?></div>
                        <div class="fs-15 fw-600"><?php echo e(single_price($cartItem['price']*$cartItem['quantity'])); ?></div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0" onclick="removeFromCart(<?php echo e($key); ?>)">
                            <i class="las la-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </li>
                <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li class="list-group-item">
                <div class="text-center">
                    <i class="las la-frown la-3x opacity-50"></i>
                    <p><?php echo e(translate('No Product Added')); ?></p>
                </div>
            </li>
        <?php endif; ?>
        </ul>
    <?php else: ?>
        <div class="text-center">
            <i class="las la-frown la-3x opacity-50"></i>
            <p><?php echo e(translate('No Product Added')); ?></p>
        </div>
    <?php endif; ?>
</div>
<div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span><?php echo e(translate('Sub Total')); ?></span>
        <span><?php echo e(single_price($subtotal)); ?></span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span><?php echo e(translate('Tax')); ?></span>
        <span><?php echo e(single_price($tax)); ?></span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span><?php echo e(translate('Shipping')); ?></span>
        <span><?php echo e(single_price(Session::get('pos.shipping', 0))); ?></span>
    </div>
    <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
        <span><?php echo e(translate('Discount')); ?></span>
        <span><?php echo e(single_price(Session::get('pos.discount', 0))); ?></span>
    </div>
    <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
        <span><?php echo e(translate('Total')); ?></span>
        <span><?php echo e(single_price($subtotal+$tax+Session::get('pos.shipping', 0) - Session::get('pos.discount', 0))); ?></span>
    </div>
</div>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/pos/cart.blade.php ENDPATH**/ ?>