@extends('layouts.account', [
	'hide_pending_request_warning' => 1,
])

@section('account_content')

	<div id="update-credit-card">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/payment.cc.update.title') }}</h1>

		<script src="https://checkout.stripe.com/checkout.js"></script>
		<script type="text/javascript">
			var stripeHandler = StripeCheckout.configure({
				key: '{{ env('STRIPE_KEY') }}',
				name: 'Molista',
				description: '{!! Lang::get('account/payment.cc.update.title') !!}',
				locale: '{{ LaravelLocalization::getCurrentLocale() }}',
				email: '{{ $user_email }}',
				panelLabel: '{!! Lang::get('account/payment.cc.update.label') !!}',
				opened: function() {},
				closed: function() {},
				token: function(token, args) {
					var form = $('#stripe-form');
					if ( form.find('input[name="stripeToken"]').length < 1 ) {
						form.append('<input type="hidden" name="stripeToken" />');
					}
					form.find('input[name="stripeToken"]').val(token.id);
					LOADING.show();
					form.submit();
				}
			});
		</script>

		<div>
			{!! Lang::get('account/payment.cc.update.intro') !!}
		</div>
		<div class="alert text-center">
			<a href="#" id="stripe-form-trigger" class="btn btn-primary">{{ Lang::get('account/payment.cc.update.button') }}</a>
		</div>

		{!! Form::open([ 'action'=>'Account\PaymentController@postUpdateCreditCard', 'method'=>'POST', 'id'=>'stripe-form' ]) !!}
		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			$('#stripe-form-trigger').on('click', function(e){
				e.preventDefault();
				stripeHandler.open();
			});
		});
	</script>

@endsection
