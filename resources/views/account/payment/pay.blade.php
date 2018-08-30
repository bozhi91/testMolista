@extends('layouts.account', [
	'hide_pending_request_warning' => 1,
])

@section('account_content')

	<div id="plan-upgrade">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/payment.invoicing.created') }}</h1>

		@if ( $pending_request->summary->payment_method == 'stripe' )
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					{!! Lang::get('corporate/signup.finish.pay') !!}
					{!! Lang::get('corporate/signup.finish.plan.details', [
						'plan' => @$pending_request->summary->plan_name,
						'price_text' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, $pending_request->plan->infocurrency->toArray()),
					]) !!}
					<br />

					<script src="https://checkout.stripe.com/checkout.js"></script>
					<script type="text/javascript">
						var stripeHandler = StripeCheckout.configure({
							key: '{{ env('STRIPE_KEY') }}',
							amount: '{{ round($pending_request->plan_price*100) }}',
							currency: '{{ $pending_request->plan_currency }}',
							name: 'Contromia',
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
					{!! Form::open([ 'action'=>'Account\PaymentController@postPay', 'method'=>'POST', 'id'=>'stripe-form' ]) !!}
					{!! Form::close() !!}
					<div class="links-warning">
						<div class="help-block">{!! Lang::get('corporate/signup.finish.stripe.warnings') !!}</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-4 col-sm-offset-2">
					{!! Lang::get('corporate/signup.finish.our.help', [
						'phone' => Config::get('app.phone_support'),
					]) !!}
				</div>
			</div>
		@elseif ( $pending_request->summary->payment_method == 'transfer' )
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					{!! Lang::get('account/payment.invoicing.transfer.intro', [
						'plan' => @$pending_request->summary->plan_name,
						'price_text' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, $pending_request->plan->infocurrency->toArray()),
					]) !!}
				</div>
				<div class="col-xs-12 col-sm-4 col-sm-offset-2">
					{!! Lang::get('corporate/signup.finish.our.help', [
						'phone' => Config::get('app.phone_support'),
					]) !!}
				</div>
			</div>
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#upgrade-form');

			$('#stripe-form-trigger').on('click', function(e){
				e.preventDefault();
				stripeHandler.open({
				});
			});
		});
	</script>
@endsection
