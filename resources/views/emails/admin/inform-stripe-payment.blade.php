@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('corporate/signup.email.stripe.subject') }}</h1>

	{!! Lang::get('corporate/signup.email.stripe.body', [
		'site_id' => @$id,
		'subdomain' => @$subdomain,
		'created' => @date("d/m/Y", strtotime($paid_until)),
	]) !!}

@endsection
