@extends( Input::get('ajax') ? 'layouts.popup' : 'layouts.account' )

@section( Input::get('ajax') ? 'content' : 'account_content' )

	@if ( Input::get('ajax') )
		<div id="account-container">
	@endif

	<div id="admin-customers" class="popup-container-like">

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

	@if ( Input::get('ajax') )
		</div>
	@endif

@endsection