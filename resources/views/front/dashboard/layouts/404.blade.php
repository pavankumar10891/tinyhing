<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link rel="shortcut icon" href="{{WEBSITE_IMG_URL}}fav.png">
		<title>{{{Config::get("Site.title")}}}</title>
		
		<link href="{{WEBSITE_CSS_URL}}bootstrap.css" rel="stylesheet">
	</head>
	<body class="over-hidden">

	<div id="mws-error-page">
		<h1>Error <span>404</span></h1>
		<h5>Oopss... this is embarassing, either you tried to access a non existing page, or our server has gone crazy.</h5>
		<p><a href="{{ route('dashboard')}}">click here</a> to go back dashboard</p>
     </div>
 

		
	</body>
</html>