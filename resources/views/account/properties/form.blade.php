{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}
	{!! Form::hidden('current_tab', session('current_tab', '#tab-general')) !!}
	{!! Form::hidden('label_color', null) !!}

	<div class="custom-tabs">

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.general') }}</a></li>
			<li role="presentation"><a href="#tab-location" aria-controls="tab-location" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.location') }}</a></li>
			<li role="presentation"><a href="#tab-text" aria-controls="tab-text" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.text') }}</a></li>
			<li role="presentation"><a href="#tab-images" aria-controls="tab-images" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.images') }}</a></li>
			@if ( $item )
				<li role="presentation"><a href="#tab-employees" aria-controls="tab-employees" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.employees') }}</a></li>
			@else
				<li role="presentation"><a href="#tab-seller" aria-controls="tab-seller" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.seller') }}</a></li>
			@endif
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('ref', Lang::get('account/properties.ref')) !!}
							{!! Form::text('ref', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('type', Lang::get('account/properties.type')) !!}
							{!! Form::select('type', [ ''=>'' ] + $types, null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('mode', Lang::get('account/properties.mode')) !!}
							{!! Form::select('mode', [ ''=>'' ] + $modes, null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::hidden('currency', empty($property->currency) ? 'EUR' : $property->currency) !!}
							{!! Form::label('price', Lang::get('account/properties.price')) !!}
							<div class="input-group">
								{!! Form::text('price', null, [ 'class'=>'form-control required number', 'min'=>'0' ]) !!}
								<div class="input-group-addon">{{ price_symbol(empty($property->currency) ? 'EUR' : $property->currency) }}</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::hidden('size_unit', 'sqm') !!}
							{!! Form::label('size', Lang::get('account/properties.size')) !!}
							<div class="input-group">
								{!! Form::text('size', null, [ 'class'=>'form-control required number', 'min'=>'0' ]) !!}
								<div class="input-group-addon">m²</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('rooms', Lang::get('account/properties.rooms')) !!}
							{!! Form::text('rooms', null, [ 'class'=>'form-control required digits', 'min'=>'0' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('baths', Lang::get('account/properties.baths')) !!}
							{!! Form::text('baths', null, [ 'class'=>'form-control required digits', 'min'=>'0' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
							<div class="error-container">
								{!! Form::label('ec', Lang::get('account/properties.energy.certificate')) !!}
								{!! Form::select('ec', [''=>'']+$energy_types, null, [ 'class'=>'form-control' ]) !!}
							</div>
							<div class="help-block">
								<label>
									{!! Form::checkbox('ec_pending', 1, null) !!}
									{{ Lang::get('account/properties.energy.certificate.pending') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('enabled', Lang::get('account/properties.enabled')) !!}
							{!! Form::select('enabled', [ '1'=>Lang::get('general.yes'), '0'=>Lang::get('general.no') ], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
				<hr />
				{!! Form::label(null, Lang::get('account/properties.characteristics')) !!}
				<div class="row">
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('highlighted', 1, null) !!}
									{{ Lang::get('account/properties.highlighted') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('new_item', 1, null) !!}
									{{ Lang::get('account/properties.new.item') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('opportunity', 1, null) !!}
									{{ Lang::get('account/properties.opportunity') }}
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('newly_build', 1, null) !!}
									{{ Lang::get('account/properties.newly_build') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('second_hand', 1, null) !!}
									{{ Lang::get('account/properties.second_hand') }}
								</label>
							</div>
						</div>
					</div>
				</div>
				<hr />
				{!! Form::label(null, Lang::get('account/properties.services')) !!}
				<div class="row">
					@foreach ($services as $service)
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('services[]', $service->id, empty($property) ? null : $property->hasService($service->id) ) !!}
										{{ $service->title }}
									</label>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-location">
				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('country_id', Lang::get('account/properties.country')) !!}
							{!! Form::select('country_id', $countries->toArray(), @$country_id, [ 'class'=>'form-control required country-input', 'data-rel'=>'.state-input, .city-input', 'data-target'=>'.state-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'state') ]) !!}
						</div>
						<div class="form-group error-container">
							<?php $tmp = empty($states) ? [ ''=>'' ] : [ ''=>'' ] + $states->toArray(); ?>
							{!! Form::label('state_id', Lang::get('account/properties.state')) !!}
							{!! Form::select('state_id', $tmp, null, [ 'class'=>'form-control required state-input', 'data-rel'=>'.city-input', 'data-target'=>'.city-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'city') ]) !!}
						</div>
						<div class="form-group error-container">
							<?php $tmp = empty($cities) ? [ ''=>'' ] : [ ''=>'' ] + $cities->toArray(); ?>
							{!! Form::label('city_id', Lang::get('account/properties.city')) !!}
							{!! Form::select('city_id', $tmp, null, [ 'class'=>'form-control required city-input' ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('district', Lang::get('account/properties.district')) !!}
							{!! Form::text('district', null, [ 'class'=>'form-control district-input' ]) !!}
						</div>
						@include('account/properties/form-address')
					</div>
					<div class="col-xs-12 col-sm-8">
						<div id="property-map" style="height: 400px; margin-bottom: 10px;"></div>
						<div class="row">
							<div class="col-xs-12 col-sm-4">
								<div class="form-group error-container">
									{!! Form::label('lat', Lang::get('account/properties.lat'), [ 'class'=>'normal' ]) !!}
									{!! Form::text('lat', null, [ 'class'=>'form-control required number input-lat', 'readonly'=>'readonly' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<div class="form-group error-container">
									{!! Form::label('lng', Lang::get('account/properties.lng'), [ 'class'=>'normal' ]) !!}
									{!! Form::text('lng', null, [ 'class'=>'form-control required number input-lng', 'readonly'=>'readonly' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label>&nbsp;</label>
								<div>
									{!! Form::button(Lang::get('account/properties.geolocate'), [ 'class'=>'btn btn-block btn-default geolocate-trigger' ]) !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-text">
				<ul class="nav nav-tabs locale-tabs" role="tablist">
					@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
						<li role="presentation" class="autotranslate-flag-area">
							<a href="#lang-{{$lang_iso}}" aria-controls="lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a>
							<i class="fa fa-check autotranslate-flag" aria-hidden="true"></i>
						</li>
					@endforeach
				</ul>
				<div class="tab-content translate-area">
					@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
						<div role="tabpanel" class="tab-pane tab-locale" id="lang-{{$lang_iso}}">
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('account/properties.title')) !!}
										<div class="error-container">
											{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control title-input '.(($lang_iso == fallback_lang()) ? 'required' : ''), 'lang'=>$lang_iso ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".title-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
									<div class="form-group">
										<div class="pull-right">
											{!! Form::text(null, null, [ 'class'=>'label-color-input' ]) !!}
										</div>
										{!! Form::label("i18n[label][{$lang_iso}]", Lang::get('account/properties.label')) !!}
										<div class="error-container">
											{!! Form::text("i18n[label][{$lang_iso}]", null, [ 'class'=>'form-control label-input', 'lang'=>$lang_iso ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".label-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
									<div class="autotranslate-credit">
										<a href="http://aka.ms/MicrosoftTranslatorAttribution" target="_blank">{{ Lang::get('general.autotranslate.credits') }} <img src="{{ asset('images/autotranslate/microsoft.png') }}" alt="Microsoft" class="credited"></a>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										{!! Form::label("i18n[description][{$lang_iso}]", Lang::get('account/properties.description')) !!}
										<div class="error-container">
											{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control resize-vertical description-input', 'lang'=>$lang_iso, 'rows'=>'4' ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".description-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-images">
				<div class="row">
					<div class="col-xs-12 col-sm-7">
						<h4>{{ Lang::get('account/properties.images.gallery') }}</h4>
						<hr>
						<div class="alert alert-info images-empty">
							{{ Lang::get('account/properties.images.empty') }}
						</div>
						<ul class="image-gallery sortable-image-gallery">
							@if ( !empty($property) && count($property->images) > 0 )
								@foreach ($property->images->sortBy('position') as $image)
									<li class="handler">
										<a href="{{ asset("sites/{$property->site_id}/properties/{$property->id}/{$image->image}") }}" target="_blank" class="thumb" style="background-image: url({{ asset("sites/{$property->site_id}/properties/{$property->id}/{$image->image}") }})"></a>
										<div class="options text-right">
											{!! Form::hidden('images[]', $image->id) !!}
											<a href="#" class="image-delete-trigger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
										</div>
									</li>
								@endforeach
							@endif
						</ul>
						<div class="visible-xs-block">
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="col-xs-12 col-sm-5">
						<h4>{{ Lang::get('account/properties.images.upload') }}</h4>
						<hr>
						<div class="dropzone-previews" id="dropzone-previews"></div>
						<div class="help-block">{{ Lang::get('account/properties.images.dropzone.helper') }}</div>
					</div>
				</div>
			</div>

			@if ( $item )
				<div role="tabpanel" class="tab-pane tab-main" id="tab-employees">
					@include('account.properties.tab-managers', [
						'item' => $item,
						'employees' => $item->users()->withRole('employee')->get(),
					])
				</div>
			@else
				<div role="tabpanel" class="tab-pane tab-main" id="tab-seller">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('employee_id', Lang::get('account/properties.show.property.catch.employee') ) !!}
								{!! Form::select('employee_id', [''=>'&nbsp;']+$managers, Auth::user()->id, [ 'class'=>'has-select-2 form-control required', ]) !!}
							</div>
						</div>
					</div>
					<hr />
					@include('account.properties.catch-form', [ 
						'item' => null,
						'price_symbol' => '€',
					])
				</div>
			@endif

		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

		<br />

	</div>

{!! Form::close() !!}

<script type="text/html" id="image_upload_item_tmpl">
	<li>
		<input class="" name="new_images[<%=id%>]" type="file" accept="image/*" />
		<hr>
	</li>
</script>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#edit-form');

		var property_map;
		var property_marker;
		var property_geocoder;
		var property_zoom = 14;

		// Enable first language tab
		form.find('.locale-tabs a').eq(0).trigger('click');

		// Form validation
		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			invalidHandler: function(e, validator){
				if ( validator.errorList.length ) {
					var el = $(validator.errorList[0].element);
					form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
					if ( el.closest('.tab-locale').length ) {
						form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
					}
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		// Enable map when opening tab
		form.find('.main-tabs a[href="#tab-location"]').on('shown.bs.tab', function (e) {
			var el = $(e.target);

			if ( el.hasClass('property-map-initialized') ) {
				return;
			}

			property_geocoder = new google.maps.Geocoder();

			el.addClass('property-map-initialized');

			var lat = form.find('.input-lat').val();
			var lng = form.find('.input-lng').val();

			if ( !lat || !lng ) {
				property_zoom = 6;
				lat = '40.4636670';
				lng = '-3.7492200';
			}

			var mapLatLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

			property_map = new google.maps.Map(document.getElementById('property-map'), {
				zoom: property_zoom,
				center: mapLatLng
			});

			property_marker = new google.maps.Marker({
				position: mapLatLng,
				map: property_map,
				draggable: true
			});

			property_marker.addListener('dragend',function(event) {
				form.find('.input-lat').val( event.latLng.lat() );
				form.find('.input-lng').val( event.latLng.lng() );
			});
		});

		// Geolocation
		form.on('click', '.geolocate-trigger', function(e){
			var address = [];
			var error = false;

			if ( form.find('.address-input').val() ) {
				address.push( form.find('.address-input').val() );
			}

			if ( form.find('.district-input').val() ) {
				address.push( form.find('.district-input').val() );
			}

			$.each(['city','state','country'], function(k,v){
				var input = form.find('.'+v+'-input');
				if ( input.valid() ) {
					if ( input.prop('tagName').toLowerCase() == 'select' ) {
						address.push( input.find('option:selected').text() );
					} else {
						address.push( input.val() );
					}
				} else {
					error = true;
				}
			});

			if ( error ) {
				alertify.error("{{ print_js_string( Lang::get('account/properties.geolocate.missing') ) }}"); 
				return false;
			}

			LOADING.show();

			property_geocoder.geocode({
				'address': address.join(', ')
			}, function(results, status) {
				LOADING.hide();
				if (status === google.maps.GeocoderStatus.OK) {
					property_map.setCenter( results[0].geometry.location );
					property_marker.setPosition( results[0].geometry.location );
					form.find('.input-lat').val( results[0].geometry.location.lat() );
					form.find('.input-lng').val( results[0].geometry.location.lng() );
				} else {
					alertify.error("{{ print_js_string( Lang::get('account/properties.geolocate.error') ) }}"+status);
				}
			});
		});

		// Location selectors
		form.on('change', '.country-input, .state-input', function(){
			var el = $(this);

			form.find( el.data().rel ).html('<option value=""></option>');

			if ( !el.val() ) {
				return;
			}

			LOADING.show();

			var data = {};
			data[el.attr('name')] = el.val();

			$.ajax({
				dataType: 'json',
				url: el.data().action,
				data: data,
				success: function(data) {
					if ( data ) {
						var target = form.find( el.data().target );
						$.each(data, function(k,v) {
							target.append('<option value="' + v.id + '">' + v.label + '</option>');
						});
					}
					LOADING.hide();
				},
				error: function() {
					LOADING.hide();
				}
			});
		});

		// Automatic location
		form.on('change', '.city-input', function(){
			if ( $(this).val() ) {
				property_map.setZoom(13);
				form.find('.geolocate-trigger').trigger('click');
			}
		});

		// Image gallery
		form.find('.image-gallery').sortable();
		form.find('.image-gallery .thumb').each(function(){
			$(this).magnificPopup({
				type: 'image',
				closeOnContentClick: false,
				mainClass: 'mfp-img-mobile',
				image: {
					verticalFit: true
				}
			});
		});
		form.on('click', '.image-delete-trigger', function(e){
			var el = $(this);
			e.preventDefault();
			SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.images.delete') ) }}", function (e) {
				if (e) {
					el.closest('.handler').remove();
					if ( form.find('.image-gallery .thumb').length < 1 ) {
						form.find('.images-empty').show();
					}
				}
			});
		});
		if ( form.find('.image-gallery .thumb').length > 0 ) {
			form.find('.images-empty').hide();
		}

		// Translations
		var translation_flag_fields = '.title-input, .description-input';

		function checkTranslations() {
			form.find('.autotranslate-flag-area').each(function(){
				var el = $(this).addClass('autotranslate-complete');
				var target = $( $(this).find('a').attr('href') );

				var completed = true;

				target.find(translation_flag_fields).each(function() {
					if ( ! $(this).val() ) {
						el.removeClass('autotranslate-complete')
						return false;
					}
				});

			});
		}

		form.on('change', translation_flag_fields, checkTranslations);

		checkTranslations();

		form.on('click', '.translate-trigger', function(e){
			e.preventDefault();

			var el = $(this);
			var group = el.closest('.translate-area');
			var items = group.find( el.data().input );
			var from = $(this).data().lang;
			var text = items.filter('[lang="'+from+'"]').val();
			var languages = {!! json_encode(LaravelLocalization::getSupportedLocales()) !!};

			// No text to translate from
			if (!text) {
				alertify.error("{{ print_js_string( Lang::get('general.autotranslate.error.text') ) }}"); 
				return false;
			}

			// Show loader
			LOADING.show();

			// Get translation languages
			var to = [];
			items.each(function(){
				to.push( $(this).attr('lang') );
			});

			// Get translations
			$.ajax({
				url: '{{ action('Ajax\AutotranslateController@getIndex') }}',
				dataType: 'json',
				data: {
					text: text,
					from: from,
					to: to
				},
				success: function(data) {
					LOADING.hide();
					if (data.success) {
						var errors = [];
						$.each(data.translations, function(iso,txt){
							if (txt) {
								items.filter('[lang="'+iso+'"]').val(txt);
							} else {
								errors.push(languages[iso].native);
							}
						});
						// Error (no translations, except from language)
						if ( errors.length+1 == to.length ) {
							alertify.error("{{ print_js_string( Lang::get('general.autotranslate.error.all') ) }}");
						// Success
						} else {
							var msg = "{{ print_js_string( Lang::get('general.autotranslate.success') ) }}";
							// Some errors
							if ( errors.length > 0 ) {
								msg += "<br />{{ print_js_string( Lang::get('general.autotranslate.error.some') ) }} " + errors.join(', ') + '.';
							}
							alertify.success(msg);
							checkTranslations();
						}
					} else {
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				},
				error: function() {
					LOADING.hide();
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}
			});
		});

		// Hide country_id select if only one country
		if ( form.find('select[name="country_id"] option').length < 2 ) {
			form.find('select[name="country_id"] option').closest('.form-group').addClass('hide');
		}

		<?php
			$i = 0;
			$label_default = false;
			$label_palette = "[ [";
			foreach (Theme::config('label-palette') as $color) 
			{
				if ( !$label_default )
				{
					$label_default = $color;
				}

				if ( !$i )
				{
					$label_palette .= " '{$color}'";
				}
				elseif ( $i%5 == 0 )
				{
					$label_palette .= " ], [ '{$color}'";
				}
				else
				{
					$label_palette .= ", '{$color}'";
				}

				$i++;
			}
			$label_palette .= " ] ]";
		?>
		// Label color picker
		form.find('.label-color-input').each(function(){
			var el = $(this);
			var target = form.find('input[name="label_color"]');

			if ( !target.val() ) {
				target.val('{!! $label_default !!}');
			}

			el.spectrum({
				showPaletteOnly: true,
				showPalette: true,
				color: target.val(),
				palette: {!! $label_palette !!},
				move: function(color) {
				    el.spectrum('toggle');
					target.val( color.toHexString() );
					form.find('.label-color-input').spectrum("set", color);
				}
			});

		});

		// Drop zone
        Dropzone.autoDiscover = false;
		$("#dropzone-previews").addClass('dropzone').dropzone({ 
			url: '{{ action('Account\PropertiesController@postUpload') }}',
			params: {
				_token: '{{ Session::getToken() }}'
			},
			maxFilesize: {{ Config::get('app.property_image_maxsize') / 1024 }},
			acceptedFiles: 'image/*',
			dictFileTooBig: "{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize') ]) ) }}",
			dictDefaultMessage: "{{ print_js_string( Lang::get('account/properties.images.dropzone.helper') ) }}",
			error: function(file, response) {
				if ( $.type(response) === 'string') {
					if ( response.length > 500 ) {
						alertify.error("{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize') ]) ) }}");
					} else {
						alertify.error(response);
					}
				} else if ( $.type(response) === 'object' && response.message ) {
					alertify.error(response.message);
				} else {
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}

				$(file.previewElement).fadeOut(function(){ 
					$(this).remove() 
				});
			},
			canceled: function(file) {
				$(file.previewElement).fadeOut(function(){ 
					$(this).remove() 
				});
			},
			success: function(file,response) {
				var item = $('<li class="handler ui-sortable-handle" />');

				var img = '/' + response.directory + '/' + response.filename;
				item.append('<a href="'+img+'" target="_blank" class="thumb" style="background-image: url('+img+')"></a>');
				item.find('.thumb').magnificPopup({
					type: 'image',
					closeOnContentClick: false,
					mainClass: 'mfp-img-mobile',
					image: {
						verticalFit: true
					}
				});

				item.append('<div class="options text-right" />');
				item.find('.options').append('<input name="images[]" type="hidden" value="new_' + img + '" />');
				item.find('.options').append('<a href="#" class="image-delete-trigger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>');

				form.find('.image-gallery').append(item);

				$(file.previewElement).fadeOut(function(){ 
					$(this).remove() 
				});

				form.find('.images-empty').hide();
			}
		});

		var tabs = form.find('.main-tabs');
		var current_tab = form.find('input[name="current_tab"]').val();
		tabs.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			form.find('input[name="current_tab"]').val( $(this).attr('href') );
			form.find('.has-select-2').select2();
		});
		tabs.find('a[href="' + current_tab + '"]').tab('show');

		form.find('.has-select-2').select2();

	});
</script>