<div class="loader-wrapper" id="loader_img" style="display:none;">
        <div class="loader">
            <img src="<?php echo e($logoimage); ?>" alt="">
            <div class="material-spinner"></div>
        </div>
    </div> 
<div class="overlay" style="display:none"></div>
<?php if(request()->is('/')){ ?>
    <header class="top-header"> 
 <?php }else{ ?>
<header class="inner-header">
  <?php } ?>
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center py-2">
                <div class="col-md-12 text-md-right text-center">
                    <div class="cont-header pr-3"> CALL FREE ON :<a href="javascript:void"> 
                        <?php echo e(Config::get('Site.free_call')); ?> </a></div>
                        <?php if(Auth::check()): ?>
                        <div class="cont-header pr-2"> <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tachometer-alt-fastest" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-tachometer-alt-fastest fa-w-18 fa-2x">
                                <path fill="currentColor" d="M288 32C128.94 32 0 160.94 0 320c0 52.8 14.25 102.26 39.06 144.8 5.61 9.62 16.3 15.2 27.44 15.2h443c11.14 0 21.83-5.58 27.44-15.2C561.75 422.26 576 372.8 576 320c0-159.06-128.94-288-288-288zm144 128c17.67 0 32 14.33 32 32s-14.33 32-32 32-32-14.33-32-32 14.33-32 32-32zM288 96c17.67 0 32 14.33 32 32s-14.33 32-32 32-32-14.33-32-32 14.33-32 32-32zM96 384c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm48-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm339.95 151.67l-133.93 22.32c-1.51 6.37-3.69 12.48-6.9 18.01H232.88c-5.5-9.45-8.88-20.28-8.88-32 0-35.35 28.65-64 64-64 23.06 0 43.1 12.31 54.37 30.61l133.68-22.28c13.09-2.17 25.45 6.64 27.62 19.72 2.19 13.07-6.65 25.45-19.72 27.62z" class=""></path>
                            </svg><a href="<?php echo e(route('user.nannydashboard')); ?>">DASHBOARD</a></div>    <?php endif; ?>  
                    <div class="cont-header"> <img src="<?php echo e(WEBSITE_IMG_URL); ?>/signin.png" class="pr-1">
                        <?php if(Auth::check()): ?>
                        
                      
                           <a href="<?php echo e(route('user.logout')); ?>"> LOGOUT</a>
                        

                       
                        <?php else: ?>
                        <a href="<?php echo e(route('client.login')); ?>"> CLIENT</a>/<a
                            href="<?php echo e(route('user.login')); ?>"> NANNY</a>
                        <?php endif; ?>    
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="" id="header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                            <img src="<?php echo e($logoimage); ?>" alt="">
                        </a>

                        <button class="navbar-toggler" type="button">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse">
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item active">
                                    <a class="nav-link" href="<?php echo e(url('/')); ?>">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('user.aboutus')); ?>">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('user.nannylist')); ?>">Our Nannies</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="<?php echo e(route('user.pricing')); ?>">Pricing</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="<?php echo e(route('user.blog')); ?>">Blogs</a>
                                </li>
                                <?php /*
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('client.signup') }}">Subscribe</a>
                                </li> */ ?> 
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('user.faqs')); ?>">FAQs</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo e(route('user.contact')); ?>">Contact Us</a>
                                </li>
                            </ul>


                         <!--     <div class="extra_nav">
                             <ul class="navbar-nav ml-auto">

                                    <li class="nav-item order_btn">
                                        <a class="nav-link extra_btn" href="javascript:void(0);">Subscribe Now</a>
                                    </li>
                                </ul> 
                            </div>   -->
                            
                            <?php if(Auth::check()): ?>
                            <?php  $userData  = Auth::user();    ?>
                             
                            <div class="extra_nav author-detail">
                               <div class="d-flex align-items-center nav-right pl-3">

                                    <?php if(!empty(Auth::user()->photo_id) && file_exists(USER_IMAGE_ROOT_PATH.Auth::user()->photo_id)): ?>
                                      <a href="#"><img src="<?php echo e(WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.USER_IMAGE_URL.Auth::user()->photo_id); ?>" alt=""></a> 
                                    <?php else: ?>
                                    <?php if(Auth::user()->user_role_id  == NANNY_ROLE_ID ): ?>
                                        <a href="#"> <img src="<?php echo e(WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-female.jpg'); ?>" alt=""></a>
                                    <?php else: ?>
                                        <a href="#"> <img src="<?php echo e(WEBSITE_URL.'image.php?width=47&height=47&cropratio=3:3&image='.WEBSITE_IMG_URL.'no-image.png'); ?>" alt=""></a>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                   <div class="author-name text-left pl-3">
                                            <a href="#"><?php echo e((!empty($userData->name) ?  $userData->name  :  '' )); ?></a>
                                           <!-- <span> Customer</span>  -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?> 


                        <!--  ======= User DropDown =========  -->
                        <!-- <div class="nav-item dropdown user_dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="user-drop" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="img/user.png" alt="">
                            </a>
                            <div class="dropdown-menu" aria-labelledby="user-drop">
                                <div class="user_info">
                                    <div class="user_name">
                                        <div>Vishal verma</div>
                                        <div class="user_email">
                                            <small>vishal@gmail.com</small>
                                        </div>
                                    </div>
                                    <ul>
                                        <li>
                                            <a href="#!"><i class="ion-android-person"></i> My Profile</a>
                                        </li>
                                        <li>
                                            <a href="#!"><i class="ion-log-out"></i> Logout</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div> -->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header><?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/elements/header.blade.php ENDPATH**/ ?>