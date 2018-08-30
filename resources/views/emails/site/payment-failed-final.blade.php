@extends('layouts.email')

@section('content')

	{!! Lang::get('admin/emails/stripe.payment_failed_final.body', [
		'name' => $site->contact_name,
		'plan' => $site->plan->name,
		'sitename' => $site->title,
		'webname' => env('WHITELABEL_WEBNAME', 'Contromia'),
	]) !!}

	<p>Site: {{ $site->main_url }}</p>

@endsection
