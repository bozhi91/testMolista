@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('corporate/signup.email.admin.subject') }}</h1>

	{!! Lang::get('corporate/signup.email.admin.body', [
		'site_id' => @$id,
		'subdomain' => @$subdomain,
		'created' => @date("d/m/Y", strtotime($created_at)),
	]) !!}

@endsection
