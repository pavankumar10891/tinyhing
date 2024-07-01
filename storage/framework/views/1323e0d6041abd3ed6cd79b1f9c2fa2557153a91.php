
<?php $__env->startSection('content'); ?>
<?php if(!empty($aboutUs)): ?>
<section class="about-us padding-section">
        <div class="container">
            <div class="heading py-lg-5 pb-3  text-center">

                <h4><?php echo e(!empty($aboutUs->name) ? $aboutUs->name:''); ?></h4>

            </div>
            <div class="row align-items-center pb-lg-5">
                    <div class="col-md-12 text-center">
                        <div class="paragraph-block">
                        <p><?php echo !empty($aboutUs->description) ? $aboutUs->description:''; ?></p>
                         </div>
                    </div>
            </div>
        </div>
    </section>    
    <?php endif; ?>

    <?php if(!empty($testimomials)): ?>
    <section class="aboutus testimonials-sec py-md-5">
        <div class="container pt-5">
            <div class="fix-container">
                <div class="heading text-center pb-5">
                    <h5>Testimonials</h5>
                    <h3><?php echo e(!empty($testimomialsHeading->name) ? $testimomialsHeading->name:''); ?></h3>
                    <p><?php echo !empty($testimomialsHeading->description) ? $testimomialsHeading->description:''; ?></p>
                </div>


                <div class="row">
                     <?php $__currentLoopData = $testimomials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimomial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <div class="col-md-6">
                        
                        <div class="text-block pr-2">
                            <div class="text-wall">
                                <p><?php echo !empty($testimomial->description) ? strip_tags($testimomial->description):''; ?></p>
                            </div>
                            <div class="triangle"></div>

                            <div class="d-flex align-items-center py-4">
                                <?php if(!empty($testimomial->image)): ?>
                                <div class="img-wall  pl-4">
                                    <img src="<?php echo e(WEBSITE_URL.'image.php?width=80px&height=80px&image='.$testimomial->image); ?>" alt="">
                                </div>
                                <?php else: ?>
                                <div class="img-wall  pl-4">
                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>no-image.png" class="img-fluid">
                                </div>
                                <?php endif; ?>
                                

                                <div class="pl-3">

                                    <h2> <?php echo e(!empty($testimomial->name) ? $testimomial->name:''); ?></h2>
                                    <span> <?php echo e(!empty($testimomial->designation) ? $testimomial->designation:''); ?></span>
                                </div>



                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                    <div class="btn-block mb-5 my-md-5 text-center">
                        <a href="<?php echo e(route('user.testimonials')); ?>" class="btn-theme">
                            View all Testimonials
                        </a>
                    </div>
                    <div class="testimonials-img">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>balloon.PNG" class="ab-img balloon">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>balloon.PNG" class="ab-img balloon-left">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>balloon.PNG" class="ab-img balloon-right">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>cart.png" class="ab-img cart">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>butterfly.png" class="ab-img butterfly">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>test-1.png" class="ab-img ribbon">
                    </div>

                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/pages/about_us.blade.php ENDPATH**/ ?>