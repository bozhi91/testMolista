@extends('corporate.signup.index', [
	'step' => 'finish',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			<div class="step-form">
				<h2 class="text-center">{{ Lang::get('corporate/signup.finish.h2') }}</h2>

				<div class="step-content">
					<div class="congratulations">
						{!! Lang::get('corporate/signup.finish.congratulations', [ 
							'website_url' => $site->main_url
						]) !!}
					</div>

					@if ( empty($pending_request) )
						<div class="features">
							{!! Lang::get('corporate/home.features') !!}
						</div>
					@else
						@if ( $pending_request->summary->payment_method == 'stripe' )
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<p>Please complete you payment.</p>
									{!! Lang::get('corporate/signup.finish.plan.details', [
										'plan' => @$pending_request->summary->plan_name,
										'price_text' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
									]) !!}
									<div class="links-warning">
										<div class="help-block">{!! Lang::get('corporate/signup.finish.stripe.warnings') !!}</div>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
<h2 style="background: red;">[TODO] Stripe form</h2>
<?php
echo "<pre>";
@print_r($pending_request->summary);
echo "</pre>";
?>
								</div>
							</div>
						@elseif ( $pending_request->summary->payment_method == 'transfer' )
							{!! Lang::get('corporate/signup.finish.transfer.intro', [
								'plan' => @$pending_request->summary->plan_name,
								'price_text' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
							]) !!}
						@endif
					@endif
					<div class="text-right links">
						<a href="{{ $site->account_url }}" class="btn btn-primary" target="_blank">{{ Lang::get('corporate/signup.finish.gotoaccount') }}</a>
						<a href="{{ $site->main_url }}" class="btn btn-primary" target="_blank">{{ Lang::get('corporate/signup.finish.gotoweb') }}</a>
					</div>
					<div class="links-warning">
						<div class="help-block">
							{!! Lang::get('corporate/signup.finish.warning.links', [
								'owner_email' => @$owner_email,
							]) !!}
						</div>
					</div>
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
