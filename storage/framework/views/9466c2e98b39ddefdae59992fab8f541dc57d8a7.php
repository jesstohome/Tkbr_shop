<?php $__env->startSection('panel_content'); ?>

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Products')); ?></h1>
        </div>
      </div>
    </div>

    <div class="row gutters-10 justify-content-center">
        <?php if(addon_is_activated('seller_subscription')): ?>
            <div class="col-md-4 mx-auto mb-3" >
                <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x text-white"></i>
                  </span>
                  <div class="px-3 pt-3 pb-3">
                      <div class="h4 fw-700 text-center"><?php echo e(max(0, auth()->user()->shop->product_upload_limit - auth()->user()->products()->count())); ?></div>
                      <div class="opacity-50 text-center"><?php echo e(translate('Remaining Uploads')); ?></div>
                  </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-4 mx-auto mb-3" >
            <a href="<?php echo e(route('seller.products.create')); ?>">
              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                      <i class="las la-plus la-3x text-white"></i>
                  </span>
                  <div class="fs-18 text-primary"><?php echo e(translate('Add New Product')); ?></div>
              </div>
            </a>
        </div>

        <?php if(addon_is_activated('seller_subscription')): ?>
        <?php
            $seller_package = \App\Models\SellerPackage::find(Auth::user()->shop->seller_package_id);
        ?>
        <div class="col-md-4">
            <a href="<?php echo e(route('seller.seller_packages_list')); ?>" class="text-center bg-white shadow-sm hov-shadow-lg text-center d-block p-3 rounded">
                <?php if($seller_package != null): ?>
                    <img src="<?php echo e(uploaded_asset($seller_package->logo)); ?>" height="44" class="mw-100 mx-auto">
                    <span class="d-block sub-title mb-2"><?php echo e(translate('Current Package')); ?>: <?php echo e($seller_package->getTranslation('name')); ?></span>
                <?php else: ?>
                    <i class="la la-frown-o mb-2 la-3x"></i>
                    <div class="d-block sub-title mb-2"><?php echo e(translate('No Package Found')); ?></div>
                <?php endif; ?>
                <div class="btn btn-outline-primary py-1"><?php echo e(translate('Upgrade Package')); ?></div>
            </a>
        </div>
        <?php endif; ?>

    </div>

    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6"><?php echo e(translate('All Products')); ?></h5>
            </div>
            <div class="col-md-4">
                <form class="" id="sort_brands" action="" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="search" name="search" <?php if(isset($search)): ?> value="<?php echo e($search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Search product')); ?>">
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="md">
                            <?php echo e(translate('Thumbnail Image')); ?>

                        </th>
                        <th width="30%"><?php echo e(translate('Name')); ?></th>
                        <th data-breakpoints="md"><?php echo e(translate('Category')); ?></th>
                        <th data-breakpoints="md"><?php echo e(translate('Current Qty')); ?></th>
                        <th><?php echo e(translate('Base Price')); ?></th>
                        <?php if(get_setting('product_approve_by_admin') == 1): ?>
                            <th data-breakpoints="md"><?php echo e(translate('Approval')); ?></th>
                        <?php endif; ?>
                        <th data-breakpoints="md"><?php echo e(translate('Published')); ?></th>
                        <th data-breakpoints="md"><?php echo e(translate('Featured')); ?></th>
                        <th data-breakpoints="md">直通车推广</th>
                        <th data-breakpoints="md" class="text-right"><?php echo e(translate('Options')); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(($key+1) + ($products->currentPage() - 1)*$products->perPage()); ?></td>
                            <td>
                                <img
                                    height="80px"
                                    class="lazyload"
                                    src="<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>"
                                    data-src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>"
                                    alt="<?php echo e($product->getTranslation('name')); ?>"
                                    onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/placeholder.jpg')); ?>';"
                                >
                            </td>
                            <td>
                                <a href="<?php echo e(route('product', $product->slug)); ?>" target="_blank" class="text-reset">
                                    <?php echo e($product->getTranslation('name')); ?>

                                </a>
                            </td>
                            <td>
                                <?php if($product->category != null): ?>
                                    <?php echo e($product->category->getTranslation('name')); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $qty = 0;
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                    }
                                    echo $qty;
                                ?>
                            </td>
                            <td><?php echo e($product->unit_price); ?></td>
                            <?php if(get_setting('product_approve_by_admin') == 1): ?>
                                <td>
                                    <?php if($product->approved == 1): ?>
                                        <span class="badge badge-inline badge-success"><?php echo e(translate('Approved')); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-inline badge-info"><?php echo e(translate('Pending')); ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_published(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_featured(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if($product->seller_featured == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <?php if( count($seller_spread_packages_payments)==0): ?>
                                        <?php if($product->seller_spread_package_payment->expire_at > time()): ?>
                                            <input type="checkbox" checked readonly disabled >
                                        <?php else: ?>
                                            <input onchange="toJump()" type="checkbox" >
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($product->seller_spread_package_payment->expire_at > time()): ?>
                                            <input type="checkbox" checked readonly disabled >
                                        <?php else: ?>
                                            <input onchange="select_spread_package(<?php echo e($product->id); ?>)" type="checkbox">
                                        <?php endif; ?>
                                    <?php endif; ?>


                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td class="text-right">
		                      <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="<?php echo e(route('seller.products.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')])); ?>" title="<?php echo e(translate('Edit')); ?>">
		                          <i class="las la-edit"></i>
		                      </a>
                              <a href="<?php echo e(route('seller.products.duplicate', $product->id)); ?>" class="btn btn-soft-success btn-icon btn-circle btn-sm"  title="<?php echo e(translate('Duplicate')); ?>">
    							   <i class="las la-copy"></i>
    						  </a>
                              <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('seller.products.destroy', $product->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                  <i class="las la-trash"></i>
                              </a>
                          </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($products->links()); ?>

          	</div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- 选择直通车 -->
    <div class="modal fade" id="select_payment_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form class="" id="package_payment_form" action="<?php echo e(route('seller.products.spread')); ?>" method="post">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('Select Spread Package')); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="product_id" value="">
                        <div class="row">
                            <div class="col-md-2">
                                <label><?php echo e(translate('Spread Package')); ?></label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker" name="seller_spread_package_payment_id"
                                        data-minimum-results-for-search="Infinity" required>
                                        <option value=""><?php echo e(translate('Select One')); ?></option>
                                        <?php $__currentLoopData = $seller_spread_packages_payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller_spread_packages_payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($seller_spread_packages_payment['id']); ?>"><?php echo e($seller_spread_packages_payment['seller_spread_package']['name']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1" id="select_type_cancel" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                            <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1"><?php echo e(translate('Confirm')); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('seller.products.featured')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Featured products updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                    location.reload();
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('seller.products.published')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Published products updated successfully')); ?>');
                }
                else if(data == 2){
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Please upgrade your package.')); ?>');
                    location.reload();
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                    location.reload();
                }
            });
        }

        function toJump(){
            AIZ.plugins.notify('danger', '请开通直通车');
            window.location.href = "<?php echo e(route('seller.seller_spread_packages_list')); ?>"
        }

        function select_spread_package(product_id){
            $('input[name=product_id]').val(product_id);
            $('#select_payment_type_modal').modal('show');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/product/products/index.blade.php ENDPATH**/ ?>