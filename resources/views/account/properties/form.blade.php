{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}

	<div class="custom-tabs">

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.general') }}</a></li>
			<li role="presentation"><a href="#tab-location" aria-controls="tab-location" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.location') }}</a></li>
			<li role="presentation"><a href="#tab-text" aria-controls="tab-text" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.text') }}</a></li>
			<li role="presentation"><a href="#tab-images" aria-controls="tab-images" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.images') }}</a></li>
			@if ( $item )
				<li role="presentation"><a href="#tab-employees" aria-controls="tab-employees" role="tab" data-toggle="tab">{{ Lang::get('account/properties.tab.employees') }}</a></li>
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
							{!! Form::hidden('currency', 'EUR') !!}
							{!! Form::label('price', Lang::get('account/properties.price')) !!}
							<div class="input-group">
								{!! Form::text('price', null, [ 'class'=>'form-control required number', 'min'=>'0' ]) !!}
								<div class="input-group-addon">€</div>
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
						<div class="checkbox error-container">
							<label>
								{!! Form::checkbox('highlighted', 1, null) !!}
								{{ Lang::get('account/properties.highlighted') }}
							</label>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="checkbox error-container">
							<label>
								{!! Form::checkbox('newly_build', 1, null) !!}
								{{ Lang::get('account/properties.newly_build') }}
							</label>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="checkbox error-container">
							<label>
								{!! Form::checkbox('second_hand', 1, null) !!}
								{{ Lang::get('account/properties.second_hand') }}
							</label>
						</div>
					</div>
				</div>
				<hr />
				{!! Form::label(null, Lang::get('account/properties.services')) !!}
				<div class="row">
					@foreach ($services as $service)
						<div class="col-xs-12 col-sm-3">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('services[]', $service->id, empty($property) ? null : $property->hasService($service->id) ) !!}
									{{ $service->title }}
								</label>
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
							{!! Form::text('district', null, [ 'class'=>'form-control' ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('address', Lang::get('account/properties.address')) !!}
							{!! Form::textarea('address', null, [ 'class'=>'form-control required address-input', 'rows'=>'3' ]) !!}
						</div>
						<div class="row hide">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('zipcode', Lang::get('account/properties.zipcode')) !!}
									{!! Form::text('zipcode', null, [ 'class'=>'form-control' ]) !!}
								</div>
							</div>
						</div>
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
						<li role="presentation"><a href="#lang-{{$lang_iso}}" aria-controls="lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a></li>
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
											{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control required title-input', 'lang'=>$lang_iso ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".title-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
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
											{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control description-input', 'lang'=>$lang_iso, 'rows'=>'4' ]) !!}
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
						@if ( empty($property) || count($property->images) < 1 )
							<div class="alert alert-info">
								{{ Lang::get('account/properties.images.empty') }}
							</div>
						@else
							<ul class="image-gallery sortable-image-gallery">
								@foreach ($property->images->sortBy('position') as $image)
									<li class="handler">
										<a href="{{ asset("sites/{$property->site_id}/properties/{$property->id}/{$image->image}") }}" target="_blank" class="thumb" style="background-image: url({{ asset("sites/{$property->site_id}/properties/{$property->id}/{$image->image}") }})"></a>
										<div class="options text-right">
											{!! Form::hidden('images[]', $image->id) !!}
											<a href="#" class="image-delete-trigger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
										</div>
									</li>
								@endforeach
							</ul>
						@endif
						<div class="visible-xs-block">
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="col-xs-12 col-sm-5">
						{!! Form::button('+', [ 'class'=>'btn btn-default btn-xs pull-right image-upload-trigger' ]) !!}
						<h4>{{ Lang::get('account/properties.images.upload') }}</h4>
						<hr>
						<ul class="list-unstyled image-upload-area"></ul>
						<div class="help-block">
							{!! Lang::get('account/properties.images.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
						</div>
					</div>
				</div>
			</div>

			@if ( $item )
				<div role="tabpanel" class="tab-pane tab-main" id="tab-employees">
					<div class="alert alert-info properties-empty {{ ( $employees->count() > 0 ) ? 'hide' : '' }}">{{ Lang::get('account/properties.employees.empty') }}</div>
					@if ( Auth::user()->can('property-edit') && Auth::user()->can('employee-edit'))
						<div class="text-right">
							<a href="#associate-modal" class="btn btn-default btn-sm associate-trigger">{{ Lang::get('account/properties.employees.associate') }}</a>
						</div>
					@endif
					<div class="properties-list {{ ( $employees->count() < 1 ) ? 'hide' : '' }}">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>{{ Lang::get('account/properties.employees.employee') }}</th>
									<th>{{ Lang::get('account/properties.employees.email') }}</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@if ( $employees->count() > 0 )
									@include('account.properties.form-employees', [ 'employees'=>$employees, 'property_id'=>$item->id ])
								@endif
							</tbody>
						</table>
					</div>
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

@if ( $item )
	<div id="associate-modal" class="mfp-white-popup mfp-hide" data-url="{{ action('Account\PropertiesController@getAssociate', $item->slug) }}">
		<h4 class="page-title">{{ Lang::get('account/properties.employees.associate') }}</h4>
		<div class="form-group">
			<select class="form-control employee-select">
				<option value="">&nbsp;</option>
			</select>
		</div>
		<div class="text-right">
			{!! Form::button( Lang::get('general.continue'), [ 'class'=>'btn btn-yellow btn-continue']) !!}
		</div>
	</div>
@endif

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

		var image_counter = 0;

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

			var zoom = 14;
			var lat = form.find('.input-lat').val();
			var lng = form.find('.input-lng').val();

			if ( !lat || !lng ) {
				zoom = 6;
				lat = '40.4636670';
				lng = '-3.7492200';
			}

			var mapLatLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

			property_map = new google.maps.Map(document.getElementById('property-map'), {
				zoom: zoom,
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

			$.each(['address','city','state','country'], function(k,v){
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
			alertify.confirm("{{ print_js_string( Lang::get('account/properties.images.delete') ) }}", function (e) {
				if (e) {
					el.closest('.handler').remove();
				}
			});
		});

		// Image input
		function addImage() {
			image_counter++;
			form.find('.image-upload-area').append( tmpl('image_upload_item_tmpl', { id : image_counter }) );
		}
		form.on('click', '.image-upload-trigger', addImage);
		addImage();

		// Dissociate employee
		form.on('click','.dissociate-trigger',function(e){
			var el = $(this);
			e.preventDefault();
			alertify.confirm("{{ print_js_string( Lang::get('account/properties.employees.dissociate.confirm') ) }}", function (e) {
				if (e) {
					LOADING.show();
					$.ajax({
						dataType: 'json',
						url: el.data().url,
						success: function(data) {
							LOADING.hide();
							if ( data.success ) {
								el.closest('.property-line').remove();
								if ( form.find('.properties-list .property-line').length < 1 ) {
									form.find('.properties-list').addClass('hide');
									form.find('.properties-empty').removeClass('hide');
								}
								alertify.success("{{ print_js_string( Lang::get('account/properties.employees.dissociated') ) }}");
							} else {
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
						},
						error: function() {
							LOADING.hide();
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					});
					}
			});
		});

		// Associate employee
		var associate_modal = $('#associate-modal');
		var associate_select = associate_modal.find('.employee-select');
		form.find('.associate-trigger').magnificPopup({
			type: 'inline',
			callbacks: {
				beforeOpen: function() {
					$.ajax({
						dataType: "json",
						url: associate_modal.data().url ,
						success: function(data) {
							if ( data.success && data.items.length > 0 ) {
								$.each(data.items, function(i, item) {
									associate_select.append('<option value="' + item.value + '">' + item.label + '</option>');
								});
							} else {
								associate_select.html('<option value="">{{ print_js_string( Lang::get('account/properties.employees.empty') ) }}</option>');
							}
						},
						error: function() {
							associate_select.html('<option value="">{{ print_js_string( Lang::get('account/properties.employees.empty') ) }}</option>');
						}
					});
				},
				close: function() {
					associate_select.html('<option value="">&nbsp;</option>');
				}
			}
		});
		associate_modal.on('click', '.btn-continue', function(e){
			e.preventDefault();
			var user_id = associate_modal.find('.employee-select').val();
			if ( !user_id ) {
				return false;
			}

			$.magnificPopup.close();
			LOADING.show();

			$.ajax({
				dataType: "json",
				url: associate_modal.data().url ,
				data: { id : user_id },
				success: function(data) {
					LOADING.hide();
					if ( data.success ) {
						form.find('.properties-list tbody').html( data.html );
						form.find('.properties-empty').addClass('hide');
						form.find('.properties-list').removeClass('hide');
						alertify.success("{{ print_js_string( Lang::get('account/properties.employees.associated') ) }}"); 
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

		// Translations
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
						}
					} else {
						alertify.error("{{ print_js_string( Lang::get('general.error.simple') ) }}");
					}
				},
				error: function() {
					LOADING.hide();
					alertify.error("{{ print_js_string( Lang::get('general.error.simple') ) }}");
				}
			});
		});

		// Hide country_id select if only one country
		if ( form.find('select[name="country_id"] option').length < 2 ) {
			form.find('select[name="country_id"] option').closest('.form-group').addClass('hide');
		}

	});
</script>