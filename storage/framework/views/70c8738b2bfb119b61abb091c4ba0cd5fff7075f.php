<?php $__env->startSection('panel_content'); ?>
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Manage Profile')); ?></h1>
        </div>
      </div>
    </div>
    <form action="<?php echo e(route('seller.profile.update', $user->id)); ?>" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="POST">
        <?php echo csrf_field(); ?>
        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Basic Info')); ?></h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="name"><?php echo e(translate('Your Name')); ?></label>
                    <div class="col-md-10">
                        <input type="text" name="name" value="<?php echo e($user->name); ?>" id="name" class="form-control" placeholder="<?php echo e(translate('Your Name')); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="phone"><?php echo e(translate('Your Phone')); ?></label>
                    <div class="col-md-10">
                        <input type="text" name="phone" value="<?php echo e($user->phone); ?>" id="phone" class="form-control" placeholder="<?php echo e(translate('Your Phone')); ?>">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label"><?php echo e(translate('Photo')); ?></label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium"><?php echo e(translate('Browse')); ?></div>
                            </div>
                            <div class="form-control file-amount"><?php echo e(translate('Choose File')); ?></div>
                            <input type="hidden" name="photo" value="<?php echo e($user->avatar_original); ?>" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="password"><?php echo e(translate('Your Password')); ?></label>
                    <div class="col-md-10">
                        <input type="password" name="new_password" id="password" class="form-control" placeholder="<?php echo e(translate('New Password')); ?>">
                        <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="confirm_password"><?php echo e(translate('Confirm Password')); ?></label>
                    <div class="col-md-10">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="<?php echo e(translate('Confirm Password')); ?>" >
                        <?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Payment System -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Payment Setting')); ?></h5>
            </div>
            <div class="card-body">
                <div class="row" id="cash">
                    <label class="col-md-3 col-form-label"><?php echo e(translate('Cash Payment')); ?></label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="cash_on_delivery_status" type="checkbox" <?php if($user->shop->cash_on_delivery_status == 1): ?> checked <?php endif; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row" id="bank">
                    <label class="col-md-3 col-form-label"><?php echo e(translate('Bank Payment')); ?></label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="bank_payment_status" type="checkbox" <?php if($user->shop->bank_payment_status == 1): ?> checked <?php endif; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_name"><?php echo e(translate('Bank Name')); ?></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_name" value="<?php echo e($user->shop->bank_name); ?>" id="bank_name" class="form-control mb-3" placeholder="<?php echo e(translate('Bank Name')); ?>">
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_name"><?php echo e(translate('Bank Account Name')); ?></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_name" value="<?php echo e($user->shop->bank_acc_name); ?>" id="bank_acc_name" class="form-control mb-3" placeholder="<?php echo e(translate('Bank Account Name')); ?>">
                        <?php $__errorArgs = ['bank_acc_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_no"><?php echo e(translate('Bank Account Number')); ?></label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_no" value="<?php echo e($user->shop->bank_acc_no); ?>" id="bank_acc_no" class="form-control mb-3" placeholder="<?php echo e(translate('Bank Account Number')); ?>">
                        <?php $__errorArgs = ['bank_acc_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_routing_no"><?php echo e(translate('Bank Routing Number')); ?></label>
                    <div class="col-md-9">
                        <input type="number" name="bank_routing_no" value="<?php echo e($user->shop->bank_routing_no); ?>" id="bank_routing_no" lang="en" class="form-control mb-3" placeholder="<?php echo e(translate('Bank Routing Number')); ?>">
                        <?php $__errorArgs = ['bank_routing_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

               <div class="row"  id="usdt">
                    <label class="col-md-3 col-form-label"><?php echo e(translate('USDT Payment')); ?></label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="usdt_payment_status" type="checkbox" <?php if($user->shop->usdt_payment_status == 1): ?> checked <?php endif; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="usdt_type"><?php echo e(translate('USDT Link')); ?></label>
                    <div class="col-md-9">
                        <input type="text" name="usdt_type" value="<?php echo e($user->shop->usdt_type); ?>" id="usdt_type" class="form-control mb-3" placeholder="<?php echo e(translate('USDT Link')); ?>">
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="usdt_address"><?php echo e(translate('USDT Address')); ?></label>
                    <div class="col-md-9">
                        <input type="text" name="usdt_address" value="<?php echo e($user->shop->usdt_address); ?>" id="usdt_address" class="form-control mb-3" placeholder="<?php echo e(translate('USDT Address')); ?>">
                        <?php $__errorArgs = ['usdt_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="form-text text-danger"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-0 text-right">
            <button type="submit" class="btn btn-primary"><?php echo e(translate('Update Profile')); ?></button>
        </div>
    </form>

    <br>

    <!-- Address -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6"><?php echo e(translate('Address')); ?></h5>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4">
                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('Address')); ?>:</span>
                                <span class="ml-2"><?php echo e($address->address); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('Postal Code')); ?>:</span>
                                <span class="ml-2"><?php echo e($address->postal_code); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('City')); ?>:</span>
                                <span class="ml-2"><?php echo e(optional($address->city)->name); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('State')); ?>:</span>
                                <span class="ml-2"><?php echo e(optional($address->state)->name); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('Country')); ?>:</span>
                                <span class="ml-2"><?php echo e(optional($address->country)->name); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600"><?php echo e(translate('Phone')); ?>:</span>
                                <span class="ml-2"><?php echo e($address->phone); ?></span>
                            </div>
                            <?php if($address->set_default): ?>
                                <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                    <span class="badge badge-inline badge-primary"><?php echo e(translate('Default')); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="dropdown position-absolute right-0 top-0">
                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                    <i class="la la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="edit_address('<?php echo e($address->id); ?>')">
                                        <?php echo e(translate('Edit')); ?>

                                    </a>
                                    <?php if(!$address->set_default): ?>
                                        <a class="dropdown-item" href="<?php echo e(route('seller.addresses.set_default', $address->id)); ?>"><?php echo e(translate('Make This Default')); ?></a>
                                    <?php endif; ?>
                                    <a class="dropdown-item" href="<?php echo e(route('seller.addresses.destroy', $address->id)); ?>"><?php echo e(translate('Delete')); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-4 mx-auto" onclick="add_new_address()">
                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                        <i class="la la-plus la-2x"></i>
                        <div class="alpha-7"><?php echo e(translate('Add New Address')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Email -->
    <form action="<?php echo e(route('user.change.email')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0 h6"><?php echo e(translate('Change your email')); ?></h5>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-md-2">
                      <label><?php echo e(translate('Your Email')); ?></label>
                  </div>
                  <div class="col-md-10">
                      <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="<?php echo e(translate('Your Email')); ?>" name="email" value="<?php echo e($user->email); ?>" />
                        <div class="input-group-append">
                           <button type="button" class="btn btn-outline-secondary new-email-verification">
                               <span class="d-none loading">
                                   <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><?php echo e(translate('Sending Email...')); ?>

                               </span>
                               <span class="default"><?php echo e(translate('Verify')); ?></span>
                           </button>
                        </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-primary"><?php echo e(translate('Update Email')); ?></button>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </form>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    
    <div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('New Address')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="<?php echo e(route('seller.addresses.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('Address')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control mb-3" placeholder="<?php echo e(translate('Your Address')); ?>" rows="2" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('Country')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="<?php echo e(translate('Select your country')); ?>" name="country_id" required>
                                            <option value=""><?php echo e(translate('Select your country')); ?></option>
                                            <?php $__currentLoopData = \App\Models\Country::where('status', 1)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('State')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required>

                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('City')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>

                                    </select>
                                </div>
                            </div>

                            <?php if(get_setting('google_map') == 1): ?>
                                <div class="row">
                                    <input id="searchInput" class="controls" type="text" placeholder="<?php echo e(translate('Enter a location')); ?>">
                                    <div id="map"></div>
                                    <ul id="geoData">
                                        <li style="display: none;"><?php echo e(translate('Full Address')); ?>: <span id="location"></span></li>
                                        <li style="display: none;"><?php echo e(translate('Postal Code')); ?>: <span id="postal_code"></span></li>
                                        <li style="display: none;"><?php echo e(translate('Country')); ?>: <span id="country"></span></li>
                                        <li style="display: none;"><?php echo e(translate('Latitude')); ?>: <span id="lat"></span></li>
                                        <li style="display: none;"><?php echo e(translate('Longitude')); ?>: <span id="lon"></span></li>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname"><?php echo e(translate('Longitude')); ?></label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="longitude" name="longitude" readonly="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname"><?php echo e(translate('Latitude')); ?></label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="latitude" name="latitude" readonly="">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('Postal code')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('Your Postal Code')); ?>" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label><?php echo e(translate('Phone')); ?></label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="<?php echo e(translate('+880')); ?>" name="phone" value="" required>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary"><?php echo e(translate('Save')); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo e(translate('New Address')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="edit_modal_body">

                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">

        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();

            $.post('<?php echo e(route('user.new.verify')); ?>', {_token:'<?php echo e(csrf_token()); ?>', email: email}, function(data){
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if(data.status == 2)
                    AIZ.plugins.notify('warning', data.message);
                else if(data.status == 1)
                    AIZ.plugins.notify('success', data.message);
                else
                    AIZ.plugins.notify('danger', data.message);
            });
        });

        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '<?php echo e(route("seller.addresses.edit", ":id")); ?>';
            url = url.replace(':id', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    <?php if(get_setting('google_map') == 1): ?>
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = response.data.address_data.latitude;
                            long    = response.data.address_data.longitude;
                        }

                        initialize(lat, long, 'edit_');
                    <?php endif; ?>
                }
            });
        }

        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });

        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('seller.get-state')); ?>",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "<?php echo e(route('seller.get-city')); ?>",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

    </script>

    <?php if(get_setting('google_map') == 1): ?>

        <?php echo $__env->make('frontend.partials.google_map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('seller.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/seller/profile/index.blade.php ENDPATH**/ ?>