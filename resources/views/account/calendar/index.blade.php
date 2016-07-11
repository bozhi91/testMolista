@extends('layouts.account')

@section('account_content')

	<div id="calendar-page" class="">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/calendar.h1') }}</h1>

		<div class="search-filters">
			@if ( !empty($clean_filters) )
				<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
			@endif
			<h2>{{ Lang::get('general.filters') }}</h2>
			{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
				{!! Form::hidden('calendar_defaultView', Input::get('calendar_defaultView','agendaWeek'), [ 'id'=>'calendar-defaultView' ]) !!}
				{!! Form::hidden('calendar_defaultDate', Input::get('calendar_defaultDate', date('Y-m-d')), [ 'id'=>'calendar-defaultDate' ]) !!}
				<div class="form-group">
					{!! Form::label('agent', Lang::get('account/calendar.agent'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('agent', [ ''=>Lang::get('account/calendar.agent.all')], Input::get('agent'), [ 'class'=>'form-control' ]) !!}
				</div>
				<div class="form-group">
					{!! Form::label('status', Lang::get('account/calendar.status'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('status', [ ''=>Lang::get('account/calendar.status.all') ], Input::get('status'), [ 'class'=>'form-control' ]) !!}
				</div>
				{!! Form::submit(Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::close() !!}
		</div>

		<div class="calendar-area">
			<div class="calendar-item"></div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#calendar-page');
			var calendar = cont.find('.calendar-item');

			calendar.fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				defaultView: '{{ Input::get('calendar_defaultView','agendaWeek') }}',
				defaultDate: '{{ Input::get('calendar_defaultDate',date('Y-m-d')) }}',
				firstDay: 1,
				allDaySlot: false,
				events: '{{ action('Account\Calendar\BaseController@getEvents') }}',
				eventClick: function(event) {
					if (event.url) {
alert(event.url)
					}
					return false;
				},
				viewRender: function(view,element) {
					$('#calendar-defaultView').val(view.type);
					$('#calendar-defaultDate').val(view.intervalStart.format('YYYY-MM-DD'));
				}
			});
		});
	</script>

@endsection