@extends('account.profile.signatures.layout')

@section('signatures_content')

	@if ( $signatures->count() < 1 )
		<div class="alert alert-info">{{ Lang::get('account/profile.signatures.empty') }}</div>
	@else
	@endif

@endsection
