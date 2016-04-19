{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'advanced-search-form' ]) !!}
	{!! Form::hidden('search', 1) !!}
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::text('term', Input::get('term'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$search_data['states'], Input::get('state'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				@if ( empty($search_data_cities) )
					{!! Form::select('city', [''=>Lang::get('web/properties.city')], null, [ 'class'=>'form-control has-placeholder' ]) !!}
				@else
					{!! Form::select('city', [''=>Lang::get('web/properties.city')]+$search_data_cities, Input::get('city'), [ 'class'=>'form-control has-placeholder' ]) !!}
				@endif
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$search_data['modes'], Input::get('mode'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$search_data['types'], Input::get('type'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="newly_build" {{ Input::get('newly_build') ? 'checked="checked"' : '' }} />
						{{ Lang::get('web/properties.labels.new') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="second_hand" {{ Input::get('second_hand') ? 'checked="checked"' : '' }} />
						{{ Lang::get('web/properties.labels.used') }}
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-2">
			<div class="form-group error-container">
				{!! Form::hidden('currency', 'EUR') !!}
				{!! Form::select('price', [''=>Lang::get('web/properties.more.price')]+$search_data['prices'], Input::get('price'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-2">
			<div class="form-group error-container">
				{!! Form::hidden('size_unit', 'sqm') !!}
				{!! Form::select('size', [''=>Lang::get('web/properties.more.sqm')]+$search_data['sizes'], Input::get('size'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-2">
			<div class="form-group error-container">
				{!! Form::select('rooms', [''=>Lang::get('web/properties.more.rooms')]+$search_data['rooms'], Input::get('rooms'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-2">
			<div class="form-group error-container">
				{!! Form::select('baths', [''=>Lang::get('web/properties.more.baths')]+$search_data['baths'], Input::get('baths'), [ 'class'=>'form-control has-placeholder' ]) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('services[]', Lang::get('web/properties.services')) !!}
				{!! Form::select('services[]', $search_data['services'], Input::get('services'), [ 'class'=>'form-control has-select-2', 'multiple'=>'multiple', 'size'=>'1' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<label class="hidden-xs">&nbsp;</label>
			<div class="text-right">
				{!! Form::submit(Lang::get('web/home.search.button'), [ 'class'=>'btn btn-submit btn-warning text-uppercase' ]) !!}
			</div>
		</div>
	</div>
	<div class="row">
	</div>
{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#advanced-search-form');
		var cities = {};

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

		form.find('.checkbox').each(function(){
			var el = $(this);
			var trigger = el.find('input');

			trigger.on('change', function(){
				if ( trigger.is(':checked') ) {
					el.removeClass('is-placeholder');
				} else {
					el.addClass('is-placeholder');
				}
			});

			if ( !trigger.is(':checked') ) {
				el.addClass('is-placeholder');
			}
		});

		form.on('change', 'select[name="state"]', function(){
			var state = $(this).val();
			var target = form.find('select[name="city"]');

			target.html('<option value="">' + target.find('option[value=""]').eq(0).text() + '</option>').addClass('is-placeholder');
			if ( !state ) {
				return;
			}

			if ( cities.hasOwnProperty(state) ) {
				$.each(cities[state], function(k,v) {
					target.append('<option value="' + v.code + '">' + v.label + '</option>');
				});
			} else {
				$.ajax({
					dataType: 'json',
					url: '{{ action('Ajax\GeographyController@getSuggest', 'city') }}',
					data: { state_slug: state },
					success: function(data) {
						if ( data ) {
							cities[state] = data;
							$.each(cities[state], function(k,v) {
								target.append('<option value="' + v.code + '">' + v.label + '</option>');
							});
						}
					}
				});
			}
		});


		form.find('select.has-select-2').each(function(){
			$(this).select2();
		});

		$(window).resize(function(){
			form.find('select.has-select-2').each(function(){
				$(this).select2();
			});
		});
	});
</script>
