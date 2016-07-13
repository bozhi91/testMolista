@extends('layouts.email')

@section('content')

	<p>{{ Lang::get('account/marketplaces.contact.name') }}: {{ $name }}</p>
	<p>{{ Lang::get('account/marketplaces.contact.email') }}: {{ $email }}</p>
	<p>{{ Lang::get('account/marketplaces.contact.phone') }}: {{ $phone }}</p>
	<hr />
	<p>Site ID: {{ $site_id }}</p>
	<p>Site URL:  {{ $site_url }}</p>

@endsection
