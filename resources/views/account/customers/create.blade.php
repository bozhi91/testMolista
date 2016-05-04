@extends('layouts.account')

@section('account_content')

	<div id="admin-customers">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/customers.create.h1') }}</h1>

		@include('web.customers.form', [
			'item' => null,
			'action' => 'Account\CustomersController@store',
			'method' => 'POST',
			'button' => Lang::get('general.save'),
			'filled_by_customer' => false,
		])

	</div>

@endsection