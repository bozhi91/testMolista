@extends('layouts.email')

@section('content')

	{!! Lang::get('corporate/signup.email.hello', [
		'name' => @$owner_name,
	]) !!}

	@if ( empty($pending_request) )
		{!! Lang::get('corporate/signup.email.features.intro.now') !!}
		{!! Lang::get('corporate/signup.email.features.list') !!}
	@elseif ( $pending_request['payment_method'] == 'stripe' )
		{!! Lang::get('corporate/signup.email.features.intro.stripe', [
			'plan' => @$pending_request->plan_name,
			'price' => @$pending_request->plan_price,
			'period' => @$pending_request->payment_interval,
			'priceperiod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
			'paymethod' => Lang::get('account/payment.method.stripe'),
		]) !!}
		{!! Lang::get('corporate/signup.email.features.list') !!}
	@elseif ( $pending_request['payment_method'] == 'transfer' )
		{!! Lang::get('corporate/signup.email.features.intro.transfer', [
			'plan' => @$pending_request->plan_name,
			'price' => @$pending_request->plan_price,
			'period' => @$pending_request->payment_interval,
			'priceperiod' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, [ 'decimals'=>0 ]),
			'paymethod' => Lang::get('account/payment.method.transfer'),
			'iban' => @$pending_request->iban_account,
		]) !!}
		{!! Lang::get('corporate/signup.email.features.list') !!}
	@endif

	{!! Lang::get('corporate/signup.email.url.site', [
		'site_url' => @$site_url,
	]) !!}

	{!! Lang::get('corporate/signup.email.url.account', [
		'account_url' => @$account_url,
		'email' => @$owner_email,
	]) !!}

	@if ( @$pending_request['payment_method'] == 'stripe' )
		<p>&nbsp;</p>
		{!! Lang::get('corporate/signup.email.warning.stripe') !!}
	@endif

@endsection
