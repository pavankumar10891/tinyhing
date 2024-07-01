
<?php $__env->startSection('content'); ?>

<?php if(!empty($banners)): ?>



<section class="banner">
    <div class="owl-carousel banner">
        <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keybanner=>$valbaneer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       
        <div class="items bg-banner" style="background-image: url(<?php echo e(!empty($valbaneer->image) ? $valbaneer->image:''); ?>);">
            <div class="container">
                <div class="slide-1">
                    <div class="row align-items-center">
                        <div class="col-md-7  banner-col-left text-center text-md-left">
                            <?php if($keybanner == 0): ?>
                            <div class="heading pb-3 pt-5 pt-md-0">
                                <h5><?php echo e(!empty($valbaneer->title) ? $valbaneer->title:''); ?></h5>
                                <h3><?php echo !empty($valbaneer->description) ? strip_tags($valbaneer->description):''; ?>

                                </h3>
                            </div>
                            <div class="paragraph-block">
                                <p>Our Nannies come with wealth of experience</p>
                            </div>
                            <?php else: ?>
                                <div class="heading pb-3 pt-5 pt-md-0"> 
                                    <!-- <h5><?php echo e(!empty($valbaneer->title) ? $valbaneer->title:''); ?></h5> -->
                                    <h3><?php echo !empty($valbaneer->description) ? strip_tags($valbaneer->description):''; ?>

                                    </h3>
                                </div>
                                <?php  if(empty(Auth::user())){  ?>
                                <div class="btn-block mt-4 d-flex justify-content-center">
                                    <a href="<?php echo e(route('user.nannylist')); ?>" class="btn-theme mr-4">
                                        Interview Nanny
                                    </a>
                                    <a href="<?php echo e(route('user.pricing')); ?>" class="btn-theme btn-white">
                                        Join Now
                                    </a>
                                </div>
                                <?php }  ?>
                            <?php endif; ?>
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
                                  <!--  <div class="form-group">
                                        <select class="custom-select babbysitter-type"  name="babbysitter">
                                            <option value="">-Select Babbysitter Type--</option>
                                            <option value="1">Nanny</option>
                                            <option value="2">Babbysitter</option>
                                        </select>
                                    </div>
 -->
                                <div class="container">
                                     
                                      <ul class="nav nav-tabs">
                                        <li class="active nny"><a  href="javascript:void(0)" class="nanny_tab">Nanny</a></li>
                                        &nbsp;<li class="bbs"><a href="javascript:void(0)" class="babbysitter_tab">Babbysitter</a></li>
                                      </ul>
                                  </div>
                
                                <form id="user-quote-form" class="form nanny-quote-form" id="formData_<?php echo e($keybanner); ?>">
                                    <input type="hidden" name="type" value="" class="babysittertype">
                                    
                                    <div class="form-group">
                                        <select class="custom-select children"  name="children">
                                            <option value="">Number of children</option>
                                            <?php for($i=1;$i<=5;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="children_value_error help-inline error"></span>
                                    </div>

                                    <div class="form-group">

                                        <select class=" custom-select weeks" name="weeks">
                                            <option value="">Number of Hours per week</option>
                                            <?php for($i=4;$i<=50;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="week_value_error help-inline error"></span>
                                    </div>
                                    <input type="hidden" name="children_value" class="children_value children_values" value="">
                                    <input type="hidden" name="week_value" class="week_value week_values" value="">
                                    <a href="javascript:void(0);" class="btn-theme mt-3 getquote" data-id="<?php echo e($keybanner); ?>">
                                        SUBMIT NOW
                                    </a>

                                    <img src="<?php echo e(WEBSITE_IMG_URL); ?>heart.png" class="ab-img heart">
                                </form>

                                <form id="user-quote-form " class="form babbysitter-quote-form formData_<?php echo e($keybanner); ?>" style="display: none;">
                                    <input type="hidden" name="type" value="2" class="babysittertype">
                                    <!-- <div class="form-group">
                                        
                                        <span class="children_value_error help-inline error"></span>
                                    </div> -->
                                    <div class="form-group">
                                        <input type="datetime-local" name="date_time" class="form-control date_time_<?php echo e($keybanner); ?> datepickerfrom">
                                        <span class="date_time_error help-inline error"></span>
                                    </div>

                                    <div class="form-group">
                                        <select class=" custom-select duration" name="duration">
                                            <option value="">Duration</option>
                                            <?php for($i=1;$i<=24;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="duration_value_error help-inline error"></span>
                                    </div>
                                    <div class="form-group">
                                        <select class="custom-select children"  name="children">
                                            <option value="">Number of children</option>
                                            <?php for($i=1;$i<=5;$i++): ?> <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                                <?php endfor; ?>
                                        </select>
                                        <span class="children_value_error help-inline error"></span>
                                    </div>
                                    <input type="hidden" name="children_value" class="children_value" value="">
                                    <input type="hidden" name="duration_value" class="duration_value" value="">
                                    <a href="javascript:void(0);" class="btn-theme mt-3 babbysitter_getquote" data-id="<?php echo e($keybanner); ?>">
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
                        <?php echo strip_tags($aboutUs->description); ?>

                        <?php endif; ?>

                    </div>

                    <div class="btn-block mt-5">
                        <a href="<?php echo e(route('user.aboutus')); ?>" class="btn-theme">
                           Learn More
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
                    <h3>What We Offer</h3>
                </div>

                <?php $image =  !empty($liveoutnanny1->image) ? $liveoutnanny1->image: WEBSITE_IMG_URL.'service-img-1.jpg'; ?>

                

                <?php $imag2 =  !empty($liveoutnanny2->image) ? $liveoutnanny2->image: WEBSITE_IMG_URL.'service-img-2.jpg'; ?>
                <div class="img-wall service_img" style="background-image: url(<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag2); ?>)">
                    <img src="<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag2); ?>" style="opacity: 0;">
                    <div class="text-wall"><?php echo $liveoutnanny2->description; ?>


                    </div>
                </div>

                <?php $imag3 =  !empty($liveoutnanny3->image) ? $liveoutnanny3->image: WEBSITE_IMG_URL.'service-img-3.jpg'; ?>
                <div class="img-wall service_img" style="background-image: url(<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag3); ?>)">
                    <img src="<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag3); ?>" style="opacity: 0;">
                    <div class="text-wall"><?php echo $liveoutnanny3->description; ?>


                    </div>
                </div>
                <?php $imag4 =  !empty($liveoutnanny4->image) ? $liveoutnanny4->image: WEBSITE_IMG_URL.'service-img-3.jpg'; ?>
                 <div class="img-wall service_img" style="background-image: url(<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag4); ?>)">
                    <img src="<?php echo e(WEBSITE_URL.'image.php?width=608px&height=168px&image='.$imag4); ?>" style="opacity: 0;">
                    <div class="text-wall"><?php echo $liveoutnanny4->description; ?>


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
                                <img src="<?php echo e(WEBSITE_IMG_URL); ?>no-image.png" class="img-fluid" width="80" height="80">
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

<?php endif; ?>


<script>
    function showPosition() {

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
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

    function getCurrentLocation() {

        $.ajax({
            url: '<?php echo e(route("user.current.location")); ?>',
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $("#loader_img").show();
            },
            success: function (res) {
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
    $(document).ready(function () {
      $('.babbysitter-type').change(function () {
            var type =  $(this).val();
            if(type == 1){
                $('.nanny-quote-form').show();
                $('.babbysitter-quote-form').hide();
                $('.babysittertype').val(1);
            } else if(type == 2){
                $('.nanny-quote-form').hide();
                $('.babbysitter-quote-form').show();
                $('.babysittertype').val(2);
            }
        });

    });
    $('.babbysitter_tab').click(function(){
        $('.nanny-quote-form').hide();
        $('.nanny-quote-form').fadeOut();
        $('.babbysitter-quote-form').fadeIn();
        $('.babbysitter-quote-form').show();
        $('.nny').removeClass('active');
        $('.bbs').addClass('active');

    });

    $('.nanny_tab').click(function(){
        $('.nny').addClass('active');
        $('.bbs').removeClass('active');
        $('.nanny-quote-form').show();
        $('.nanny-quote-form').fadeIn();
        $('.babbysitter-quote-form').fadeOut();
        $('.babbysitter-quote-form').hide();

    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/users/index.blade.php ENDPATH**/ ?>