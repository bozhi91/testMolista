@if ( empty($email_content_only) )
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
		</head>
		<body>
			@yield('content')
		</body>
	</html>
@else
	@yield('content')
@endif
