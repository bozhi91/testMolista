@extends('layouts.email')

@section('content')

	{!! Lang::get('admin/emails/stripe.payment_failed_warning.body', [
		'name' => $site->contact_name,
		'plan' => $site->plan->name,
		'next_attempt' => date('d/m/Y', $next_payment_attempt),
		'webname' => env('WHITELABEL_WEBNAME', 'Contromia'),
	]) !!}

	<p>Site: {{ $site->main_url }}</p>

@endsection
