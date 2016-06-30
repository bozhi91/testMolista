@extends('layouts.email')

@section('content')

	<p>{{ Lang::get('corporate/general.contact.name') }}: {{ @$name }}</p>
	<p>{{ Lang::get('corporate/general.contact.email') }}: {{ @$email }}</p>
	<p>{{ Lang::get('corporate/general.contact.phone') }}: {{ @$phone }}</p>
	<br />
	{!! nl2p($details) !!}

@endsection
