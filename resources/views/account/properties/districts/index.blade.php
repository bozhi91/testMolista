@extends('layouts.account')

@section('account_content')

	<div id="districts">

		@include('common.messages', [ 'dismissible'=>true ])

		<a href="{{ action(('Account\Properties\DistrictsController@getCreate')) }}" class="btn btn-primary pull-right">{{ Lang::get('account/properties.districts.create') }}</a>

		<h1 class="page-title">{{ Lang::get('account/properties.districts.h1') }}</h1>
		
		
		
	</div>
@endsection
