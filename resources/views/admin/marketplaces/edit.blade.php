@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/marketplaces.edit.title') }}</h1>

		@include('admin.marketplaces.form', [
			'item' => $marketplace,
			'method' => 'PATCH',
			'action' => [ 'Admin\MarketplacesController@update', $marketplace->id ],
		])

	</div>

@endsection