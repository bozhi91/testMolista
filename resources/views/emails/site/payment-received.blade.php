@extends('layouts.email')

@section('content')

	{!! Lang::get('admin/emails/stripe.payment_succeeded.body', [
		'name' => $site->contact_name,
		'plan' => $site->plan->name,
		'start' => date('d/m/Y', strtotime($paid_from)),
		'end' => date('d/m/Y', strtotime($paid_until)),
	]) !!}

	<p>Site: {{ $site->main_url }}</p>

@endsection
