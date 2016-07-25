@extends('layouts.account')

@section('account_content')

	<div id="calendar-page" class="">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1>{{ Lang::get('account/calendar.title.create') }}</h1>

		@include('account.calendar.form', [ 
			'item' => @$defaults,
			'method' => 'POST',
			'action' => 'Account\Calendar\BaseController@postCreate',
		])

	</div>

@endsection