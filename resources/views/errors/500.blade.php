@extends('layouts.web')

@section('content')

	<div class="container">
		<div class="error-message">
			{{ Lang::get('errors.500.body') }}
		</div>
	</div>

@endsection
