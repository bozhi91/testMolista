@extends('layouts.account')

@section('account_content')

	<div id="calendar-page" class="">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1>{{ Lang::get('account/calendar.title.edit') }}</h1>

		@include('account.calendar.form', [ 
			'item' => $event,
			'method' => 'POST',
			'action' => [ 'Account\Calendar\BaseController@postEvent',  $event->id ],
		])

	</div>

@endsection