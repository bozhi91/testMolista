@extends('layouts.admin')

@section('content')

	<div class="container">
		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/config/plans.create.title') }}</h1>

		@include('admin.config.plans.form', [ 
			'item' => null,
			'method' => 'POST',
			'action' => 'Admin\Config\PlansController@store',
		])

	</div>

@endsection
