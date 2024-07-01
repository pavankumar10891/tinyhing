<!DOCTYPE html>
<?php $logo = CustomHelper::getlogo();
$logoimage = '';
 if($logo){
   $logoimage = $logo->image;
 } 
?>
<?php $footerlogo = CustomHelper::footerlogo(); 

$footerlogoimage = '';
 if($footerlogo){
   $footerlogoimage = $footerlogo->image;
 }
?>
<html>	
	<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
		
    <?php $favicon = CustomHelper::fevicon(); ?>
    <?php if(!empty($favicon->image)): ?>
    <link rel="icon" href="<?php echo e($favicon->image); ?>">
    <?php else: ?>
    <link rel="icon" href="<?php echo e(WEBSITE_IMG_URL); ?>fav.png">
    <?php endif; ?>
		<title><?php echo e(Config::get("Site.title")); ?></title>

	<link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/bootstrap.css">
    <!-- Icons CSS -->
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/font-awesome.css" rel="stylesheet">
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/ionicons.min.css" rel="stylesheet">
    <!-- Animate CSS -->
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/animation.css" rel="stylesheet">
    <!-- Owl CSS -->
    <link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>front/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>front/owl.theme.default.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/style.css">
    <!-- Custom Responsive CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/responsive.css">
	<link href="<?php echo e(WEBSITE_CSS_URL); ?>notification/jquery.toastmessage.css" rel="stylesheet">

	<script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>notification/jquery.toastmessage.js"></script>
	
	 <script type="text/javascript">
		function show_message(message,message_type) {
			$().toastmessage('showToast', {	
				text: message,
				sticky: false,
				position: 'top-right',
				type: message_type,
				stayTime : 8000
			});	
		}
	</script>
  
	</head>
 
<body>
	 	 <?php echo $__env->make('front.elements.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		 <?php if(Session::has('error')): ?>
			<script>
				show_message("<?php echo Session::get('error'); ?>", "error");
			</script>
			<?php endif; ?>
			<?php if(Session::has('success')): ?>
			<script>
				show_message("<?php echo Session::get('success'); ?>", "success");
			</script>
			<?php endif; ?>
			<?php if(Session::has('info')): ?>
			<script>
				show_message("<?php echo Session::get('success'); ?>", "success");
			</script>
			<?php endif; ?>	
			<?php if(Session::has('flash_notice')): ?>
			<script>
				show_message("<?php echo Session::get('success'); ?>", "success");
			</script>
			<?php endif; ?>	
			<?php if(Session::has('warning')): ?>
			<script>
				show_message("<?php echo Session::get('notice'); ?>", "warning");
			</script>
			<?php endif; ?>
	 	  <?php echo $__env->yieldContent('content'); ?>
	 	 <?php echo $__env->make('front.elements.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</body>
    <script>
        $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
        </script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/counter.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/owl.carousel.min.js"></script>
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/wow.js"></script>
    <!-- Custom JavaScript -->
    <script type="text/javascript" src="<?php echo e(WEBSITE_JS_URL); ?>front1/script.js"></script>

    <script>
        $(document).ready(function () {
            $(".owl-carousel.brand").owlCarousel({

                loop: true,
                margin: 18,
                nav: false,
                dots: false,
                autoplay: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 4
                    }
                }
            });
            $(".owl-carousel.banner").owlCarousel({
				loop: true,
				margin: 0,
                autoplayTimeout: 5000,
				nav: false,
                autoplayHoverPause:true,
				dots: false,
				autoplay: true,
				responsive: {
				    0: {
				        items: 1
				    },
				    600: {
				        items: 1
				    },
				    1000: {
				        items: 1
				    }
				}
				});
        });

    </script>
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function () {
            $(".partner-owl").owlCarousel({
                nav: false,
                dots: false,
                loop: false,
                autoplay: true,
                autoplayTimeout: 2000,
                autoplayHoverPause: true,
                stagePadding: 0,
                margin: 0,
                items: 6,
                navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"],
                responsive: {
                    0: {
                        items: 2
                    },
                    575: {
                        items: 3
                    },
                    767: {
                        items: 4
                    },
                    991: {
                        items: 6
                    }
                }
            });
        });
    </script>
     
    <?php echo $__env->yieldContent('scripts'); ?>
</html>
	
<?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/layouts/default.blade.php ENDPATH**/ ?>