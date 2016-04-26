@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.pages.edit.title') }}</h1>

		@include('account.site.pages.form', [ 
			'item' => $page,
			'method' => 'PATCH',
			'action' => [ 'Account\Site\PagesController@update', $page->slug ],
		])

	</div>

@endsection