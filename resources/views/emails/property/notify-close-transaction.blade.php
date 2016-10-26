@extends('layouts.email')

@section('content')

	<div class="container">

		<div class="content">
			<div class="h1">{{ Lang::get('corporate/signup.email.subject') }}</div>
		</div>

		<div class="footer">
			<a href="{{ env('LINKS_FACEBOOK', 'https://www.facebook.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-facebook.png') }}" alt="" /></a>
			<a href="{{ env('LINKS_LINKEDIN', 'https://www.linkedin.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-linkedin.png') }}" alt="" /></a>
		</div>

	</div>

@endsection
