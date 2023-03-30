<?php $__env->startSection('panel_content'); ?>
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
          <div class="col-md-6">
              <b class="h4"><?php echo e(translate('Conversations')); ?></b>
          </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($conversation->receiver != null && $conversation->sender != null): ?>
                    <li class="list-group-item px-0">
                      <div class="row gutters-10">
                          <div class="col-auto">
                              <div class="media">
                                  <span class="avatar avatar-sm flex-shrink-0">
                                    <?php if(Auth::user()->id == $conversation->sender_id): ?>
                                        <img <?php if($conversation->receiver->avatar_original == null): ?> src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" <?php else: ?> src="<?php echo e(uploaded_asset($conversation->receiver->avatar_original)); ?>" <?php endif; ?> onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';">
                                    <?php else: ?>
                                        <img <?php if($conversation->sender->avatar_original == null): ?> src="<?php echo e(static_asset('assets/img/avatar-place.png')); ?>" <?php else: ?> src="<?php echo e(uploaded_asset($conversation->sender->avatar_original)); ?>" <?php endif; ?> class="rounded-circle" onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';">
                                    <?php endif; ?>
                                </span>
                              </div>
                          </div>
                          <div class="col-auto col-lg-3">
                              <p>
                                  <?php if(Auth::user()->id == $conversation->sender_id): ?>
                                      <span class="fw-600"><?php echo e($conversation->receiver->name); ?></span>
                                  <?php else: ?>
                                      <span class="fw-600"><?php echo e($conversation->sender->name); ?></span>
                                  <?php endif; ?>
                                  <br>
                                  <span class="opacity-50">
                                      <?php echo e(date('h:i:m d-m-Y', strtotime($conversation->messages->last()->created_at))); ?>

                                  </span>
                              </p>
                          </div>
                          <div class="col-12 col-lg">
                              <div class="block-body">
                                  <div class="block-body-inner pb-3">
                                      <div class="row no-gutters">
                                          <div class="col">
                                              <h6 class="mt-0">
                                                  <a href="<?php echo e(route('conversations.show', encrypt($conversation->id))); ?>" class="text-dark fw-600">
                                                      <?php echo e($conversation->title); ?>

                                                  </a>
                                                  <?php if((Auth::user()->id == $conversation->sender_id && $conversation->sender_viewed == 0) || (Auth::user()->id == $conversation->receiver_id && $conversation->receiver_viewed == 0)): ?>
                                                      <span class="badge badge-inline badge-danger"><?php echo e(translate('New')); ?></span>
                                                  <?php else: ?>
                                                      <?php $__currentLoopData = $conversation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $messages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                          <?php if( $messages->updated_at == $messages->created_at && $messages->user_id != Auth::user()->id ): ?>
                                                              <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                                                              <?php break; ?>
                                                          <?php endif; ?>
                                                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                  <?php endif; ?>
                                              </h6>
                                          </div>
                                      </div>
                                      <p class="mb-0 opacity-50">
                                          <?php echo e($conversation->messages->last()->message); ?>

                                      </p>
                                  </div>
                              </div>
                          </div>
                      </div>
                    </li>
              <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    </div>
    <div class="aiz-pagination">
      	<?php echo e($conversations->links()); ?>

    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/wwwroot/alicrossborder.com/resources/views/frontend/user/conversations/index.blade.php ENDPATH**/ ?>