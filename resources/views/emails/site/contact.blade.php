@extends('layouts.email_corporate')

@section('content')

	<h1>{{ $subject }}</h1>

	<p>{{ Lang::get('web/pages.name') }}: <b>{{ @$name }}</b></p>
	<p>{{ Lang::get('web/pages.email') }}: <b>{{ @$email }}</b></p>
	<p>{{ Lang::get('web/pages.phone') }}: <b>{{ @$phone }}</b></p>
	@if (!empty($interest))
	<p>{{ Lang::get('web/pages.interest') }}: <b>{{ Lang::get('web/pages.interest.'.@$interest) }}</b></p>
	@endif
	{!! nl2p($body) !!}

@endsection
