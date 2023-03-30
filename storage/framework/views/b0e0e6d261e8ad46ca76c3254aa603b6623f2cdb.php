<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(translate('INVOICE')); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
	<style media="all">
        @page  {
			margin: 0;
			padding:0;
		}
		body{
			font-size: 0.875rem;
            font-family: '<?php echo  $font_family ?>';
            font-weight: normal;
            direction: <?php echo  $direction ?>;
            text-align: <?php echo  $text_align ?>;
			padding:0;
			margin:0; 
		}
		.gry-color *,
		.gry-color{
			color:#000;
		}
		table{
			width: 100%;
		}
		table th{
			font-weight: normal;
		}
		table.padding th{
			padding: .25rem .7rem;
		}
		table.padding td{
			padding: .25rem .7rem;
		}
		table.sm-padding td{
			padding: .1rem .7rem;
		}
		.border-bottom td,
		.border-bottom th{
			border-bottom:1px solid #eceff4;
		}
		.text-left{
			text-align:<?php echo  $text_align ?>;
		}
		.text-right{
			text-align:<?php echo  $not_text_align ?>;
		}
	</style>
</head>
<body>
	<div>

		<?php
			$logo = get_setting('header_logo');
		?>

		<div style="background: #eceff4;padding: 1rem;">
			<table>
				<tr>
					<td>
						<?php if($logo != null): ?>
							<img src="<?php echo e(uploaded_asset($logo)); ?>" height="30" style="display:inline-block;">
						<?php else: ?>
							<img src="<?php echo e(static_asset('assets/img/logo.png')); ?>" height="30" style="display:inline-block;">
						<?php endif; ?>
					</td>
					<td style="font-size: 1.5rem;" class="text-right strong"><?php echo e(translate('INVOICE')); ?></td>
				</tr>
			</table>
			<table>
				<tr>
					<td style="font-size: 1rem;" class="strong"><?php echo e(get_setting('site_name')); ?></td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small"><?php echo e(get_setting('contact_address')); ?></td>
					<td class="text-right"></td>
				</tr>
				<tr>
					<td class="gry-color small"><?php echo e(translate('Email')); ?>: <?php echo e(get_setting('contact_email')); ?></td>
					<td class="text-right small"><span class="gry-color small"><?php echo e(translate('Order ID')); ?>:</span> <span class="strong"><?php echo e($order->code); ?></span></td>
				</tr>
				<tr>
					<td class="gry-color small"><?php echo e(translate('Phone')); ?>: <?php echo e(get_setting('contact_phone')); ?></td>
					<td class="text-right small"><span class="gry-color small"><?php echo e(translate('Order Date')); ?>:</span> <span class=" strong"><?php echo e(date('d-m-Y', $order->date)); ?></span></td>
				</tr>
			</table>

		</div>

		<div style="padding: 1rem;padding-bottom: 0">
            <table>
				<?php
					$shipping_address = json_decode($order->shipping_address);
				?>
				<tr><td class="strong small gry-color"><?php echo e(translate('Bill to')); ?>:</td></tr>
				<tr><td class="strong"><?php echo e($shipping_address->name); ?></td></tr>
				<tr><td class="gry-color small"><?php echo e($shipping_address->address); ?>, <?php echo e($shipping_address->city); ?>, <?php echo e($shipping_address->postal_code); ?>, <?php echo e($shipping_address->country); ?></td></tr>
				<tr><td class="gry-color small"><?php echo e(translate('Email')); ?>: <?php echo e($shipping_address->email); ?></td></tr>
				<tr><td class="gry-color small"><?php echo e(translate('Phone')); ?>: <?php echo e($shipping_address->phone); ?></td></tr>
			</table>
		</div>

	    <div style="padding: 1rem;">
			<table class="padding text-left small border-bottom">
				<thead>
	                <tr class="gry-color" style="background: #eceff4;">
	                    <th width="35%" class="text-left"><?php echo e(translate('Product Name')); ?></th>
						<th width="15%" class="text-left"><?php echo e(translate('Delivery Type')); ?></th>
	                    <th width="10%" class="text-left"><?php echo e(translate('Qty')); ?></th>
	                    <th width="15%" class="text-left"><?php echo e(translate('Unit Price')); ?></th>
	                    <th width="10%" class="text-left"><?php echo e(translate('Tax')); ?></th>
	                    <th width="15%" class="text-right"><?php echo e(translate('Total')); ?></th>
	                </tr>
				</thead>
				<tbody class="strong">
	                <?php $__currentLoopData = $order->orderDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $orderDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                <?php if($orderDetail->product != null): ?>
							<tr class="">
								<td><?php echo e($orderDetail->product->name); ?> <?php if($orderDetail->variation != null): ?> (<?php echo e($orderDetail->variation); ?>) <?php endif; ?></td>
								<td>
									<?php if($order->shipping_type != null && $order->shipping_type == 'home_delivery'): ?>
										<?php echo e(translate('Home Delivery')); ?>

									<?php elseif($order->shipping_type == 'pickup_point'): ?>
										<?php if($order->pickup_point != null): ?>
											<?php echo e($order->pickup_point->getTranslation('name')); ?> (<?php echo e(translate('Pickip Point')); ?>)
										<?php endif; ?>
									<?php endif; ?>
								</td>
								<td class=""><?php echo e($orderDetail->quantity); ?></td>
								<td class="currency"><?php echo e(single_price($orderDetail->price/$orderDetail->quantity)); ?></td>
								<td class="currency"><?php echo e(single_price($orderDetail->tax/$orderDetail->quantity)); ?></td>
			                    <td class="text-right currency"><?php echo e(single_price($orderDetail->price+$orderDetail->tax)); ?></td>
							</tr>
		                <?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	            </tbody>
			</table>
		</div>

	    <div style="padding:0 1.5rem;">
	        <table class="text-right sm-padding small strong">
	        	<thead>
	        		<tr>
	        			<th width="60%"></th>
	        			<th width="40%"></th>
	        		</tr>
	        	</thead>
		        <tbody>
			        <tr>
			            <td class="text-left">
                            <?php
                                $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                            ?>
                            <?php echo str_replace($removedXML,"", QrCode::size(100)->generate($order->code)); ?>

			            </td>
			            <td>
					        <table class="text-right sm-padding small strong">
						        <tbody>
							        <tr>
							            <th class="gry-color text-left"><?php echo e(translate('Sub Total')); ?></th>
							            <td class="currency"><?php echo e(single_price($order->orderDetails->sum('price'))); ?></td>
							        </tr>
							        <tr>
							            <th class="gry-color text-left"><?php echo e(translate('Shipping Cost')); ?></th>
							            <td class="currency"><?php echo e(single_price($order->orderDetails->sum('shipping_cost'))); ?></td>
							        </tr>
							        <tr class="border-bottom">
							            <th class="gry-color text-left"><?php echo e(translate('Total Tax')); ?></th>
							            <td class="currency"><?php echo e(single_price($order->orderDetails->sum('tax'))); ?></td>
							        </tr>
				                    <tr class="border-bottom">
							            <th class="gry-color text-left"><?php echo e(translate('Coupon Discount')); ?></th>
							            <td class="currency"><?php echo e(single_price($order->coupon_discount)); ?></td>
							        </tr>
							        <tr>
							            <th class="text-left strong"><?php echo e(translate('Grand Total')); ?></th>
							            <td class="currency"><?php echo e(single_price($order->grand_total)); ?></td>
							        </tr>
						        </tbody>
						    </table>
			            </td>
			        </tr>
		        </tbody>
		    </table>
	    </div>

	</div>
</body>
</html>
<?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/invoices/invoice.blade.php ENDPATH**/ ?>