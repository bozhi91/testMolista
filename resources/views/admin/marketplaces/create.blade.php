@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/marketplaces.create.title') }}</h1>

		@include('admin.marketplaces.form', [
			'item' => null,
			'method' => 'POST',
			'action' => 'Admin\MarketplacesController@store',
		])

	</div>

@endsection