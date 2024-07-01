
<?php $__env->startSection('content'); ?>
<div class="padding-section">
   <div class="container">
       <div class="faq py-lg-5 py-4">
        <div class="heading pb-lg-4 pb-4 text-center">

            <h3>Frequently Asked Questions</h3>
        <p>Need any help jusct send a message via our email address</p>
        </div>
        <div id="accordion" class="accordion">
        <?php if(!empty($faqs )): ?>
        <?php ($count = 1); ?>
          <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $_faqs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           
            <div class="card">
                <div class="card-header collapsed" data-toggle="collapse" href="#collapse_<?php echo e($count); ?>">
                    <a class="card-title"> Reference site about Lorem Ipsum, as well as a random Lipsum generator. </a>
                </div>
                <div id="collapse_<?php echo e($count); ?>" class="card-body collapse" data-parent="#accordion">
                    <p>Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS. </p>
                </div>
               
              
            </div>
            <?php ( $count = $count+1 ); ?>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
       <?php endif; ?>
      
        </div>
   </div>
</div>
</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/pages/faqs.blade.php ENDPATH**/ ?>