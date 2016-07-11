@extends('layouts.email')

@section('content')

	<div class="container">

		<div class="content">

			<div class="header">
				<a href="{{ LaravelLocalization::getLocalizedURL(null, '/') }}" target="_blank"><img src="{{ asset('images/emails/logo.png') }}" alt="" /></a>
			</div>

			<div class="h1">{{ Lang::get('corporate/signup.email.subject') }}</div>

			<div class="text-gray">
				{!! Lang::get('corporate/signup.email.hello', [
					'name' => @$owner_name,
				]) !!}
			</div>

			@if ( empty($pending_request) )
				<div class="text-gray">
					{!! Lang::get('corporate/signup.email.features.intro.now') !!}
				</div>
				{!! Lang::get('corporate/signup.email.features.list') !!}
			@elseif ( $pending_request['payment_method'] == 'stripe' )
				<div class="text-gray">
					{!! Lang::get('corporate/signup.email.features.intro.stripe', [
						'plan' => @$pending_request->plan_name,
						'price' => @$pending_request->plan_price,
						'period' => @$pending_request->payment_interval,
						'priceperiod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
						'paymethod' => Lang::get('account/payment.method.stripe'),
					]) !!}
				</div>
				{!! Lang::get('corporate/signup.email.features.list') !!}
			@elseif ( $pending_request['payment_method'] == 'transfer' )
				<div class="text-gray">
					{!! Lang::get('corporate/signup.email.features.intro.transfer', [
						'plan' => @$pending_request->plan_name,
						'price' => @$pending_request->plan_price,
						'period' => @$pending_request->payment_interval,
						'priceperiod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
						'paymethod' => Lang::get('account/payment.method.transfer'),
						'iban' => @$pending_request->iban_account,
					]) !!}
				</div>
				{!! Lang::get('corporate/signup.email.features.list') !!}
			@endif

			<div class="text-center website-block">
				{!! Lang::get('corporate/signup.email.url.site', [
					'site_url' => @$site_url,
				]) !!}
				<p>
					<a href="{{ @$site_url }}" target="_blank" class="website-link">{{ @$site_url}}</a>
				</p>
			</div>

			{!! Lang::get('corporate/signup.email.url.account', [
				'account_url' => @$account_url,
				'email' => @$owner_email,
			]) !!}

			@if ( @$pending_request['payment_method'] == 'stripe' )
				<div class="payment-completed">
					{!! Lang::get('corporate/signup.email.warning.stripe') !!}
				</div>
			@endif

		</div>

		<div class="footer">
			<a href="{{ env('LINKS_FACEBOOK', 'https://www.facebook.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-facebook.png') }}" alt="" /></a>
			<a href="{{ env('LINKS_LINKEDIN', 'https://www.linkedin.com/') }}" target="_blank"><img src="{{ asset('images/emails/footer-linkedin.png') }}" alt="" /></a>
		</div>

	</div>

@endsection
