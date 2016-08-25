@extends('account.profile.accounts.layout', [
	'page_title' => Lang::get('account/profile.accounts.h1.edit')
])

@section('email_accounts_content')

	@include('account.profile.accounts.form', [ 
		'account' => $account,
		'method' => 'POST',
		'action' => action('Account\Profile\AccountsController@postEdit', $account->id),
	])

@endsection
