@extends('layouts.account')

@section('account_content')

	<div id="admin-properties" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">{{ Lang::get('account/properties.edit.title') }}</h1>

	        @include('account.properties.form', [ 
	            'item' => $property,
	            'method' => 'PATCH',
	            'action' => [ 'Account\PropertiesController@update', $property->slug ],
	        ])

		</div>
	</div>

@endsection