<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Molista</title>
	<link href="https://fonts.googleapis.com/css?family=Lato:400,300,700,900,300italic,400italic,700italic" rel="stylesheet" type="text/css" />
	<link href="/compiled/css/corporate.css" rel="stylesheet" type='text/css' />
	<link id="page_favicon" href="http://molista.localhost/favicon.ico" rel="icon" type="image/x-icon" />
	<script type="text/javascript">
		var ready_callbacks = [];
	</script>
	<style type="text/css">
		body { font-family: 'Lato', sans-serif; font-style: normal; font-weight: 400; }
		#home { opacity: 0; }
			#home h1 { font-size: 20px; }
			#home .img-responsive { display: inline-block; }
		.footer { background: transparent; }
			.footer ul.footer-list { padding-top: 30px; }
			.footer a { text-decoration: none; color: #999; font-size: 0.8em; }
			.footer a:hover { text-decoration: none; color: #666; }
	</style>
</head>

<body>

<div id="home">
	<div class="container text-center">
		<br />
		<p><img src="{{ asset('images/logo-beta.png') }}" alt="Molista" class="img-responsive" /></p>
		<h1>Something big is coming soon</h1>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var h = $('#home').outerHeight() + 20;
		if ( $(window).height() > h ) {
			$('#home').css({
				'top': '50%',
				'position': 'absolute',
				'width': '100%',
				'margin-top': ( -1 * h / 2 ) + 'px'
			});
		}

		$('#home').animate({ opacity: 1 }, 1000);

	});
</script>


<footer class="footer">
	<div class="container">
		<ul class="list-unstyled pull-right footer-list">
			<li><a href="{{ action('AdminController@index') }}">{{ Lang::get('admin/menu.home') }}</a></li>
		</ul>
	</div>
</footer>

<script src="{{ Theme::url('/compiled/js/app.js') }}"></script>

</body>
</html>
