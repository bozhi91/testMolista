@extends('layouts.admin')

@section('content')

	<div class="container">

		<h1 class="list-title">{{ Lang::get('admin/menu.expirations') }}</h1>

		<div class="alert alert-success text-center">
			<h4>{{ Lang::get('general.messages.success.saved') }}</h4>
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-success' ]) !!}
		</div>

	</div>

@endsection
