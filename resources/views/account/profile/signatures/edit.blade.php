@extends('account.profile.signatures.layout', [
	'page_title' => Lang::get('account/profile.signatures.h1.edit')
])

@section('signatures_content')

	@include('account.profile.signatures.form', [ 
		'signature' => $signature,
		'method' => 'POST',
		'action' => action('Account\Profile\SignaturesController@postEdit', $signature->id),
	])

@endsection
