<?php $__env->startSection('content'); ?>

<?php if(!empty($banners)): ?>



<section class="banner">
    <div class="owl-carousel banner">
        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keybanner=>$valbaneer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($keybanner == 0): ?>
        <div class="items bg-banner"
            style="background-image: url(<?php echo e(!empty($valbaneer->image) ? $valbaneer->image:''); ?>);">
            <div class="container">
                <div class="slide-1">
                    <div class="row align-items-center">
                        <div class="col-md-7  banner-col-left text-center text-md-left">
                            <div class="heading pb-3 pt-5 pt-md-0">
                                <h5><?php echo e(!empty($valbaneer->title) ? $valbaneer->title:''); ?></h5>
                                <h3><?php echo !empty($valbaneer->description) ? strip_tags($valbaneer->description):''; ?>

                                </h3>
                            </div>
                            <div class="paragraph-block">
                                <p>Our Nannies come with wealth of experience</p>
                            </div>
                            <!-- <div class="btn-block mt-4">
                            <a href="javascript:void(0);" class="btn-theme">
                                Subscribe Now
                            </a>
                      </div> -->
                        </div>
                        <div class="col-md-5 banner-col-right my-3 my-md-0">
                            <div class="form-block">
                                <div class="d-flex pb-5 align-items-center">
                                    <div class="text-block">
                                        <h3> Get a Quote!</h3>
                                        <span> Connect with us</span>
                                    </div>
                                    <div class="icon-block">
                                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>form-icon.PNG">
                                    </div>
                                </div>

                                <!--     <?php echo e(Form::open(array('id' => 'user-quote-form', 'class' => 'form'))); ?> -->
                                <form id="user-quote-form" class="form">
                                    <div class="form-group">
                                        <select class="custom-select" id="children" name="children">
                                            <option value="">Number of children</option>
                                            <?php for($i=1;$i<=5;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="children_error help-inline error"></span>
                                    </div>

                                    <div class="form-group">

                                        <select class=" custom-select" name="weeks" id="weeks">
                                            <option value="">Number of Hours per week</option>
                                            <?php for($i=4;$i<=50;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="weeks_error help-inline error"></span>
                                    </div>
                                    <a href="javascript:void(0);" class="btn-theme mt-3" id="getquote">
                                        SUBMIT NOW
                                    </a>

                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>heart.png" class="ab-img heart">
                                </form>
                                <!--  <?php echo e(Form::close()); ?> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="items bg-banner" style="background-image: url(<?php echo e($valbaneer->image); ?>);">
            <div class="container">
                <div class="slide-2">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="heading pb-2 text-center">
                                <h5><?php echo e(!empty($valbaneer->title) ? $valbaneer->title:''); ?></h5>
                                <h3><?php echo !empty($valbaneer->description) ? strip_tags($valbaneer->description):''; ?>

                                </h3>
                            </div>
                            <div class="paragraph-block pb-2">
                                <p>looking for care</p>
                            </div>
                            <?php  if(empty(Auth::user())){  
                            
                         ?>
                            <div class="btn-block mt-4 d-flex justify-content-center">
                                <a href="<?php echo e(url('/signup')); ?>" class="btn-theme mr-4">
                                    Register as Nanny/Babysitter
                                </a>
                                <a href="<?php echo e(url('/login')); ?>" class="btn-theme btn-white">
                                    Login As nanny
                                </a>
                            </div>
                            <?php   
                        }  ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>
<section class="location-sec">
    <div class="container">
        <div class="d-flex">

            <div class=" fields-bx">

                <div class="location-input-wrap">
                    <div class="location-input">

                        <!--   <?php echo e(Form::open(['role' => 'form','url' =>  route("user.nannylist"),'class' => 'mws-form', 'method' => 'get',"autocomplete"=>"off"])); ?> -->
                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="search" role="img"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            class="svg-inline--fa fa-search fa-w-16 fa-2x">
                            <path fill="currentColor"
                                d="M508.5 481.6l-129-129c-2.3-2.3-5.3-3.5-8.5-3.5h-10.3C395 312 416 262.5 416 208 416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c54.5 0 104-21 141.1-55.2V371c0 3.2 1.3 6.2 3.5 8.5l129 129c4.7 4.7 12.3 4.7 17 0l9.9-9.9c4.7-4.7 4.7-12.3 0-17zM208 384c-97.3 0-176-78.7-176-176S110.7 32 208 32s176 78.7 176 176-78.7 176-176 176z"
                                class=""></path>
                        </svg>
                        <input type="text" name="zipcode" id="zipcode_val" placeholder="Enter Your Zip/Postal Code">
                    </div>
                    <button type="button" id="zipcode_search" class="location-sub-btn "><i
                            class="fas fa-search"></i>Search</button>

                    <!--   <?php echo e(Form::close()); ?> -->
                    <button class="location-sub-btn" onclick=" return getCurrentLocation()"><i
                            class="fas fa-paper-plane"></i>Detect your location</button>

                </div>

            </div>
        </div>
    </div>
</section>
<?php if(!empty($aboutUs)): ?>
<section class="aboutus-sec ">
    <div class="container">

        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="img-wallsec">
                    <div class="img-wall">
                        <?php if(!empty($aboutUs->image)): ?>
                        <img src="<?php echo e(($aboutUs->image)); ?>">
                        <?php else: ?>
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>no-image.png" class="img-fluid">
                        <?php endif; ?>
                    </div>
                    <span><img src="<?php echo e(WEBSITE_IMG_URL); ?>about-us1.png" class="about-backimg ab-img"></span>
                    <span><img src="<?php echo e(WEBSITE_IMG_URL); ?>line.png" class="line-img ab-img"></span>
                    <span><img src="<?php echo e(WEBSITE_IMG_URL); ?>triangle.png" class="triangle-img ab-img"></span>
                    <span><img src="<?php echo e(WEBSITE_IMG_URL); ?>close.png" class="close-img ab-img"></span>
                </div>
            </div>
            <div class="col-md-6 ">
                <div class="padding-left pt-5 pt-md-0">
                    <div class="heading pb-3">
                        <h5>About Us</h5>
                        <h3>Welcome to <br /> Tiny Hugs</h3>
                    </div>
                    <div class="paragraph-block">
                        <?php if(!empty($aboutUs->description)): ?>
                        <?php echo e(Str::limit(strip_tags($aboutUs->description), 150)); ?>

                        <?php endif; ?>

                    </div>

                    <div class="btn-block mt-5">
                        <a href="<?php echo e(route('user.aboutus')); ?>" class="btn-theme">
                            Discover More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="services-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="heading pt-3">
                    <h5>Our Services</h5>
                    <h3>What we Offer</h3>
                </div>

                <?php $image =  !empty($liveoutnanny1->image) ? $liveoutnanny1->image: WEBSITE_IMG_URL.'service-img-1.jpg'; ?>

                <div class="img-wall service_img" style="background-image: url(<?php echo e($image); ?>)">
                    <img src="<?php echo e($image); ?>" style="opacity: 0;">
                    <div class="text-wall"><?php echo e($liveoutnanny1->description); ?>


                    </div>
                </div>

                <?php $imag2 =  !empty($liveoutnanny2->image) ? $liveoutnanny2->image: WEBSITE_IMG_URL.'service-img-2.jpg'; ?>
                <div class="img-wall service_img" style="background-image: url(<?php echo e($imag2); ?>)">
                    <img src="<?php echo e($imag2); ?>" style="opacity: 0;">
                    <div class="text-wall"><?php echo e($liveoutnanny2->description); ?>


                    </div>
                </div>

                <?php $imag3 =  !empty($liveoutnanny3->image) ? $liveoutnanny3->image: WEBSITE_IMG_URL.'service-img-3.jpg'; ?>
                <div class="img-wall service_img" style="background-image: url(<?php echo e($imag3); ?>)">
                    <img src="<?php echo e($imag3); ?> style=" opacity: 0;">
                    <div class="text-wall"><?php echo e($liveoutnanny3->description); ?>


                    </div>
                </div>


            </div>
            <div class="col-md-6 ">
                <div class="padding-left">
                    <div class="heading right-heading pt-3">
                        <h5>Reviews</h5>
                        <h3>Facebook Reviews</h3>
                    </div>

                    <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
                    </fb:login-button>

                    <?php if(!empty($facebookRevies)): ?>
                    <?php $__currentLoopData = $facebookRevies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kf=>$vf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php if($kf == 0): ?>
                    <div class="card active">
                        <div class="card-horizontal no-gutters">
                            <p class=""><?php echo e($vf->review_text); ?></p>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-horizontal no-gutters">
                            <p class=""><?php echo e($vf->review_text); ?></p>
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>


                    <!-- <div class="card">
                        <div class="card-horizontal no-gutters">
                            <div class="col-auto">
                                <div class="img-wall1">
                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>profile.jpg" class="img-fluid">
                                </div>
                            </div>
                            <div class="col pl-3">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Jessica Smith</h4>

                                    <div class="update-post">
                                        <div class="d-flex align-items-center">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fab"
                                                data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512" class="svg-inline--fa fa-facebook fa-w-16 fa-2x">
                                                <path fill="currentColor"
                                                    d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"
                                                    class=""></path>
                                            </svg> 5 weeks ago
                                        </div>
                                    </div>
                                </div>
                                <span>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </span>
                                <p class="card-text">
                                    Geben Sie Ihre Präferenzen wie Standort, Gehalt und Phase des Unternehmens ein -
                                    alles in weniger als Minuten!</p>
                            </div>
                        </div>


                    </div> -->
                    <!-- <div class="card">
                        <div class="card-horizontal no-gutters">
                            <div class="col-auto">
                                <div class="img-wall1">
                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>profile.jpg" class="img-fluid">
                                </div>
                            </div>
                            <div class="col pl-3">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Jessica Smith</h4>

                                    <div class="update-post">
                                        <div class="d-flex align-items-center">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fab"
                                                data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512" class="svg-inline--fa fa-facebook fa-w-16 fa-2x">
                                                <path fill="currentColor"
                                                    d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"
                                                    class=""></path>
                                            </svg> 5 weeks ago
                                        </div>
                                    </div>
                                </div>
                                <span>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </span>
                                <p class="card-text">
                                    Geben Sie Ihre Präferenzen wie Standort, Gehalt und Phase des Unternehmens ein -
                                    alles in weniger als Minuten!</p>
                            </div>
                        </div>


                    </div> -->
                    <!-- <div class="card">
                        <div class="card-horizontal no-gutters">
                            <div class="col-auto">
                                <div class="img-wall1">
                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>profile.jpg" class="img-fluid">
                                </div>
                            </div>
                            <div class="col pl-3">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Jessica Smith</h4>

                                    <div class="update-post">
                                        <div class="d-flex align-items-center">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fab"
                                                data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512" class="svg-inline--fa fa-facebook fa-w-16 fa-2x">
                                                <path fill="currentColor"
                                                    d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"
                                                    class=""></path>
                                            </svg> 5 weeks ago
                                        </div>
                                    </div>
                                </div>
                                <span>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </span>
                                <p class="card-text">
                                    Geben Sie Ihre Präferenzen wie Standort, Gehalt und Phase des Unternehmens ein -
                                    alles in weniger als Minuten!</p>
                            </div>
                        </div>


                    </div> -->
                </div>
            </div>

        </div>

        <div class="service-img">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>cloud.png" class="ab-img cloud">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>cloud.png" class="ab-img cloud1">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>close.png" class="ab-img close">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>heart.png" class="ab-img heart">
            <img src="<?php echo e(WEBSITE_IMG_URL); ?>line1.png" class="ab-img line">
        </div>
    </div>
</section>
<?php if(!empty($whychooseUs)): ?>
<section class="why-choose-us">
    <div class="container">
        <div class="row choose-padding no-gutters align-items-end">
            <div class="col-lg-7">
                <div class="heading pt-3 text-md-left text-center">
                    <h5>Benefits</h5>
                    <h3><?php echo e(!empty($whychooseUsHeading->name) ? $whychooseUsHeading->name:''); ?></h3>
                    <p><?php echo !empty($whychooseUsHeading->description) ? $whychooseUsHeading->description:''; ?></p>
                </div>
                <div class="choose-cont">
                    <div class="row">
                        <?php $__currentLoopData = $whychooseUs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $whychoose): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6">
                            <div class="py-3  text-center text-md-left">
                                <div class="d-inline-flex align-items-center pb-4">
                                    <div class="bg-img">
                                        <?php if($whychoose->image != ''): ?>
                                        <img src="<?php echo e(WHYCHOOSEUS_IMAGE_URL.$whychoose->image); ?>">
                                        <?php else: ?>
                                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>choose-icon-1.png">
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h3> <?php echo e(!empty($whychoose->name) ? $whychoose->name:''); ?> </h3>
                                    </div>
                                </div>
                                <div class="paragraph-block ">
                                    <p><?php echo !empty($whychoose->description) ? $whychoose->description:''; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>


            </div>
            <div class="col-lg-5">

                <div class="img-wall text-lg-right text-center">
                    <?php if(!empty($whychooseUsHeading) && $whychooseUsHeading->image !=''): ?>
                    <img src="<?php echo e(WEBSITE_URL.'image.php?width=490px&height=490px&image='.$whychooseUsHeading->image); ?>">
                    <?php else: ?>
                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>choose-us-img.png">
                    <?php endif; ?>

                    <div class="choose-img">

                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>heart.png" class="ab-img heart">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>line.png" class="ab-img line">
                        <img src="<?php echo e(WEBSITE_IMG_URL); ?>triangle.png" class="ab-img triangle1">
                    </div>
                </div>
            </div>

            <!--<div class="btn-block mt-lg-4 mt-5 text-center text-lg-left">
                    <a href="javascript:void(0);" class="btn-theme">
                        Subscribe Now
                    </a>
                </div>  -->
        </div>
    </div>

</section>
<?php endif; ?>



<section class="counter-sec">
    <div class="container">


        <div class="row">
            <div class="col-md-4">
                <div class="counter">
                    <?php ( $children_count = (!empty(Config::get('Site.number_of_children'))) ?
                    Config::get('Site.number_of_children') : 0 ); ?>
                    <h2 class="counting" data-count="<?php echo e($children_count); ?>">0</h2>
                    <p class="count-text ">Number of children</p>

                </div>
            </div>
            <div class="col-md-4 ">
                <div class="counter mx-md-auto">
                    <?php ( $satified_clients = (!empty(Config::get('Site.satified_clients'))) ?
                    Config::get('Site.satified_clients') : 0 ); ?>
                    <h2 class="counting" data-count="<?php echo e($satified_clients); ?>">0</h2>
                    <p class="count-text ">Satisfied Clients</p>
                </div>
            </div>
            <div class="col-md-4 ">
                <div class="counter ml-md-auto">
                    <?php ( $number_of_nannies = (!empty(Config::get('Site.number_of_nannies'))) ?
                    Config::get('Site.number_of_nannies') : 0 ); ?>
                    <h2 class="counting" data-count="<?php echo e($number_of_nannies); ?>">0</h2>
                    <p class="count-text ">Number of Nannies</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if(!empty($testimomials)): ?>
<section class="testimonials-sec py-md-5">
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
                                <img src="<?php echo e(WEBSITE_URL.'image.php?width=80px&height=80px&image='.$testimomial->image); ?>"
                                    alt="">
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


                <div class="btn-block my-5 text-center">
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

<?php if(!empty($corevalues)): ?>
<section class="value-sec">
    <div class="container">
        <div class="heading text-center pt-2 py-md-5">
            <h5>Values</h5>
            <h3><?php echo e(!empty($corevaluesCenterImage->name) ? $corevaluesCenterImage->name:'Our Core Values'); ?></h3>
        </div>
        <div class="row align-items-center pt-5">

            <div class="col-md-6 left-col">
                <?php $__currentLoopData = $corevalues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kcorevalues=>$vcorevalues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($kcorevalues%2 == 0): ?>
                <div class="row align-items-center padding-bottom no-gutters">
                    <div class="col">
                        <div class="text-block">
                            <h4><?php echo e(!empty($vcorevalues->name) ? $vcorevalues->name:''); ?></h4>
                            <p><?php echo !empty($vcorevalues->description) ? $vcorevalues->description:''; ?>

                            </p>
                        </div>
                    </div>

                    <div class="col-auto pl-3">
                        <?php if(!empty($vcorevalues->image)): ?>
                        <div class="vision"><img
                                src="<?php echo e(WEBSITE_URL.'image.php?width=144px&height=110px&image='.OURCOREVALUES_IMAGE_URL.$vcorevalues->image); ?>">
                        </div>
                        <?php else: ?>
                        <div class="vision"><img src="<?php echo e(WEBSITE_IMG_URL); ?>eye.png"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>


            <?php if(!empty($corevaluesCenterImage->image)): ?>
            <div class="col-md-6 middle-col">
                <figure> <img
                        src="<?php echo e(WEBSITE_URL.'image.php?width=540px&height=465px&image='.$corevaluesCenterImage->image); ?>"
                        alt=""></figure>
            </div>
            <?php else: ?>
            <div class="col-md-6 middle-col">
                <figure> <img src="<?php echo e(WEBSITE_IMG_URL); ?>value.png" alt=""></figure>
            </div>
            <?php endif; ?>

            <div class="col-md-6 offset-col right-col">
                <?php $__currentLoopData = $corevalues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kcorevaluess=>$vcorevalues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($kcorevaluess%2 != 0): ?>
                <div class="row align-items-center padding-bottom no-gutters">
                    <div class="col order-custom">
                        <div class="text-block">
                            <h4><?php echo e(!empty($vcorevalues->name) ? $vcorevalues->name:''); ?></h4>
                            <p><?php echo !empty($vcorevalues->description) ? $vcorevalues->description:''; ?>

                        </div>
                    </div>
                    <div class="col-auto pr-3">
                        <?php if(!empty($vcorevalues->image)): ?>
                        <div class="vision"><img src="<?php echo e(OURCOREVALUES_IMAGE_URL.$vcorevalues->image); ?>"></div>
                        <?php else: ?>
                        <div class="vision"><img src="<?php echo e(WEBSITE_IMG_URL); ?>eye.png"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<?php if(!empty($partners)): ?>
<section class="brand-sec">
    <div class="container">
        <div class="owl-carousel brand">
            <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!empty($partner->logo)): ?>
            <div class="item">
                <img
                    src="<?php echo e(WEBSITE_URL.'image.php?width=150px&height=97px&cropratio=3:2&image='.PARTNER_LOGO_URL.$partner->logo); ?>">
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<!-- Modal -->
<div class="modal fade price-moodel" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> -->
            <div class="modal-body">
                <div class="text-center py-4">
                    <div class="price-block"> Based on the specifications, the weekly rate for your nanny would be <span class="h5 text-center estimateprice">$0.00</span></br>
                            <span class="h6">
                            An additoinal
                            montly subscription fee to ensure your 
                            child receives the best care is also
                            listed below, featuring various included services.</span> 
                            <!-- <a data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">
                                <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="info-circle"
                                    role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                    class="svg-inline--fa fa-info-circle fa-w-16 fa-2x">
                                    <path fill="currentColor"
                                        d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z"
                                        class=""></path>
                                </svg>
                            </a> -->
                        </span></div>


                    <div class="radio-block mt-3">
                        Schedule Interview <input type="radio" name='action' class="mr-3" id="actionInterview"
                            value='interview' checked />
                        Pricing <input type="radio" name='action' id="actionPricing" value='pricing' />
                    </div>


                    <div class="btn-block mt-4">
                        <a href="javascript::void(0)" class="btn-theme proceedNowBtn">
                            Proceed Now
                        </a>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>
