<?php $__env->startSection('panel_content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3 row align-items-center">
    <div class="align-items-center">
        <h1 class="h3"><?php echo e(translate('All Customers')); ?></h1>
    </div>
    <!--<div class="col text-right">
        <a href="<?php echo e(route('salesman.customers.create')); ?>" class="btn btn-circle btn-info">
            <span><?php echo e(translate('Add Virtual Account')); ?></span>
        </a>
    </div> -->
    
      <div class="ml-auto" style="margin-right: 6px;">
                <button id="create_virtual_user" type="button" class="btn btn-outline-primary btn-block" onclick=""><?php echo e(translate('Create Virtual Customers')); ?></button>
            </div>
</div>


<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6"><?php echo e(translate('Customers')); ?></h5>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    <?php echo e(translate('Bulk Action')); ?>

                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"><?php echo e(translate('Delete selection')); ?></a>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"<?php if(isset($sort_search)): ?> value="<?php echo e($sort_search); ?>" <?php endif; ?> placeholder="<?php echo e(translate('Type email or name & Enter')); ?>">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <!--<th data-breakpoints="lg">#</th>-->
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
                        <th data-breakpoints="lg"><?php echo e(translate('Email Address')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Phone')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Package')); ?></th>
                        <th data-breakpoints="lg"><?php echo e(translate('Wallet Balance')); ?></th>
                        <th><?php echo e(translate('Options')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($user != null): ?>
                            <tr>
                                <!--<td><?php echo e(($key+1) + ($users->currentPage() - 1)*$users->perPage()); ?></td>-->
                                <td>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]" value="<?php echo e($user->id); ?>">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td><?php if($user->banned == 1): ?> <i class="fa fa-ban text-danger" aria-hidden="true"></i> <?php endif; ?> <?php echo e($user->name); ?> <?php if($user->is_virtual_user == 1): ?> (<font color="red"><?php echo e(translate('Virtual')); ?></font>) <?php endif; ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td><?php echo e($user->phone); ?></td>
                                <td>
                                    <?php if($user->customer_package != null): ?>
                                    <?php echo e($user->customer_package->getTranslation('name')); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(single_price($user->balance)); ?></td>
                                <td class="text-right">
                                    
                                       <a href="<?php echo e(route('customers.login', encrypt($user->id))); ?>" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="<?php echo e(translate('Log in as this Customer')); ?>">
                                            <i class="las la-edit"></i>
                                        </a>
                                        
                                        
                                    <?php if(Auth::user()->user_type == 'salesman' && false): ?>
                                    <a href="<?php echo e(route('customers.login', encrypt($user->id))); ?>" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="<?php echo e(translate('Log in as this Customer')); ?>">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if($user->banned != 1): ?>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="confirm_ban('<?php echo e(route('salesman.customers.ban', encrypt($user->id))); ?>');" title="<?php echo e(translate('Ban this Customer')); ?>">
                                        <i class="las la-user-slash"></i>
                                    </a>
                                    <?php else: ?>
                                    <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="confirm_unban('<?php echo e(route('salesman.customers.ban', encrypt($user->id))); ?>');" title="<?php echo e(translate('Unban this Customer')); ?>">
                                        <i class="las la-user-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="<?php echo e(route('salesman.customers.destroy', $user->id)); ?>" title="<?php echo e(translate('Delete')); ?>">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="aiz-pagination">
                <?php echo e($users->appends(request()->input())->links()); ?>

            </div>
        </div>
    </form>
</div>


<div class="modal fade" id="confirm-ban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6"><?php echo e(translate('Confirmation')); ?></h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?php echo e(translate('Do you really want to ban this Customer?')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                <a type="button" id="confirmation" class="btn btn-primary"><?php echo e(translate('Proceed!')); ?></a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-unban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6"><?php echo e(translate('Confirmation')); ?></h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?php echo e(translate('Do you really want to unban this Customer?')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                <a type="button" id="confirmationunban" class="btn btn-primary"><?php echo e(translate('Proceed!')); ?></a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    
        <div class="modal fade" id="virtual_user_form" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6"><?php echo e(translate('Create Virtual Customers')); ?></h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 16px; font-size: 14px; ">
                    <i><?php echo e(translate('N:B: You can create virtual customers here, with a maximum of 100 people')); ?></i>
                </div>
                <form class="form-horizontal" action="<?php echo e(route('customers.create_virtual_user')); ?>" method="POST">
                    <!--<div class="form-group row">
                        <div class="col-lg-2"><?php echo e(translate('Name Prefix')); ?></div>
                        <div class="col-lg-6">
                           <input type="text" class="form-control" name="name_prefix" value="" placeholder="Prefix of Name" required>
                        </div>
                        <div class="col-lg-4"><span><?php echo e(translate('Optional')); ?></span></div>
                    </div>-->
                    
                    <div class="form-group row" style="display:none;">
                        <div class="col-lg-2"><?php echo e(translate('Referrel User')); ?></div>
                        <div class="col-lg-6">
                           <input type="number" class="form-control" name="referred_by" value="<?php echo Auth::user()->id;?>" placeholder="Referrel User" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-lg-2"><?php echo e(translate('Quantity')); ?></div>
                        <div class="col-lg-6">
                           <input type="number" min="1" step="1" max="100" class="form-control" name="quantity" value="1" placeholder="Quantity of generate" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-lg-2"><?php echo e(translate('Initial Balance')); ?></div>
                        <div class="col-lg-6">
                           <input type="number" min="0" class="form-control" name="balance" value="0.00" placeholder="Initial Balance of Accounts" required>
                        </div>
                    </div>
                     <div class="form-group row">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-6">
                            <div style="display: flex; align-items: center;"> 
                            
                            <div class="aiz-checkbox-inline" style="display: inline-block;">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="disable_login" value="1">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                            <span style="margin-top: 14px;"><?php echo e(translate('Disable Log in')); ?></span>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(translate('Cancel')); ?></button>
                <a type="button" id="submitVirtualCustomer" class="btn btn-primary"><?php echo e(translate('Submit')); ?></a>
            </div>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">


    $(function() {
            $('#create_virtual_user').bind('click', function() {
                $('#virtual_user_form').modal('show')
            })
            
            $('#submitVirtualCustomer').bind('click', function() {
                let target = $(this)
                if (target.hasClass('disabled')) return false
                
                target.addClass('disabled');
                
                let max = $('input[name=quantity]').val()
                let balance = $('input[name=balance]').val()
                let referred_by = $('input[name=referred_by]').val()
                
                fetch('<?php echo e(route('customers.create_virtual_user')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'fetch'
                    },
                    body: JSON.stringify({
                        _token: '<?php echo e(csrf_token()); ?>',
                        max,
                        balance,
                        referred_by,
                    })
                })
                .then(resp => resp.text())
                .then(res => {
                    if (res == 1) {
                        AIZ.plugins.notify('success', '<?php echo e(translate('Successfully created virtual customer')); ?>')
                        setTimeout(() => {
                            window.location.reload()
                        }, 500)
                    } else {
                        AIZ.plugins.notify('danger', '<?php echo e(translate('Executed failure Try again')); ?>')
                        target.removeClass('disabled')
                    }
                    
                })
                .catch(err => null).finally(() => {
                    //target.removeClass('disabled')
                })
                
            })
        })
        
        
        
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

        function sort_customers(el){
            $('#sort_customers').submit();
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
            var data = new FormData($('#sort_customers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('bulk-customer-delete')); ?>",
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

<?php echo $__env->make('salesman.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/salesman/customers/index.blade.php ENDPATH**/ ?>