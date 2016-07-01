@extends('corporate.signup.index', [
	'step' => 'finish',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			<div class="step-form">
				@if ( empty($pending_request) )
					<h2 class="text-center">{{ Lang::get('corporate/signup.finish.h2.ready') }}</h2>
				@else
					<h2 class="text-center">{{ Lang::get('corporate/signup.finish.h2') }}</h2>
				@endif

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
									{!! Lang::get('corporate/signup.finish.pay') !!}
									{!! Lang::get('corporate/signup.finish.plan.details', [
										'plan' => @$pending_request->summary->plan_name,
										'price_text' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
									]) !!}
									<br />
									<script src="https://checkout.stripe.com/checkout.js"></script>
									<script type="text/javascript">
										var stripeHandler = StripeCheckout.configure({
											key: '{{ env('STRIPE_KEY') }}',
											amount: '{{ round($pending_request->plan_price*100) }}',
											currency: 'EUR',
											name: 'Molista',
											description: '{{ Lang::get('corporate/signup.confirm.plan') }}: {{ @$pending_request->summary->plan_name }}',
											locale: '{{ LaravelLocalization::getCurrentLocale() }}',
											email: '{{ @$pending_request->invoicing['email'] }}',
											opened: function() {},
											closed: function() {},
											token: function(token, args) {
												var form = $('#stripe-form');
												if ( form.find('input[name="stripeToken"]').length < 1 ) {
													form.append('<input type="hidden" name="stripeToken" />');
												}
												form.find('input[name="stripeToken"]').val( token.id);

												LOADING.show();
												form.submit();
											}
										});
									</script>
									<a href="#" id="stripe-form-trigger" class="btn btn-block btn-warning btn-lg">{{ Lang::get('corporate/signup.finish.stripe.button') }}</a>
									<form action="{{ action('StripeController@postSubscription', [ $site->id, $pending_request->id ]) }}" method="POST" id="stripe-form">
									</form>
									<div class="links-warning">
										<div class="help-block">{!! Lang::get('corporate/signup.finish.stripe.warnings') !!}</div>
									</div>
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

			$('#stripe-form-trigger').on('click', function(e){
				e.preventDefault();
				stripeHandler.open({
				});
			});
		});
	</script>
@endsection
