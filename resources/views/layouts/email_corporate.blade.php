<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div class="container">

			<div class="header">
				<a href="{{ LaravelLocalization::getLocalizedURL(null, '/') }}" target="_blank"><img style="width:50%; height:20%;" src="{{ $logo_url }}" alt="" /></a>
			</div>

			<div class="content">
				@yield('content')
			</div>

			<div class="footer">
				<a href="{{ env('LINKS_FACEBOOK', 'https://www.facebook.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-facebook.png') }}" alt="" /></a>
				<a href="{{ env('LINKS_LINKEDIN', 'https://www.linkedin.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-linkedin.png') }}" alt="" /></a>
			</div>
		</div>
	</body>
</html>
