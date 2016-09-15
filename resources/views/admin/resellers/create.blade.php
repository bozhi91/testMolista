@extends('layouts.admin')

@section('content')

<div class="container">

	@include('common.messages', [ 'dismissible'=>true ])

	{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Admin\ResellersController@store' ], 'id'=>'reseller-form' ]) !!}

		<h1 class="list-title">{{ Lang::get('admin/resellers.create.title') }}</h1>

		@include('admin.resellers.form')

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
		</div>

	{!! Form::close() !!}

</div>

@endsection