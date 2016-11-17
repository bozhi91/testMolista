@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('admin/emails/stripe.payment_failed.subject') }}</h1>

	{!! Lang::get('admin/emails/stripe.payment_failed.body', [
		'site_id' => @$id,
		'subdomain' => @$subdomain,
		'created' => @date("d/m/Y", strtotime($paid_until)),
	]) !!}

@endsection