<form id="quoteform">
    <input type="hidden" name="children_value" id="children_value" value="">
    <input type="hidden" name="week_value" id="week_value" value="">
</form>

<script>
function showPosition() {

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var positionInfo = "Your current position is (" + "Latitude: " + position.coords.latitude + ", " +
                "Longitude: " + position.coords.longitude + ")";
            alert(positionInfo);
            //document.getElementById("result").innerHTML = positionInfo;
        });
    } else {
        alert("Sorry, your browser does not support HTML5 geolocation.");
    }
}
</script>
<script type="text/javascript">
$(function() {
    $('#user-quote-form').keypress(function(e) { //use form id
        if (e.which == 13) {
            login();
        }
    });
});

function quote() {

    var child = $("#children_value").val();
    var week = $("#week_value").val();
    $.ajax({
        url: "<?php echo e(route('user.quote')); ?>",
        method: 'post',
        data: {
            children: child,
            weeks: week
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $("#loader_img").show();
        },
        success: function(response) {
            $("#loader_img").hide();

            if (response.success == true) {
                $('.estimateprice').html(response.data.price);
                $('#exampleModal').modal('show');

                // window.location.href=response.page_redirect;
                //location.reload();
            } else if (response.success == 2) {
                show_message(response.message, 'error');
            } else if (response.success == false) {
                $('.children_error').html(response.errors.children_error);
                $('.weeks_error').html(response.errors.children_error);
                //location.reload();
            } else {

                $('span[id*="_error"]').each(function() {
                    var id = $(this).attr('id');

                    if (id in response.errors) {
                        $("#" + id).html(response.errors[id]);
                    } else {
                        $("#" + id).html('');
                    }
                });
            }
        }
    });
}

$(".proceedNowBtn").on('click',function(){
        if($("#actionInterview").prop('checked')==true){
            window.location.href="<?php echo e(route('user.nannylist')); ?>";
        }else{
            window.location.href="<?php echo e(route('user.pricing')); ?>";

        }
    });


function getCurrentLocation() {

    $.ajax({
        url: '<?php echo e(route("user.current.location")); ?>',
        method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $("#loader_img").show();
        },
        success: function(res) {
            $("#loader_img").hide();
            if (res.data != '') {
                $('#zipcode_val').val(res.data);
            } else {
                alert(res.mesg);
                return false;
            }
        }
    });

}
$(document).ready(function() {
    $('#getquote').click(function() {
        quote();
    });

    $('#children').change(function() {
        var chldval = $(this).val();
        $("#children_value").val(chldval);
    });

    $('#weeks').change(function() {
        var weekdval = $(this).val();
        $("#week_value").val(weekdval);
    });




});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/users/index.blade.php ENDPATH**/ ?>