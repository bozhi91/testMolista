@extends('account.profile.accounts.layout', [
	'page_title' => Lang::get('account/profile.accounts.h1.create')
])

@section('email_accounts_content')

	@include('account.profile.accounts.form', [ 
		'account' => null,
		'method' => 'POST',
		'action' => action('Account\Profile\AccountsController@postCreate'),
	])

@endsection
