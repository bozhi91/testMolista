@extends('layouts.account')

@section('account_content')

	<div id="calendar-page" class="">

        @include('common.messages', [ 'dismissible'=>true ])

		<div class="pull-right">
			<a href="{{ action('Account\Calendar\BaseController@getCreate') }}" class="btn btn-primary btn-calendar-event-new">{{ Lang::get('account/calendar.button.new') }}</a>
		</div>

		<h1 class="page-title">{{ Lang::get('account/calendar.h1') }}</h1>

		<div class="search-filters">
			@if ( !empty($clean_filters) )
				<a href="?limit={{ Input::get('limit') }}" class="text-bold pull-right">{{ Lang::get('general.filters.clean') }}</a>
			@endif
			<h2>{{ Lang::get('general.filters') }}</h2>
			{!! Form::open([ 'method'=>'GET', 'class'=>'form-inline', 'id'=>'filters-form' ]) !!}
				{!! Form::hidden('calendar_defaultView', Input::get('calendar_defaultView','agendaWeek'), [ 'id'=>'calendar-defaultView', 'class'=>'filter-value' ]) !!}
				{!! Form::hidden('calendar_defaultDate', Input::get('calendar_defaultDate', date('Y-m-d')), [ 'id'=>'calendar-defaultDate', 'class'=>'filter-value' ]) !!}
				<div class="form-group">
					{!! Form::label('agent', Lang::get('account/calendar.agent'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('agent', [ ''=>Lang::get('account/calendar.agent.all')]+$employees, Input::get('agent'), [ 'class'=>'filter-value form-control' ]) !!}
				</div>
				<div class="form-group">
					{!! Form::label('status', Lang::get('account/calendar.status'), [ 'class'=>'sr-only' ]) !!}
					{!! Form::select('status', [ ''=>Lang::get('account/calendar.status.all') ], Input::get('status'), [ 'class'=>'filter-value form-control' ]) !!}
				</div>
				{!! Form::submit(Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::close() !!}
		</div>

		<div class="calendar-area">
			<div class="row">
				<div class="col-xs-12 col-sm-10">
					<div class="calendar-item"></div>
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="fc-toolbar">
						<h2>&nbsp;</h2>
					</div>
					<div class="calendar-reference">
						<h3>{{ Lang::get('account/calendar.reference.title') }}</h3>
						<ul class="list-unstyled calendar-reference">
							<li class="event-type-visit">{{ Lang::get('account/calendar.reference.type.visit') }}</li>
							<li class="event-type-catch">{{ Lang::get('account/calendar.reference.type.catch') }}</li>
							<li class="event-type-interview">{{ Lang::get('account/calendar.reference.type.interview') }}</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div id="event-summary-modal" class="mfp-hide app-popup-block-white"></div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#calendar-page');
			var filters = $('#filters-form');
			var calendar = cont.find('.calendar-item');

			filters.validate({
				submitHandler: function(f) {
					calendar.fullCalendar('refetchEvents');
				}
			});

			calendar.fullCalendar({
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				defaultView: '{{ Input::get('calendar_defaultView') ? Input::get('calendar_defaultView') : 'agendaWeek' }}',
				defaultDate: '{{ Input::get('calendar_defaultDate') ? Input::get('calendar_defaultDate') : date('Y-m-d') }}',
				firstDay: 1,
				allDaySlot: false,
				events: {
					url: '{{ action('Account\Calendar\BaseController@getEvents') }}',
					data : function() {
						var data = {};
						$.each(filters.serializeArray(), function(k,v){
							data[v.name] = v.value;
						});
						return data;
					}
				},
				eventClick: function(event) {
					if (event.summary) {
						$('#event-summary-modal').html(event.summary);
						$.magnificPopup.open({
							items: {
								src: '#event-summary-modal'
							},
							type: 'inline'
						});
					}
					return false;
				},
				viewRender: function(view,element) {
					$('#calendar-defaultView').val(view.type);
					$('#calendar-defaultDate').val(view.intervalStart.format('YYYY-MM-DD'));
				},
				loading: function( isLoading, view ) {
					if (isLoading) {
						LOADING.show();
					} else {
						LOADING.hide();
					}
				}
			});

			function addFilterValues(url) {
				cont.find('.search-filters .filter-value').each(function(){
					url = SITECOMMON.addUriParam(url, $(this).attr('name'), $(this).val());
				});
				return url;
			}

			cont.on('click', '.btn-calendar-event-new', function(e){
				$(this).attr('href', addFilterValues( $(this).attr('href') ));
			});
		});
	</script>

@endsection