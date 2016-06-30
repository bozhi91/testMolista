@extends('layouts.admin')

@section('content')

	<div class="container">
		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/config/plans.edit.title') }}</h1>

		@include('admin.config.plans.form', [ 
			'item' => $plan,
			'method' => 'PATCH',
			'action' => [ 'Admin\Config\PlansController@update', $plan->id ],
		])

	</div>

@endsection
