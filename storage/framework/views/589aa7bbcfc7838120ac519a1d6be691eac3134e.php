
<?php $__env->startSection('content'); ?>

<nav aria-label="breadcrumb" class="padding-section">
        <div class="breadcrumb-block">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/blogs')); ?>">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog Detail</li>
                </ol>
            </div>
        </div>
    </nav>
    <section class="blog-area blog-detail backg-img">
        <div class="container">
            <div class="row py-md-5">
                <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
                    <div class="single-blog">
                        <div class="blog-image">
                            <img src="<?php echo e(BLOG_IMAGE_URL.$blog->banner_image); ?>" alt="">
                        </div>
                        <div class="blog-details">
                  

                            <h3><?php echo e(isset($blog->title) ? $blog->title:''); ?></h3>

                            <div class="post-date">
                                <a href="#">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo e(date('d M, Y', strtotime($blog->created_at))); ?>

                                </a>
                            </div>
                            <p><?php echo isset($blog->description) ? $blog->description:''; ?></p>
                        </div>
                    </div>
                    <div class="share-button">
                        <div class="share-heading"> Share it</div>
                        <div class="social-bookmark">
                            <ul class="list-unstyled social_link_bar ">

                                <li>
                                    <a href="javascript:void(0)" class="facebook-bx" onclick="postToFeed('<?php echo $blog->title ;  ?>','<?php echo url()->current(); ?>','','<?php echo BLOG_IMAGE_URL.$blog->banner_image; ?>')">
                                       <i class="fab fa-facebook-f"> </i>
                                    </a>
                                </li>
                             
                                <!-- <li>
                                    <a href="javascript:void(0);" class="youtube-bx">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                </li> -->
                                <?php $twitter_share_link = "https://twitter.com/intent/tweet?text=".ucwords($blog->title)."+".url()->current(); ?>
                                <li>
                                <a class="twitter-share-button twitter-bx" onclick="window.open('<?php echo e($twitter_share_link); ?>','name','width=600,height=400')"  href="javascript:void(0)"  data-size="large">
                                 
                               <!-- <a href="javascript:void(0)" onclick="window.open('<?php echo e($twitter_share_link); ?>','name','width=600,height=400')" class="twitter-bx">-->
                                <i class="fab fa-twitter"></i>
                                </a>
                                </li>
                               <!-- 
                                      <li>
                                    <a href="javascript:void(0);" class="facebook-bx">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                   
                                   <li>
                                    <a href="javascript:void(0);" class="twitter-bx">
                
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>  -->
                
                            </ul>
                        </div>
                    </div>

                </div>
                <!-- Right Sidebar -->
                <?php echo $__env->make('front.blog.blog_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </div> 
        </div>
    </section>

    <script>
    
    window.fbAsyncInit = function() {
        FB.init({
            appId      : "307086770869300",
            xfbml      : true,
            version    : 'v2.8',
            redirect_uri: '<?php echo WEBSITE_URL; ?>',
        });
    };

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));


	function postToFeed(title, url, description, image){
		FB.getLoginStatus(function(response) {
			if (response.authResponse) {
				FB.ui({ 
					method: 'share_open_graph', 
					action_type: 'og.shares',
					action_properties: JSON.stringify({ 
						object : {
                            'og:url': url, // your url to share
                            'og:title': title,
                            'og:description': "",
                            'og:image': image
						}
					}),
					display:'popup',
				}, function(response){
					
				});  
			}else {
				FB.login(function(response) {
                    if (response.authResponse) {
                        FB.ui({
                            method: 'share_open_graph',
                            action_type: 'og.shares',
                            action_properties: JSON.stringify({
                                object : {
                                    'og:url': url, // your url to share
                                    'og:title': title,
                                    'og:description': "",
                                    'og:image': image
                                }
                            }),
                            display:'popup',
                        }, function(response){
                            
                        });
                    } else {
                        // alert("User cancelled login or did not fully authorize.");
                    }
                }, true);
			}
		}, true); 
	}



    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('front.layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/blog/blog_details.blade.php ENDPATH**/ ?>