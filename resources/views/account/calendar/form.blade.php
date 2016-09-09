<style type="text/css">
	#calendar-form .help-block .checkbox { border: none; background: none; padding: 0px; line-height: 20px; }
</style>

{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'calendar-form' ]) !!}
	<input type="hidden" name="calendar_defaultView" value="{{ Input::get('calendar_defaultView') }}" />
	<input type="hidden" name="calendar_defaultDate" value="{{ Input::get('calendar_defaultDate') }}" />

	<div class="row">
		<div class="col-xs-12">
			<div class="form-group error-container">
				{!! Form::label('title', Lang::get('account/calendar.title')) !!}
				{!! Form::text('title', null, [ 'class'=>'title-input form-control required' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('start_time', Lang::get('account/calendar.start')) !!}
				{!! Form::text('start_time', null, [ 'class'=>'datetimepicker-start form-control required' ]) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('end_time', Lang::get('account/calendar.end')) !!}
				{!! Form::text('end_time', null, [ 'class'=>'datetimepicker-end form-control required' ]) !!}
			</div>
			<div class="form-group error-container">
				{!! Form::label('type', Lang::get('account/calendar.type')) !!}
				{!! Form::select('type', [ ''=>'' ]+$types, null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('comments', Lang::get('account/calendar.comments')) !!}
				{!! Form::textarea('comments', null, [ 'class'=>'form-control', 'rows' => 9 ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('user_ids[]', Lang::get('account/calendar.agent')) !!}
				{!! Form::select('user_ids[]', $users, null, [ 'class'=>'has-select-2 form-control required', 'multiple'=>'multiple', 'size'=>1 ]) !!}
			</div>
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('customer_id', Lang::get('account/calendar.customer')) !!}
					{!! Form::select('customer_id', [ ''=>'&nbsp;' ]+$customers, null, [ 'class'=>'customer-select has-select-2 form-control' ]) !!}
				</div>
				<div class="help-block customer-title-area hide">
					<div class="checkbox">
						<label>
							{!! Form::checkbox('customer_title', 1, null, [ 'class'=>'customer-title-input' ]) !!}
							{{ Lang::get('account/calendar.title.customer') }}
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('location', Lang::get('account/calendar.location')) !!}
				{!! Form::text('location', null, [ 'class'=>'location-input form-control' ]) !!}
			</div>
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('property_ids[]', Lang::get('account/calendar.property')) !!}
					<select name="property_ids[]" class="property-select has-select-2 form-control" multiple="multiple" size="1">
						@include('account.calendar.form-properties-options', [ 
							'properties' => $properties,
							'selected_ids' => old('property_ids', Input::get('property_ids', @$item->property_ids)),
						])
					</select>
				</div>
				<div class="help-block location-property-area hide">
					<div class="checkbox">
						<label>
							{!! Form::checkbox('property_location', 1, null, [ 'class'=>'property-location-input' ]) !!}
							{{ Lang::get('account/calendar.location.property') }}
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			@if ( @$current_site->mailer['service'] != 'custom' )
				<div class="alert alert-warning">{!! Lang::get('account/calendar.notify.warning') !!}</div>
			@endif
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="text-right">
				@if ( @$goback )
					<a href="{{ $goback }}" class="btn btn-default">{{ Lang::get('general.back') }}</a>
				@endif
				@if ( @$item->id )
					<a href="#" class="btn btn-danger delete-event-trigger">{{ Lang::get('general.delete') }}</a>
				@endif
				{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
			</div>
		</div>
	</div>

{!! Form::close() !!}

@if ( @$item->id )
	{!! Form::open([ 'action'=>[ 'Account\Calendar\BaseController@deleteEvent', $item->id ], 'method'=>'DELETE', 'id'=>'delete-event-form' ]) !!}
	{!! Form::close() !!}
@endif

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#calendar-form');

		$.validator.addMethod('endmindate',function(v,el){
			var startTime = form.find('.datetimepicker-start').data("DateTimePicker").date();
			//var endDate = form.find('.datetimepicker-end').data("DateTimePicker").date();
			var endTime = $(el).data("DateTimePicker").date();

			if ( !startTime || !endTime ) {
				return true;
			}

			return startTime < endTime;
		}, "{{ print_js_string( Lang::get('account/calendar.end.error') ) }}");

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			rules: {
				end_time: {
					endmindate: true
				}
			},
			messages: {
				end_time: {
					endmindate: "{{ print_js_string( Lang::get('account/calendar.end.error') ) }}"
				}
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

        if ( form.find('.datetimepicker-start').val() ) {
        	form.find('.datetimepicker-start').trigger('dp.change');
        }

		form.find('.has-select-2').select2().on("select2:unselecting", function(e) {
			$(this).data('state', 'unselected');
		}).on("select2:open", function(e) {
			var el = $(this);
			if ( el.data('state') === 'unselected' ) {
				el.removeData('state'); 
				setTimeout(function() {
					el.select2('close');
				}, 1);
			}
		});

		form.find('.property-select').on('change', function(){
			var opt = $(this).find('option:selected');
			if ( opt.length == 1 && opt.data().location ) {
				form.find('.location-property-area').removeClass('hide');
			} else {
				form.find('.location-property-area').addClass('hide').find('.property-location-input').prop('checked', false);
			}
		}).trigger('change');

		form.on('change', '.property-location-input', function(){
			if ( $(this).is(':checked') ) {
				form.find('.location-input').val( form.find('.property-select option:selected').data().location );
			}
		});

		form.find('.customer-select').on('change', function(){
			if ( $(this).val() ) {
				form.find('.customer-title-area').removeClass('hide');
			} else {
				form.find('.customer-title-area').addClass('hide').find('.customer-title-input').prop('checked', false);
			}
		}).trigger('change');

		form.on('change', '.customer-title-input', function(){
			if ( $(this).is(':checked') ) {
				form.find('.title-input').val( form.find('.customer-select option:selected').text() );
			}
		});

		form.on('click', '.delete-event-trigger', function(e){
			e.preventDefault();
			$('#delete-event-form').submit();
		});

		$('#delete-event-form').validate({
			submitHandler: function(f) {
				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/calendar.delete.warning') ) }}", function (e) {
					if (e) {
						LOADING.show();
						f.submit();
					}
				});
			}
		});

	});
</script>