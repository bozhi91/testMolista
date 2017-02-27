@extends('layouts.email')

@section('content')

	<p>{{ Lang::get('corporate/home.distributor.label.name') }}: {{ @$name }}</p>
	<p>{{ Lang::get('corporate/home.distributor.label.company') }}: {{ @$company }}</p>
	<p>{{ Lang::get('corporate/home.distributor.label.email') }}: {{ @$email }}</p>
	<p>{{ Lang::get('corporate/home.distributor.label.phone') }}: {{ @$phone }}</p>
	<p>{{ Lang::get('corporate/home.distributor.label.workers') }}: {{ @$workers }}</p>
	
	<br />
	{!! nl2p($mensaje) !!}

@endsection
