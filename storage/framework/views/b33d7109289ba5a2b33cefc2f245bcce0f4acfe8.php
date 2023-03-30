<div class="row">
	<div class="col-xl-6">
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
	            <li class="list-group-item px-0">
	                <div class="row gutters-10 align-items-center">
	                    <div class="col">
	                    	<div class="d-flex">
	                    		<?php if($stock->image == null): ?>
	                    			<img src="<?php echo e(uploaded_asset($stock->product->thumbnail_img)); ?>" class="img-fit size-60px">
	                    		<?php else: ?>
	                    			<img src="<?php echo e(uploaded_asset($stock->image)); ?>" class="img-fit size-60px">
	                    		<?php endif; ?>
	                    		<span class="flex-grow-1 ml-3 mr-0">
			                        <div class="text-truncate-2"><?php echo e($stock->product->name); ?></div>
			                        <span class="span badge badge-inline fs-12 badge-soft-secondary"><?php echo e($cartItem['variant']); ?></span>
	                    		</span>
	                    	</div>
	                    </div>
	                    <div class="col-xl-3">
	                        <div class="fs-14 fw-600 text-right"><?php echo e(single_price($cartItem['price'])); ?></div>
	                        <div class="fs-14 text-right"><?php echo e(translate('QTY')); ?>: <?php echo e($cartItem['quantity']); ?></div>
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
	<div class="col-xl-6">
		<div class="pl-xl-4">
			<div class="card mb-4">
				<div class="card-header"><span class="fs-16"><?php echo e(translate('Customer Info')); ?></span></div>
				<div class="card-body">
					<?php if(Session::has('pos.shipping_info') && Session::get('pos.shipping_info')['name'] != null): ?>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Name')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['name']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Email')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['email']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Phone')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['phone']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Address')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['address']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Country')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['country']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('City')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['city']); ?></span>
						</div>
						<div class="d-flex justify-content-between  mb-2">
							<span class=""><?php echo e(translate('Postal Code')); ?>:</span>
							<span class="fw-600"><?php echo e(Session::get('pos.shipping_info')['postal_code']); ?></span>
						</div>
					<?php else: ?>
						<div class="text-center p-4">
							<?php echo e(translate('No customer information selected.')); ?>

						</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
		        <span><?php echo e(translate('Total')); ?></span>
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
	</div>
</div>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/pos/order_summary.blade.php ENDPATH**/ ?>