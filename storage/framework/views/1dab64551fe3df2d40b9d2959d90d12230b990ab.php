<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('All Sellers')); ?></h1>
        </div>
        <div class="col text-right">
            <a href="<?php echo e(route('shops.create')); ?>" class="btn btn-circle btn-info">
                <span><?php echo e(translate('Add Virtual Seller')); ?></span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    <?php echo e(translate('Bulk Action')); ?>

                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"><?php echo e(translate('Delete selection')); ?></a>
                </div>
            </div>

            <?php
                $salesmans = \App\Models\User::where('user_type', 'salesman')->orderBy('created_at', 'desc')->get();
            ?>

            <div class="col-md-2 ml-auto">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($date); ?>" name="date" placeholder="<?php echo e(translate('Filter by date')); ?>" data-format="Y-MM-DD" data-separator=" to " data-advanced-range="true" autocomplete="on">
                </div>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="is_virtual_user" id="is_virtual_user" onchange="sort_sellers()">
                    <option value=""><?php echo e(translate('All')); ?></option>
                    <option value="1"  <?php if(isset($is_virtual_user)): ?> <?php if($is_virtual_user == '1'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Virtual Account')); ?></option>
                    <option value="0"  <?php if(isset($is_virtual_user)): ?> <?php if($is_virtual_user == '0'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('General Account')); ?></option>
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="sort_sellers()">
                    <option value=""><?php echo e(translate('All Ssalesman')); ?></option>
                    <?php $__currentLoopData = $salesmans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($salesman->id); ?>" <?php if($user_id == $salesman->id): ?> selected <?php endif; ?> data-contact="<?php echo e($salesman->email); ?>">
                            <?php echo e($salesman->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="approved_status" id="approved_status" onchange="sort_sellers()">
                    <option value=""><?php echo e(translate('Filter by Approval')); ?></option>
                    <option value="1"  <?php if(isset($approved)): ?> <?php if($approved == 'paid'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Approved')); ?></option>
                    <option value="0"  <?php if(isset($approved)): ?> <?php if($approved == 'unpaid'): ?> selected <?php endif; ?> <?php endif; ?>><?php echo e(translate('Non-Approved')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="search" name="search"<?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type name or email & Enter')); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-styled"><?php echo e(translate('Search')); ?></button>
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
                    <th><?php echo e(translate('Name')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Phone')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Email Address')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Verification Info')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Approval')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Num. of Products')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Pending Balance')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Creditscore')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Wallet Money')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Guarantee Money')); ?></th>
                    <th data-breakpoints="lg" style="width:20%;"><?php echo e(translate('Views')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Comment Permission')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Home Display')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Total recharge')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Total withdrawal amount')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Recharge difference')); ?></th>
                    <th data-breakpoints="lg"><?php echo e(translate('Salesman')); ?></th>
                    <th width="10%"><?php echo e(translate('Options')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $total_recharge = '0.00';
                    $total_withdraw_money = '0.00';
                    $total_difference = '0.00';
                ?>
                <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="<?php echo e($shop->id); ?>">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td><?php if($shop->user->banned == 1): ?> <i class="fa fa-ban text-danger" aria-hidden="true"></i> <?php endif; ?> <?php echo e($shop->name); ?> <?php if($shop->user->is_virtual == 1): ?> (<font color="red"><?php echo e(translate('Virtual')); ?></font>) <?php endif; ?></td>
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
                                <input onchange="update_approved(this)" value="<?php echo e($shop->id); ?>" type="checkbox" <?php if($shop->verification_status == 1) echo "checked";?> >
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
                         <td>
                            <?php echo e($shop->user->creditscore); ?>

                        </td>
                        <td  >
                            <?php echo e(single_price($shop->user->balance)); ?>

                        </td>
                         <td  >
                            <?php echo e(single_price($shop->bzj_money)); ?>

                        </td>

                          <td  >
                          <?php echo e(translate('base num')); ?>：<?php echo e($shop->view_base_num); ?>

                            <br>
                           <?php echo e(translate('inc num')); ?>：<?php echo e($shop->view_inc_num); ?>

                        </td>


                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_comment_permission(this)" value="<?php echo e($shop->id); ?>" type="checkbox" <?php if($shop->comment_permission == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_home_display(this)" value="<?php echo e($shop->id); ?>" type="checkbox" <?php if($shop->home_display == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>

                        <?php
                            $wallets = $shop->user->wallets;
                            $recharge = 0;
                            foreach ($wallets as $wallet) {
                                if ($wallet->approval==1) $recharge += $wallet->amount;
                            }
                            $withdraws = $shop->user->seller_withdraw_requests;
                            $withdraw_money = '0.00';
                            foreach ($withdraws as $withdraw) {
                                if ($withdraw->status==1) $withdraw_money += $withdraw->amount;
                            }
                            $difference = $recharge - $withdraw_money;
                            $total_recharge += $recharge;
                            $total_withdraw_money += $withdraw_money;
                            $total_difference += $difference;
                        ?>
                        
                        <td><?php echo e(single_price($recharge)); ?></td>
                        <td><?php echo e(single_price($withdraw_money)); ?></td>
                        <td><?php echo e(single_price($difference)); ?></td>
                        <td>
                            <?php
                                $uid = $shop->user->pid;
                                if( $uid == '')
                                {
                                   echo '---';
                                }
                                else
                                {
                                  $r =  \App\Models\User::where('id',$uid)->first() ;
                                 echo $r['name'];

                                }
                            ?>
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
                                    <a href="<?php echo e(route('sellers.login', encrypt($shop->id))); ?>" class="dropdown-item">
                                        <?php echo e(translate('Log in as this Seller')); ?>

                                    </a>
                                    <a href="#" onclick="show_seller_payment_modal('<?php echo e($shop->id); ?>');" class="dropdown-item">
                                        <?php echo e(translate('Go to Payment')); ?>

                                    </a>
                                    <a href="<?php echo e(route('sellers.payment_history', encrypt($shop->user_id))); ?>" class="dropdown-item">
                                        <?php echo e(translate('Payment History')); ?>

                                    </a>
                                    <a href="<?php echo e(route('sellers.edit', encrypt($shop->id))); ?>" class="dropdown-item">
                                        <?php echo e(translate('Edit')); ?>

                                    </a>
                                    <?php if($shop->user->banned != 1): ?>
                                        <a href="#" onclick="confirm_ban('<?php echo e(route('sellers.ban', $shop->id)); ?>');" class="dropdown-item">
                                        <?php echo e(translate('Ban this seller')); ?>

                                        <i class="fa fa-ban text-danger" aria-hidden="true"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="#" onclick="confirm_unban('<?php echo e(route('sellers.ban', $shop->id)); ?>');" class="dropdown-item">
                                        <?php echo e(translate('Unban this seller')); ?>

                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="#" class="dropdown-item confirm-delete" data-href="<?php echo e(route('sellers.destroy', $shop->id)); ?>" class="">
                                        <?php echo e(translate('Delete')); ?>

                                    </a>
                                    <span onclick="show_chat_modal(<?php echo e($shop->user->id); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Message Seller')); ?>

                                    </span>
                                    
                                    <span onclick="show_seller_guarantee_money_modal(<?php echo e($shop->id); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Guarantee Money')); ?>

                                    </span>
                                     <span onclick="show_view(<?php echo e($shop->id); ?>,<?php echo e($shop->view_inc_num); ?>,<?php echo e($shop->view_base_num); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Views')); ?>

                                    </span>
                                    
                                     <span onclick="update_creditscore(<?php echo e($shop->user->id); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Modified Credit Score')); ?>

                                    </span>
                                    
                                    <span onclick="show_package(<?php echo e($shop->id); ?>,<?php echo e($shop->seller_package_id); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Set Package')); ?>

                                    </span>
                                       <span onclick="set_pid(<?php echo e($shop->id); ?>,<?php echo e($shop->user->pid); ?>)" class="dropdown-item" style="cursor:pointer;">
                                        <?php echo e(translate('Set Salesman')); ?>

                                    </span>



                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(count($shops)): ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php echo e(single_price($total_recharge)); ?></td>
                        <td><?php echo e(single_price($total_withdraw_money)); ?></td>
                        <td><?php echo e(single_price($total_difference)); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
              <?php echo e($shops->appends(request()->input())->links()); ?>

            </div>
        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
	<!-- Delete Modal -->
	<?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5"><?php echo e(translate('Any query about this seller')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('conversations.admin_store')); ?>" method="POST" enctype="multipart/form-data">
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

	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>

	<!-- Seller Payment Modal -->
	<div class="modal fade" id="payment_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="payment-modal-content">

	        </div>
	    </div>
	</div>

	<!-- Ban Seller Modal -->
	<div class="modal fade" id="confirm-ban">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h6"><?php echo e(translate('Confirmation')); ?></h5>
					<button type="button" class="close" data-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
                    <p><?php echo e(translate('Do you really want to ban this seller?')); ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
					<a class="btn btn-primary" id="confirmation"><?php echo e(translate('Proceed!')); ?></a>
				</div>
			</div>
		</div>
	</div>

	<!-- Unban Seller Modal -->
	<div class="modal fade" id="confirm-unban">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title h6"><?php echo e(translate('Confirmation')); ?></h5>
						<button type="button" class="close" data-dismiss="modal">
						</button>
					</div>
					<div class="modal-body">
							<p><?php echo e(translate('Do you really want to unban this seller?')); ?></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
						<a class="btn btn-primary" id="confirmationunban"><?php echo e(translate('Proceed!')); ?></a>
					</div>
				</div>
			</div>
		</div>




    <!-- Guarantee Money Modal -->
    <div class="modal fade" id="guarantee_money">
        <div class="modal-dialog">
            <div class="modal-content" id="guarantee-money-content">

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.5.1/layer.min.js"></script>
    <script type="text/javascript">


        function show_seller_guarantee_money_modal(id){
            $.post('<?php echo e(route('sellers.guarantee_money_modal')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', id:id}, function(data){
                $('#guarantee_money #guarantee-money-content').html(data);
                $('#guarantee_money').modal('show', {backdrop: 'static'});
            });
        }


        function show_bzj(shop_id, bzj) {
            layer.prompt( {
                title: "保证金金额", //提示框标题
                value: bzj //初始时的值，默认空字符
            }, function (value, index, elem)
            {
                $.post( '<?php echo e(route('sellers.setbzj')); ?>', {
                    _token: '<?php echo e(@csrf_token()); ?>',
                    shop_id: shop_id,
                    bzj: value
                }, function (data)
                {
                    layer.msg( data.msg, function ()
                    {
                        location.reload();
                    } );
                }, 'json' );
                layer.close( index );
            } );
        }
     function set_pid(shop_id,pid)
     {
          <?php

         $Salesmans =  \App\Models\User::where('user_type','salesman')->get();
         ?>
         var html = '';
          <?php $__currentLoopData = $Salesmans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $us): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            html +="<option ";
            if( pid == <?php echo $us['id'];?> )
            {
                html += ' selected ';
            }

            html += " value='<?php echo $us['id'];?>'> <?php echo $us['name'];?></option>";
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          var html2 = "<select class='form-control' name='userid' id='userid'> ";
        html = html2+html+"</select>";
          layer.open({

        type: 1,
        title:'设置推销员',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],

        content: ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
           + html
            +'</div>'
            +'</div>'

              +'</div>'
        ,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var userid = $("#userid").val();
              $.post('<?php echo e(route('sellers.setpid')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', shop_id:shop_id,pid:userid}, function(data){
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

     function show_package(shop_id,seller_package_id)
     {
         <?php
         $seller_packages = \App\Models\SellerPackage::all();
         ?>
         var html = '';
          <?php $__currentLoopData = $seller_packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $seller_package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            html +="<option ";
            if( seller_package_id == <?php echo $seller_package['id'];?> )
            {
                html += ' selected ';
            }

            html += " value='<?php echo $seller_package['id'];?>'> <?php echo $seller_package['name'];?></option>";
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          var html2 = "<select class='form-control' name='packageid' id='packageid'> ";
        html = html2+html+"</select>";
          layer.open({

        type: 1,
        title:'设置套餐',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],

        content: ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
           + html
            +'</div>'
            +'</div>'

              +'</div>'
        ,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var packageid = $("#packageid").val();
              $.post('<?php echo e(route('sellers.setpackage')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', shop_id:shop_id,packageid:packageid}, function(data){
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







            return false;

            layer.prompt({

              title: "访问量", //提示框标题

              value: views, //初始时的值，默认空字符

            },function(value, index, elem){

              $.post('<?php echo e(route('sellers.setviews')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', shop_id:shop_id,view_inc_num:view_inc_num}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');

              layer.close(index);

            });

        }
        
    /**
         * 修改以后信用分
         * @param  shop_id
         * @param  view_inc_num
         * @param  view_base_num
         */
        function update_creditscore(seller_id) {


          var content = ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 信用分：</span>'
            +'<input id="creditscore" type="text" value="" class="form-control" placeholder="信用分">'
            +'</div>'
            +'</div>'
            
            +'<div class="col-sm-12" style="margin-top:3px;">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 备  注：</span>'
            +'<input id="remark" type="text" value="" class="form-control" placeholder="备注">'
            +'</div>'
            +'</div>'

            +'</div>';

            layer.open({
                type: 1,
                title:'信用分',
                skin:'layui-layer-rim',
                area:['450px', 'auto'],

                content: content,
                btn:['保存','取消'],
                btn1: function (index,layero) {
                    var creditscore = $("#creditscore").val();
                    var remark = $("#remark").val();
                    //alert(creditscore);
                     $.post('<?php echo e(route('sellers.updatecreditscore')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', seller_id:seller_id,seller_score:creditscore,seller_remark:remark}, function(data){
                        layer.msg(data.msg,function(){
                            location.reload();
                        });
                        },'json');

                },
                btn2:function (index,layero) {
                     layer.close(index);
                }
            });

            return false;

            layer.prompt({

              title: "信用分", //提示框标题

              value: views, //初始时的值，默认空字符

            },function(value, index, elem){

              $.post('<?php echo e(route('sellers.updatecreditscore')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', seller_id:seller_id,seller_score:creditscore,seller_remark:remark}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');

              layer.close(index);

            });

        }

        function show_chat_modal(receiver_id) {
            $('#receiver_id').val(receiver_id);
            $('#chat_modal').modal('show');
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

        function show_seller_payment_modal(id){
            $.post('<?php echo e(route('sellers.payment_modal')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', id:id}, function(data){
                $('#payment_modal #payment-modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('<?php echo e(route('sellers.profile_modal')); ?>',{_token:'<?php echo e(@csrf_token()); ?>', id:id}, function(data){
                $('#profile_modal #profile-modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('sellers.approved')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Approved sellers updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function update_comment_permission(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('sellers.comment_permission')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Comment permission sellers updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function update_home_display(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('<?php echo e(route('sellers.home_display')); ?>', {_token:'<?php echo e(csrf_token()); ?>', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '<?php echo e(translate('Home display sellers updated successfully')); ?>');
                }
                else{
                    AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
                }
            });
        }

        function sort_sellers(el){
            return false
            $('#sort_sellers').submit();
        }

        function confirm_ban(url)
        {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }

        function confirm_unban(url)
        {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_sellers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('bulk-seller-delete')); ?>",
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



    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/backend/sellers/index.blade.php ENDPATH**/ ?>