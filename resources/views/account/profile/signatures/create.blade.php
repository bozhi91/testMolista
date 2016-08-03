@extends('account.profile.signatures.layout', [
	'page_title' => Lang::get('account/profile.signatures.h1.create')
])

@section('signatures_content')

	@include('account.profile.signatures.form', [ 
		'signature' => null,
		'method' => 'POST',
		'action' => action('Account\Profile\SignaturesController@postCreate'),
	])

@endsection
