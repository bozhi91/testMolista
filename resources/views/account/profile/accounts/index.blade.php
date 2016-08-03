@extends('account.profile.accounts.layout')

@section('email_accounts_content')

	@if ( count($accounts) < 1 )
		<div class="alert alert-info">{{ Lang::get('account/profile.accounts.empty') }}</div>
	@else
	@endif

@endsection
