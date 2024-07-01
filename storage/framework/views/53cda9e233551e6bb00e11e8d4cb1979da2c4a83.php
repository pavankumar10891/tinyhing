<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
		<link rel="shortcut icon" href="<?php echo e(WEBSITE_IMG_URL); ?>fav.png">
		<title><?php echo e(Config::get("Site.title")); ?></title>
		
		<link href="<?php echo e(WEBSITE_CSS_URL); ?>bootstrap.css" rel="stylesheet">
		<link href="<?php echo e(WEBSITE_CSS_URL); ?>front/style.css" rel="stylesheet">
		
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700;900&display=swap"
		rel="stylesheet">
	</head>
	<style>
		html,
		body {
			padding: 0;
			font-family: 'Lato', sans-serif;
			margin: 0;

		}

		body.over-hidden.nofound-body {
			background-image: url(https://tinyhugspanel.stage02.obdemo.com/uploads/banner_images/MAY2021/1620639343-image.jpg);

			background-size: cover;
			background-position: center;
		}

		.nofound-body {
			height: 100vh;
			margin: 0;
			padding: 0;
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			justify-content: center;
			position: relative;
		}
.mail{ color: #fff; 
    font-size: 18px;}
.mail label{padding-right: 10px;}
.mail a{ color: #fff; 
    font-size: 18px;}

		.nofound-body:after {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgb(130 167 156 / 80%);
}

		.error-page {
			position: relative;
			z-index: 4;
		}

		.error-page h1 {
			font-size: 160px;
			line-height: 1;
			font-weight: 800;
			color: #ffffff;
		}

		.error-page h2 {
			font-size: 38px;
    letter-spacing: -1px;
    font-weight: 600;
    color: #fff;
		}

		.error-page h4 {
			font-weight: 400;
			font-size: 21px;
			color: #f7f7f7;
		}

		.backHome {
			background-color: #ffffff;
			border-color: #ffffff;
			padding: 14px 34px;
			outline: 0;
			box-shadow: unset;
			font-weight: 600;
			display: inline-block;
			margin-top: 35px;
			border-radius: 0;
			color: #000;
		}

		.backHome:hover {
			color: #000000;
		}


		.backHome:hover span:before {
			width: 100%;
		}

		.webLogo {
			max-width: 410px;
			margin: 0 auto 40px;
			padding: 10px;
		}

		.webLogo img {
			max-width: 180px;
			filter: invert(1);
		}

		@media (max-width: 767.98px) {
			.error-page h1 {
				font-size: 120px;
			}

			.webLogo img {
				max-width: 150px;
			}

			.error-page h2 {
				font-size: 24px;
			}

			.error-page h4 {
				font-size: 15px;
			}

			.backHome {
				padding: 11px 24px;
				font-weight: 600;
				margin-top: 20px;
				font-size: 14px;
			}
		}
	</style>

</head>

<body class="over-hidden nofound-body">

	<div id="mws-error-page" class="error-page text-center">

		<h1>404</h1>
		<h2>Oh no! There was an error.</h2>
		<h4>We couldn't find the page you were looking for.</h4>

		<div class="mail"><label>Email : </label><a href="mailto:<?php echo e(Config::get('Contact.email')); ?>"><?php echo e(Config::get('Contact.email')); ?></a></div>
		<!-- <h1>Error <span>404</span></h1>
		<h5>Oopss... this is embarassing, either you tried to access a non existing page, or our server has gone crazy.
		</h5> -->
		<!-- <p><a href="https://attri.stage02.obdemo.com/adminpnlx/dashboard">click here</a> to go back dashboard</p> -->
		<a href="<?php echo e(URL::to('/')); ?>" class="btn backHome"><span>Back to the Homepage</span></a>
	</div>



</body></html><?php /**PATH F:\xampp\htdocs\tinyhugs\resources\views/front/layouts/404.blade.php ENDPATH**/ ?>