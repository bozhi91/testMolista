@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.pages.create.title') }}</h1>

		@include('account.site.pages.form', [ 
			'item' => null,
			'method' => 'POST',
			'action' => 'Account\Site\PagesController@store',
		])

	</div>

@endsection