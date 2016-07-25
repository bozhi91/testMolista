{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'calendar-form' ]) !!}
	<input type="hidden" name="calendar_defaultView" value="{{ Input::get('calendar_defaultView') }}" />
	<input type="hidden" name="calendar_defaultDate" value="{{ Input::get('calendar_defaultDate') }}" />

	<div class="row">
		<div class="col-xs-12">
			<div class="form-group error-container">
				{!! Form::label('title', Lang::get('account/calendar.title')) !!}
				{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('start_time', Lang::get('account/calendar.start')) !!}
				{!! Form::text('start_time', null, [ 'class'=>'datetimepicker-start form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('end_time', Lang::get('account/calendar.end')) !!}
				{!! Form::text('end_time', null, [ 'class'=>'datetimepicker-end form-control required' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('type', Lang::get('account/calendar.type')) !!}
				{!! Form::select('type', [ ''=>'' ]+$types, null, [ 'class'=>'form-control required' ]) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('user_id', Lang::get('account/calendar.agent')) !!}
				{!! Form::select('user_id', [ ''=>'&nbsp;' ]+$users, null, [ 'class'=>'has-select-2 form-control required' ]) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('property_id', Lang::get('account/calendar.property')) !!}
				{!! Form::select('property_id', [ ''=>'&nbsp;' ]+$properties, null, [ 'class'=>'has-select-2 form-control' ]) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('customer_id', Lang::get('account/calendar.customer')) !!}
				{!! Form::select('customer_id', [ ''=>'&nbsp;' ]+$customers, null, [ 'class'=>'has-select-2 form-control' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('comments', Lang::get('account/calendar.comments')) !!}
				{!! Form::textarea('comments', null, [ 'class'=>'form-control', 'rows' => 6 ]) !!}
			</div>
			@if ( @$site->mailer['service'] == 'custom' )
				<div class="form-group error-container">
					<div class="checkbox">
						<label>
							{!! Form::checkbox('notify', 1) !!}
							{{ Lang::get('account/calendar.notify') }}
						</label>
					</div>
				</div>
			@else
				<div class="alert alert-warning">{!! Lang::get('account/calendar.notify.warning') !!}</div>
			@endif
		</div>
	</div>

	<div class="text-right">
		@if ( @$goback )
			<a href="{{ $goback }}" class="btn btn-default">{{ Lang::get('general.cancel') }}</a>
		@endif
		{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
	</div>

{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#calendar-form');

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

        form.find('.datetimepicker-start').datetimepicker({
        	format: 'YYYY-MM-DD HH:mm'
        });
        form.find('.datetimepicker-end').datetimepicker({
        	format: 'YYYY-MM-DD HH:mm',
            useCurrent: false //Important! See issue #1075
        });
        form.find('.datetimepicker-start').on("dp.change", function (e) {
            form.find('.datetimepicker-end').data("DateTimePicker").minDate(e.date);
        });
        form.find('.datetimepicker-end').on("dp.change", function (e) {
            form.find('.datetimepicker-start').data("DateTimePicker").maxDate(e.date);
        });

		form.find('.has-select-2').select2();
	});
</script>