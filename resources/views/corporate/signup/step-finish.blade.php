@extends('corporate.signup.index', [
	'step' => 'finish',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			<div class="step-form">
				<h2 class="text-center">{{ Lang::get('corporate/signup.finish.h2') }}</h2>

				<div class="step-content">
					{!! Lang::get('corporate/signup.finish.intro') !!}
					@if ( empty($planchange) )
					@else
	<?php
	echo "<pre>";
	print_r($planchange->new_data);
	echo "</pre>";
	?>
					@endif
				</div>
			</div>

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#signup');
		});
	</script>
@endsection
