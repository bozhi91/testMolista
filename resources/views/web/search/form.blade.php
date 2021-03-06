{!! Form::model(null, [ 'action'=>'Web\PropertiesController@index', 'method'=>'GET', 'id'=>'advanced-search-form' ]) !!}
	{!! Form::hidden('search', 1) !!}
	<div class="row">
		<div class="col-xs-12 col-sm-4 col-md-3 input-line first-input-line">
			<div class="form-group error-container">
				{!! Form::text('term', Input::get('term'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/properties.term') ]) !!}
			</div>
		</div>
		@if ( !empty($search_data['states']) )
			<div class="col-xs-12 col-sm-4 col-md-3 input-line">
				<div class="form-group error-container">
					{!! Form::select('state', [''=>Lang::get('web/properties.state')]+$search_data['states'], Input::get('state'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		<div class="col-xs-12 col-sm-4 col-md-3 input-line">
			<div class="form-group error-container">
				@if ( empty($search_data_cities) )
					{!! Form::select('city', [''=>Lang::get('web/properties.city')], null, [ 'class'=>'form-control has-placeholder' ]) !!}
				@else
					{!! Form::select('city', [''=>Lang::get('web/properties.city')]+$search_data_cities, Input::get('city'), [ 'class'=>'form-control has-placeholder' ]) !!}
				@endif
			</div>
		</div>
		
		@if ( !empty($search_data['districts']) )
			<div class="col-xs-12 col-sm-4 col-md-3 input-line">
				<div class="form-group error-container">
					{!! Form::select('district', [''=>Lang::get('web/properties.district')]+$search_data['districts'], Input::get('district'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		
	</div>
	<div class="row">
		@if ( !empty($search_data['modes']) )
			<div class="col-xs-12 col-sm-6 col-md-3 input-line">
				<div class="form-group error-container">
					{!! Form::select('mode', [''=>Lang::get('web/properties.mode')]+$search_data['modes'], Input::get('mode'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		@if ( !empty($search_data['types']) )
			<div class="col-xs-12 col-sm-6 col-md-3 input-line">
				<div class="form-group error-container">
					{!! Form::select('type', [''=>Lang::get('web/properties.type')]+$search_data['types'], Input::get('type'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		<div class="col-xs-12 col-sm-4 col-md-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="newly_build" value="1" {{ Input::get('newly_build') ? 'checked="checked"' : '' }} />
						{{ Lang::get('web/properties.labels.new') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-4 col-md-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="second_hand" value="1" {{ Input::get('second_hand') ? 'checked="checked"' : '' }} />
						{{ Lang::get('web/properties.labels.used') }}
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="new_item" value="1" {{ Input::get('new_item') ? 'checked="checked"' : '' }} />
						{{ Lang::get('account/properties.new.item') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="opportunity" value="1" {{ Input::get('opportunity') ? 'checked="checked"' : '' }} />
						{{ Lang::get('account/properties.opportunity') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="bank_owned" value="1" {{ Input::get('bank_owned') ? 'checked="checked"' : '' }} />
						{{ Lang::get('account/properties.bank_owned') }}
					</label>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3 input-line">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="private_owned" value="1" {{ Input::get('private_owned') ? 'checked="checked"' : '' }} />
						{{ Lang::get('account/properties.private_owned') }}
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		@if ( !empty($search_data['prices']) )
			<div class="col-xs-12 col-sm-3 col-md-2 input-line">
				<div class="form-group error-container">
					{!! Form::select(null, [''=>Lang::get('web/properties.more.price')], null, [ 'disabled'=>'disabled', 'class'=>'form-control has-placeholder mode-rel mode-rel-none '.( Input::get('mode','none') == 'none' ? '' : 'hide' ) ]) !!}
					{!! Form::select('price[rent]', [''=>Lang::get('web/properties.more.price')]+$search_data['prices']['rent'], Input::get('price.rent'), [ 'class'=>'form-control has-placeholder mode-rel mode-rel-rent '.( Input::get('mode') == 'rent' ? '' : 'hide' ) ]) !!}
					{!! Form::select('price[sale]', [''=>Lang::get('web/properties.more.price')]+$search_data['prices']['sale'], Input::get('price.sale'), [ 'class'=>'form-control has-placeholder mode-rel mode-rel-sale '.( Input::get('mode') == 'sale' ? '' : 'hide' ) ]) !!}
				</div>
			</div>
		@endif
		@if ( !empty($search_data['sizes']) )
			<div class="col-xs-12 col-sm-3 col-md-2 input-line">
				<div class="form-group error-container">
					{!! Form::hidden('size_unit', 'sqm') !!}
					{!! Form::select('size', [''=>Lang::get('web/properties.more.sqm')]+$search_data['sizes'], Input::get('size'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		@if ( !empty($search_data['rooms']) )
			<div class="col-xs-12 col-sm-3 col-md-2 input-line">
				<div class="form-group error-container">
					{!! Form::select('rooms', [''=>Lang::get('web/properties.more.rooms')]+$search_data['rooms'], Input::get('rooms'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
		@if ( !empty($search_data['baths']) )
			<div class="col-xs-12 col-sm-3 col-md-2 input-line">
				<div class="form-group error-container">
					{!! Form::select('baths', [''=>Lang::get('web/properties.more.baths')]+$search_data['baths'], Input::get('baths'), [ 'class'=>'form-control has-placeholder' ]) !!}
				</div>
			</div>
		@endif
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6 input-line">
			@if ( !empty($search_data['services']) )
				<div class="form-group error-container">
					{!! Form::label('services[]', Lang::get('web/properties.services')) !!}
					{!! Form::select('services[]', $search_data['services'], Input::get('services'), [ 'class'=>'form-control has-select-2', 'multiple'=>'multiple', 'size'=>'1' ]) !!}
				</div>
			@endif
		</div>
		<div class="col-xs-12 col-sm-6 input-line">
			<label class="hidden-xs">&nbsp;</label>
			<div class="text-right">
				{!! Form::submit(Lang::get('web/search.button'), [ 'class'=>'btn btn-submit btn-warning text-uppercase' ]) !!}
			</div>
		</div>
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

		form.on('change', 'select[name="mode"]', function(){
			form.find('.mode-rel').addClass('hide').filter('.mode-rel-' + ( $(this).val() || 'none' )).removeClass('hide');
		});

		form.on('change', 'select[name="state"]', function(){
			var state = $(this).val();
			var target = form.find('select[name="city"]');
			var target_html = '<option value="" class="is-placeholder">' + target.find('option[value=""]').eq(0).text() + '</option>';

			if ( !state ) {
			    target.html(target_html);
			    return;
			}

			if ( cities.hasOwnProperty(state) ) {
			    $.each(cities[state], function(k,v) {
			        target_html += '<option value="' + v.code + '">' + v.label + '</option>';
			    });
			    target.html(target_html);
			} else {
			    $.ajax({
			        dataType: 'json',
			        url: '{{ action('Ajax\GeographyController@getSuggest', 'city') }}',
			        data: {
			            state_slug: state,
			            site_id: {{ @intval($site_setup['site_id']) }}
			        },
			        success: function(data) {
			            if ( data ) {
			                cities[state] = data;
			                $.each(cities[state], function(k,v) {
			                    target_html += '<option value="' + v.code + '">' + v.label + '</option>';
			                });
			                target.html(target_html);
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
