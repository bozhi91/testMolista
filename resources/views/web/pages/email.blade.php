@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('web/pages.contact.email.subject') }}</h1>

	<p>{{ Lang::get('web/pages.name') }}: {{ @$name }}</p>
	<p>{{ Lang::get('web/pages.email') }}: {{ @$email }}</p>
	{!! nl2p($body) !!}

@endsection
