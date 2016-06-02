@extends('layouts.account')

@section('account_content')

	<div id="account-tickets">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/tickets.new') }}</h1>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\TicketsController@postCreate', 'id'=>'ticket-form' ]) !!}

			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="error-container form-group">
						{!! Form::label('customer_id', Lang::get('account/tickets.contact.name')) !!}
						{!! form::select('customer_id', [ ''=>'&nbsp;' ]+$customers, null, [ 'class'=>'form-control required has-select-2']) !!}
					</div>
					<div class="error-container form-group">
						{!! Form::label('user_id', Lang::get('account/tickets.assigned.to')) !!}
						@if ( Auth::user()->hasRole('company') )
							{!! form::select('user_id', [ ''=>'&nbsp;' ]+$employees, null, [ 'class'=>'form-control has-select-2']) !!}
						@else
							{!! form::select('user_id', $employees, null, [ 'class'=>'form-control required', 'readonly'=>'readonly' ]) !!}
						@endif
					</div>
					<div class="error-container form-group">
						{!! Form::label('property_id', Lang::get('account/properties.column.title')) !!}
						{!! form::select('property_id', [ ''=>'&nbsp;' ]+$properties, null, [ 'class'=>'form-control has-select-2']) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('subject', Lang::get('account/tickets.subject')) !!}
						{!! Form::text('subject', null, [ 'class'=>'form-control required' ]) !!}
					</div>
					<div class="form-group error-container">
						{!! Form::label('body', Lang::get('account/tickets.body')) !!}
						{!! Form::textarea('body', null, [ 'class'=>'form-control required', 'rows'=>5 ]) !!}
					</div>
					<div class="text-right">
						<a href="{{ action('Account\TicketsController@getIndex') }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
						{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
					</div>
				</div>
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#ticket-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('.has-select-2').select2();

		});
	</script>

@endsection