@extends('layouts.account', [
	'use_google_maps' => true,
])

@section('account_content')

	<div id="admin-properties" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">{{ Lang::get('account/properties.create.title') }}</h1>

	        @include('account.properties.form', [ 
	            'item' => null,
	            'method' => 'POST',
	            'action' => 'Account\PropertiesController@store',
	        ])

		</div>
	</div>

@endsection