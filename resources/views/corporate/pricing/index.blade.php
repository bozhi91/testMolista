@extends('layouts.corporate')

@section('content')

	<div id="signup" class="pricing">

		<div class="container">
			<h1 class="text-center">{!! Lang::get('corporate/pricing.h1') !!}</h1>

			<div class="pricing-plans">
				@include('corporate.common.plans', [
					'buy_plan_url' => action('Corporate\SignupController@getIndex'),
				])
			</div>
		</div>

		<div id="home">
			@include('corporate.common.home-fourth-block')
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
		});
	</script>

@endsection
