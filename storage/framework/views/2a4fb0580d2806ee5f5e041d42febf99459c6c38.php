
<?php $__env->startSection('content'); ?>

<div class="padding-section terms-conditions">
  <div class="container">
  <?php if(!empty($terms)): ?>
    <div class="heading py-lg-5 pb-3  text-center">

        <h4><?php echo e(!empty($terms->title)? $terms->title  : ''); ?></h4>
    </div>
      <div class="row">
     
          <div class="col-md-12">
                <div class="terms-service-block">
                    
                <?php echo !empty($terms->body)? $terms->body  : ''; ?>

              
                </div>
             </div> 
         </div>
         <?php endif; ?>
    </div>
</div>
   
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/pages/terms_and_conditions.blade.php ENDPATH**/ ?>