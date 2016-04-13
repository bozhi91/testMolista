@extends('layouts.admin')

@section('content')

<div class="container">

	@include('common.messages', [ 'dismissible'=>true ])

	{!! Form::model($user, [ 'method'=>'PATCH', 'action'=>[ 'Admin\UsersController@update', $user->id ], 'id'=>'user-form' ]) !!}

		<h1 class="list-title">{{ Lang::get('admin/users.edit.title') }}</h1>

		@include('admin.users.form')

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
		</div>

	{!! Form::close() !!}

</div>

@endsection