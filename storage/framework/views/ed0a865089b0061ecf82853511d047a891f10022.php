<?php $__env->startSection('panel_content'); ?>
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('All Sellers')); ?></h1>
        </div>
    </div>
    <div class="col text-right">
        <a href="<?php echo e(route('shops.create')); ?>" class="btn btn-circle btn-info">
            <span><?php echo e(translate('Add Virtual Seller')); ?></span>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-md-0 h6"><?php echo e(translate('Sellers')); ?></h5>
        </div>
        <div class="col-md-4">
            <form class="" id="sort_brands" action="" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search" <?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type name or email & Enter')); ?>">
                </div>
            </form>
        </div>
    </div>


    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
            <tr>
                <th><?php echo e(translate('Name')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Phone')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Email Address')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Verification Info')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Approval')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Num. of Products')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Pending Balance')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Wallet Money')); ?></th>
                <th data-breakpoints="lg"><?php echo e(translate('Views')); ?></th>
                <th width="10%"><?php echo e(translate('Options')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php if($shop->user->banned == 1): ?> <i class="fa fa-ban text-danger" aria-hidden="true"></i> <?php endif; ?> <?php echo e($shop->name); ?><?php if($shop->user->is_virtual == 1): ?> (<font color="red"><?php echo e(translate('Virtual')); ?></font>) <?php endif; ?></td>
                    <td><?php echo e($shop->user->phone); ?></td>
                    <td><?php echo e($shop->user->email); ?></td>
                    <td>
                        <?php if($shop->verification_info != null): ?>
                            <a href="<?php echo e(route('sellers.show_verification_request', $shop->id)); ?>">
                                <span class="badge badge-inline badge-info"><?php echo e(translate('Show')); ?></span>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <?php if(get_setting('salesman_reviews_switch') == 1): ?>
                                <input onchange="update_approved(this)" value="<?php echo e($shop->id); ?>" type="checkbox" <?php if($shop->verification_status == 1) echo "checked";?> >
                            <?php else: ?>
                            <input disabled readonly value="<?php echo e($shop->id); ?>" type="checkbox" <?php if($shop->verification_status == 1) echo "checked";?> >
                            <?php endif; ?>
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td><?php echo e($shop->user->products->count()); ?></td>
                    <td>
                        <?php if($shop->admin_to_pay >= 0): ?>
                            <?php echo e(single_price($shop->admin_to_pay)); ?>

                        <?php else: ?>
                            <?php echo e(single_price(abs($shop->admin_to_pay))); ?> (<?php echo e(translate('Due to Admin')); ?>)
                        <?php endif; ?>
                    </td>
                    <td  >
                        <?php echo e(single_price($shop->user->balance)); ?>

                    </td>
                    
                      <td  >
                          <?php echo e(translate('base num')); ?>：<?php echo e($shop->view_base_num); ?>

                            <br> 
                           <?php echo e(translate('inc num')); ?>：<?php echo e($shop->view_inc_num); ?>

                        </td>
                        
                        
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-circle btn-soft-primary btn-icon dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="las la-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                <a href="#" onclick="show_seller_profile('<?php echo e($shop->id); ?>');"  class="dropdown-item">
                                    <?php echo e(translate('Profile')); ?>

                                </a>

                                <span onclick="show_chat_modal(<?php echo e($shop->user->id); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Message Seller')); ?>

                                    </span>
                                    
                                    <span onclick="show_view(<?php echo e($shop->id); ?>,<?php echo e($shop->view_inc_num); ?>,<?php echo e($shop->view_base_num); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Views')); ?>

                                    </span>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($shops->appends(request()->input())->links()); ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5"><?php echo e(translate('Any query about this seller')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('conversations.salesman_store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="receiver_id" id="receiver_id" value="">
                    <div class="modal-body gry-bg px-3 pt-3">
                        
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="title" required
                                placeholder="<?php echo e(translate('Title')); ?>"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600"
                            data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                        <button type="submit" class="btn btn-primary fw-600"><?php echo e(translate('Send')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.5.1/layer.min.js"></script>
    <script type="text/javascript">
    
    function show_view(shop_id,view_inc_num,view_base_num) {
         
          
          var content = ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 基础访问量：</span>'
            +'<input id="base_num" type="text" value="'+view_base_num+'" class="form-control" placeholder="基础访问量">'
            +'</div>'
            +'</div>'
             
               +'<div class="col-sm-12" style="margin-top:3px;">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 每日递增量：</span>'
            +'<input id="inc_num" type="text" value="'+view_inc_num+'" class="form-control" placeholder="每日递增">'
            +'</div>'
            +'</div>'
           
              +'</div>';  
            
            layer.open({
      
        type: 1,
        title:'访问量',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],
        
        content: content,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var inc_num = $("#inc_num").val();
            var base_num = $("#base_num").val();
            
             $.post('<?php echo e(route('sellers.setviews')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', shop_id:shop_id,inc_num:inc_num,base_num:base_num}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });
                },'json');
            
        },
        btn2:function (index,layero) {
             layer.close(index);
        }
 
    });
    } 
    
    
    
        function show_seller_profile(id){
            $.post('<?php echo e(route('salesman.sellers_profile_modal')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', id:id}, function(data){
                $('#profile_modal #profile-modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }
        function sort_sellers(el){
            $('#sort_sellers').submit();
        }

        function show_chat_modal(receiver_id) {
            $('#receiver_id').val(receiver_id);
            $('#chat_modal').modal('show');
        }

        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('salesman.sellers.approved')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Approved sellers updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('salesman.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/salesman/sellers/index.blade.php ENDPATH**/ ?>