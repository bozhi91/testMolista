@extends('layouts.corporate')

@section('content')

	<div id="signup" class="step-{{ @$step }}">

		<div class="container">
			<h1 class="text-center">{{ Lang::get('corporate/signup.h1') }}</h1>

			@yield('signup_content')

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#signup-form');

			var w = 0;
			form.find('.btn-nav').each(function(){
				if ( w < $(this).innerWidth() ) {
					w = $(this).innerWidth();
				}
			}).innerWidth(w).css({ opacity : 1 });

		});
	</script>

@endsection
