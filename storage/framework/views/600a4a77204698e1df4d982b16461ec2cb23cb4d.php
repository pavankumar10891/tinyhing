<!doctype html>
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
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <title><?php echo e(Config::get("Site.title")); ?></title>
    <link rel="apple-touch-icon" sizes="57x57" href="img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="img/favicon//ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/bootstrap.css">
    <!-- Icons CSS -->
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/font-awesome.css" rel="stylesheet">
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/ionicons.min.css" rel="stylesheet">
    <!-- Animate CSS -->
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>front/animation.css" rel="stylesheet">
    <!-- Owl CSS -->
    <link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>front/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>front/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?php echo e(WEBSITE_CSS_URL); ?>front/fullcalendar.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/style.css">
    <link href="<?php echo e(WEBSITE_CSS_URL); ?>notification/jquery.toastmessage.css" rel="stylesheet">
   <!-- Custom Responsive CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo e(WEBSITE_CSS_URL); ?>front/responsive.css">
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

    <?php if(!empty(Auth::user()) && Auth::user()->user_role_id  == NANNY_ROLE_ID  ): ?> 
    <body>
    <?php elseif(!empty(Auth::user()) && Auth::user()->user_role_id  == SUBSCRIBER_ROLE_ID): ?>
    <body class="parent-dashboard">
   
     <?php endif; ?>

       <?php echo $__env->make('front.dashboard.elements.dashboard_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
	 	 <?php echo $__env->make('front.dashboard.elements.dashboard_footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	   </body>


       <?php echo $__env->yieldContent('scripts'); ?>

    </html>

	 	
		 
	 	
	
    <?php /**PATH C:\xampp\htdocs\tinyhugs\resources\views/front/dashboard/layouts/default.blade.php ENDPATH**/ ?>