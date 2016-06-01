@extends('layouts.account')

@section('account_content')

	<div id="account-tickets">

		@include('common.messages', [ 'dismissible'=>true ])

		@if ( Auth::user()->can('property-create') && Auth::user()->canProperty('create') )
			<div class="pull-right">
				<a href="{{ action('Account\PropertiesController@create') }}" class="btn btn-primary">{{ Lang::get('account/tickets.new') }}</a>
			</div>
		@endif

		<h1 class="page-title">{{ Lang::get('account/tickets.h1') }}</h1>

		<div class="search-filters">
			@if ( !empty($clean_filters) )
				<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
			@endif
			<h2>{{ Lang::get('general.filters') }}</h2>
			{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
				{!! Form::hidden('limit', Input::get('limit')) !!}
				<div class="form-group">
					{!! Form::label('status', Lang::get('account/tickets.status'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('status', [
						'' => Lang::get('account/tickets.status.all'),
						'open' => Lang::get('account/tickets.status.open'),
						'waiting' => Lang::get('account/tickets.status.waiting'),
						'resolved' => Lang::get('account/tickets.status.resolved'),
						'closed' => Lang::get('account/tickets.status.closed'),
					], Input::get('status'), [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group">
					{!! Form::label('user_id', Lang::get('account/tickets.assigned.to'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('user_id', [
						'' => Lang::get('account/tickets.agents.all'),
						'null' => Lang::get('account/tickets.unassigned'),
					]+$employees, Input::get('user_id'), [ 'class'=>'form-control' ]) !!}
				</div>
				{!! Form::submit(Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::close() !!}
		</div>

		<div id="tickets-list">
			@include('account.tickets.list', [ 'pagination_url' => url()->full() ])
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#account-tickets');

			TICKETS.init('#tickets-list');

		});
	</script>

@endsection