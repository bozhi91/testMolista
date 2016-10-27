@extends('layouts.email')

@section('content')

	<div class="container">
		<div class="content">			
			<p>{{ Lang::get('account/properties.email.sold.intro', ['name' => $customer->fullName]) }}</p>
			<p>{{ $title }}.</p>
			<p>{{ Lang::get('account/properties.email.sold.body') }}</p>
		</div>
	</div>

@endsection