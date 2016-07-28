@extends('layouts.email')

@section('content')

	{!! Lang::get('admin/planchange.accept.body', [
		'username' => $owner_name,
		'siteurl' => $site_url,
		'plan' => $pending_request->plan->name,
		'payment' => Lang::get("web/plans.price.{$pending_request->payment_interval}") . ' ' . price($pending_request->plan_price, $pending_request->plan->infocurrency->toArray()),
	]) !!}

@endsection
