@extends('layouts.web')

@section('content')

	<div id="signup" class="step-{{ @$step }}">

		<div class="container">
			<h1 class="text-center">{{ Lang::get('corporate/signup.h1') }}</h1>

			@yield('signup_content')

		</div>

	</div>

@endsection
