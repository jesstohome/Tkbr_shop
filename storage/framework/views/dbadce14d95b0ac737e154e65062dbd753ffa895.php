<?php $__env->startSection('content'); ?>

    <?php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    ?>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3"><?php echo e(translate('All products')); ?></h1>
        </div>
        <?php if($type != 'Seller'): ?>
        <div class="col text-right">
            <a href="<?php echo e(route('products.create')); ?>" class="btn btn-circle btn-info">
                <span><?php echo e(translate('Add New Product')); ?></span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<br>

<div class="card">
    <form class="form" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6"><?php echo e(translate('All Product')); ?></h5>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    <?php echo e(translate('Bulk Action')); ?>

                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"> <?php echo e(translate('Delete selection')); ?></a>
                    <?php if($type == 'In House'): ?><a class="dropdown-item" href="#" onclick="bulk_add_to_storehouse()"> <?php echo e(translate('Add selection to storehouse')); ?></a><?php endif; ?>
                </div>
            </div>

            <?php if($type == 'Seller'): ?>
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                    <option value=""><?php echo e(translate('All Sellers')); ?></option>
                    <?php $__currentLoopData = App\Models\User::where('user_type', '=', 'seller')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($seller->id); ?>" <?php if($seller->id == $seller_id): ?> selected <?php endif; ?>>
                            <?php echo e($seller->shop->name); ?> (<?php echo e($seller->name); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <?php if($type == 'All'): ?>
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" name="user_id" onchange="sort_products()">
                    <option value=""><?php echo e(translate('All Sellers')); ?></option>
                        <?php $__currentLoopData = App\Models\User::where('user_type', '=', 'admin')->orWhere('user_type', '=', 'seller')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($seller->id); ?>" <?php if($seller->id == $seller_id): ?> selected <?php endif; ?>><?php echo e($seller->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type" onchange="sort_products()">
                    <option value=""><?php echo e(translate('Sort By')); ?></option>
                    <option value="rating,desc" <?php if(isset($col_name , $query)): ?> <?php if($col_name == 'rating' && $query == 'desc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Rating (High > Low)')); ?></option>
                    <option value="rating,asc" <?php if(isset($col_name , $query)): ?> <?php if($col_name == 'rating' && $query == 'asc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Rating (Low > High)')); ?></option>
                    <option value="num_of_sale,desc"<?php if(isset($col_name , $query)): ?> <?php if($col_name == 'num_of_sale' && $query == 'desc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Num of Sale (High > Low)')); ?></option>
                    <option value="num_of_sale,asc"<?php if(isset($col_name , $query)): ?> <?php if($col_name == 'num_of_sale' && $query == 'asc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Num of Sale (Low > High)')); ?></option>
                    <option value="unit_price,desc"<?php if(isset($col_name , $query)): ?> <?php if($col_name == 'unit_price' && $query == 'desc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Base Price (High > Low)')); ?></option>
                    <option value="unit_price,asc"<?php if(isset($col_name , $query)): ?> <?php if($col_name == 'unit_price' && $query == 'asc'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Base Price (Low > High)')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"<?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type & Enter')); ?>">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <!--<th data-breakpoints="lg">#</th>-->
                        <th><?php echo e(translate('Name')); ?></th>
                        <?php if($type == 'Seller' || $type == 'All'): ?>
                            <th data-breakpoints="lg"><?php echo e(translate('Added By')); ?></th>
                        <?php endif; ?>
                        <th data-breakpoints="sm"><?php echo e(translate('Info')); ?></th>
                        <th data-breakpoints="md"><?php echo e(translate('Total Stock')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Todays Deal')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Published')); ?></th>
                        <?php if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller'): ?>
                            <th data-breakpoints="lg"><?php echo e(translate('Approved')); ?></th>
                        <?php endif; ?>
                        <th data-breakpoints="lg"><?php echo e(translate('Featured')); ?></th>
                        <?php if($type == 'In House'): ?>
                            <th data-breakpoints="lg"><?php echo e(translate('In storehouse')); ?></th>
                        <?php endif; ?>
                        <th data-breakpoints="sm" class="text-right"><?php echo e(translate('Options')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <!--<td><?php echo e(($key+1) + ($products->currentPage() - 1)*$products->perPage()); ?></td>-->
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="<?php echo e($product->id); ?>">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="<?php echo e(uploaded_asset($product->thumbnail_img)); ?>" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2"><?php echo e($product->getTranslation('name')); ?></span>
                                </div>
                            </div>
                        </td>
                        <?php if($type == 'Seller' || $type == 'All'): ?>
                            <td><?php echo e($product->user->name); ?></td>
                        <?php endif; ?>
                        <td>
                            <strong><?php echo e(translate('Num of Sale')); ?>:</strong> <?php echo e($product->num_of_sale); ?> <?php echo e(translate('times')); ?> </br>
                            <strong><?php echo e(translate('Base Price')); ?>:</strong> <?php echo e(single_price($product->unit_price)); ?> </br>
                            <strong><?php echo e(translate('Rating')); ?>:</strong> <?php echo e($product->rating); ?> </br>
                        </td>
                        <td>
                            <?php
                                $qty = 0;
                                if($product->variant_product) {
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                        echo $stock->variant.' - '.$stock->qty.'<br>';
                                    }
                                }
                                else {
                                    //$qty = $product->current_stock;
                                    $qty = optional($product->stocks->first())->qty;
                                    echo $qty;
                                }
                            ?>
                            <?php if($qty <= $product->low_stock_quantity): ?>
                                <span class="badge badge-inline badge-danger">Low</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if ($product->todays_deal == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if ($product->published == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <?php if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller'): ?>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if ($product->approved == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        <?php endif; ?>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_featured(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if ($product->featured == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <?php if($type == 'In House'): ?>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_in_storehouse(this)" value="<?php echo e($product->id); ?>" type="checkbox" <?php if ($product->in_storehouse == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        <?php endif; ?>
                        <td class="text-right">
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="product_review('<?php echo e($product->id); ?>')" title="<?php echo e(translate('Review')); ?>">
                                <?php echo e(translate('Review')); ?>

                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="<?php echo e(route('product', $product->slug)); ?>" target="_blank" title="<?php echo e(translate('View')); ?>">
                                <i class="las la-eye"></i>
                            </a>
                            <?php if($type == 'Seller'): ?>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )); ?>" title="<?php echo e(translate('Edit')); ?>">
                                <i class="las la-edit"></i>
                            </a>
                            <?php else: ?>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="<?php echo e(route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )); ?>" title="<?php echo e(translate('Edit')); ?>">
                                <i class="las la-edit"></i>
                            </a>
                            <?php endif; ?>
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="<?php echo e(route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )); ?>" title="<?php echo e(translate('Duplicate')); ?>">
                                <i class="las la-copy"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('products.destroy', $product->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($products->appends(request()->input())->links()); ?>

            </div>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6"><?php echo e(translate('Review')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <form action="<?php echo e(route('reviews.store')); ?>"  enctype="multipart/form-data" method="POST">
                    <div id="product-review-modal-content">

                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="opacity-60"><?php echo e(translate('Product')); ?></label>
                            <p><?php echo e($product->getTranslation('name')); ?></p>
                        </div>
                        <div class="form-group">
                            <label class="opacity-60"><?php echo e(translate('Customer')); ?></label>
                            <div>
                                <?php
                                    $customers = \App\Models\User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc')->get();
                                ?>
                                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>" data-contact="<?php echo e($customer->email); ?>">
                                            <?php echo e($customer->name); ?> <?php if($customer->is_virtual == 1): ?> (<font color="red"><?php echo e(translate('Virtual')); ?></font>) <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="opacity-60"><?php echo e(translate('Rating')); ?></label>
                            <div class="rating rating-input">
                                <label> <input type="radio" name="rating" value="1" required> <i class="las la-star"></i> </label>
                                <label> <input type="radio" name="rating" value="2"> <i class="las la-star"></i> </label> <label>
                                    <input type="radio" name="rating" value="3"> <i class="las la-star"></i> </label> <label>
                                    <input type="radio" name="rating" value="4"> <i class="las la-star"></i> </label> <label>
                                    <input type="radio" name="rating" value="5"> <i class="las la-star"></i> </label>
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

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        function product_review(product_id) {
            $.post('<?php echo e(route('product_admin_review_modal')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                product_id: product_id
            }, function(data) {
                $('#product-review-modal-content').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        }

        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function update_todays_deal(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('products.todays_deal')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Todays Deal updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
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
            $.post('<?php echo e(route('products.published')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Published products updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function update_approved(el){
            if(el.checked){
                var approved = 1;
            }
            else{
                var approved = 0;
            }
            $.post('<?php echo e(route('products.approved')); ?>', {
                _token      :   '<?php echo e(csrf_token()); ?>',
                id          :   el.value,
                approved    :   approved
            }, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Product approval update successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('products.featured')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Featured products updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function update_in_storehouse(el){
            if(el.checked){
                var in_storehouse = 1;
            }
            else{
                var in_storehouse = 0;
            }
            $.post('<?php echo e(route('product-storehouse-update')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, in_storehouse:in_storehouse}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Products storehouse updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function sort_products(el){
            $('#sort_products').submit();
        }

        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('bulk-product-delete')); ?>",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function bulk_add_to_storehouse() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('bulk-product-storehouse-add')); ?>",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response)
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\wamp64\www\alicrossborder.com\resources\views/backend/product/products/index.blade.php ENDPATH**/ ?>