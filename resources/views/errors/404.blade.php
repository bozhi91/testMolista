@extends('layouts.web')

@section('content')

	<div class="container">
		<div class="error-message">
			{{ Lang::get('errors.404.body') }}
		</div>
	</div>

@endsection
