@extends('layouts.web')

@section('content')

	<div id="signup" class="step-{{ @$step }}">

		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">

					<h1 class="text-center">{{ Lang::get('corporate/signup.h1') }}</h1>

					@include('common.messages', [ 'dismissible'=>true ])

					@yield('signup_content')

				</div>
			</div>
		</div>

	</div>

@endsection
