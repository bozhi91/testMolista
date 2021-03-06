@extends('layouts.account', [
	'use_google_maps' => true,
])

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.pages.edit.title') }} ({{ Lang::get("account/site.pages.type.{$page->type}") }})</h1>

		{!! Form::model($page, [ 'method'=>'PATCH', 'action'=>[ 'Account\Site\PagesController@update', $page->slug ], 'files'=>true, 'id'=>'edit-form' ]) !!}

			<div class="custom-tabs">

				<ul class="nav nav-tabs main-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/site.pages.tab.general') }}</a></li>
					<li role="presentation"><a href="#tab-seo" aria-controls="tab-seo" role="tab" data-toggle="tab">{{ Lang::get('account/site.pages.tab.seo') }}</a></li>
				</ul>

				<div class="tab-content">

					<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">

						@if ( $page->type == 'contact' )
							<div class="row">
								<div class="col-xs-12 col-xs-6">
									<div class="form-group">
										<div class="error-container">
											{!! Form::label("configuration[contact][email]", Lang::get('account/site.pages.configuration.contact.email')) !!}
											{!! Form::text("configuration[contact][email]", null, [ 'class'=>'form-control required email' ]) !!}
										</div>
										<div class="help-block">{{ Lang::get('account/site.pages.configuration.contact.email.helper') }}</div>
									</div>
								</div>
								<div class="col-xs-12 col-xs-6">
									<div class="form-group">
										<div class="error-container">
											{!! Form::label("configuration[contact][phone_required]", Lang::get('account/site.pages.configuration.contact.phone.required')) !!}
											{!! Form::select('configuration[contact][phone_required]', [
												0 => Lang::get('general.no'),
												1 => Lang::get('general.yes'),
											], null, [ 'class'=>'form-control' ]) !!}
										</div>
									</div>
								</div>
							</div>
							<p>&nbsp</p>
						@elseif ( $page->type == 'map' )
							<div class="row">
								<div class="col-xs-12 col-sm-4 col-md-3">
									<div class="form-group error-container">
										{!! Form::label("configuration[map][lat]", Lang::get('account/site.pages.configuration.map.lat')) !!}
										{!! Form::text("configuration[map][lat]", null, [ 'class'=>'input-lat form-control required number' ]) !!}
									</div>
									<div class="form-group error-container">
										{!! Form::label("configuration[map][lng]", Lang::get('account/site.pages.configuration.map.lng')) !!}
										{!! Form::text("configuration[map][lng]", null, [ 'class'=>'input-lng form-control required number' ]) !!}
									</div>
									<div class="form-group error-container">
										{!! Form::label("configuration[map][zoom]", Lang::get('account/site.pages.configuration.map.zoom')) !!}
										{!! Form::selectRange("configuration[map][zoom]", 0, 21, null, [ 'class'=>'input-zoom form-control required number' ]) !!}
									</div>
									<div class="form-group error-container">
										<a href="#map-address-form" class="btn btn-md btn-block btn-default map-address-trigger">{{ Lang::get('account/site.pages.configuration.map.button') }}</a>
									</div>
								</div>
								<div class="col-xs-12 col-sm-8 col-md-9">
									<div id="gmap" style="height: 300px;"></div>
								</div>
							</div>
							<p>&nbsp</p>
						@endif

						<ul class="nav nav-tabs locale-tabs" role="tablist">
							@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
								<li role="presentation"><a href="#tab-general-lang-{{$lang_iso}}" aria-controls="tab-general-lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a></li>
							@endforeach
						</ul>
						<div class="tab-content translate-area">
							@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
								<div role="tabpanel" class="tab-pane tab-locale" id="tab-general-lang-{{$lang_iso}}">
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="form-group error-container">
												{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('account/site.pages.title')) !!}
												{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control '.(($lang_iso == fallback_lang()) ? 'required' : ''), 'dir'=>lang_dir($lang_iso) ]) !!}
											</div>
										</div>
										@if ( $page->enabled )
											<div class="col-xs-12 col-sm-6 hidden-xs">
												<label>&nbsp;</label>
												<div class="text-right">
													@if ( !empty($page->i18n['slug'][$lang_iso]) )
														<a href="{{ LaravelLocalization::getLocalizedURL($lang_iso, action('Web\PagesController@show', $page->i18n['slug'][$lang_iso])) }}" class="btn btn-warning" target="_blank">{{ Lang::get('general.view') }}</a>
													@elseif ( !empty($page->i18n['slug'][fallback_lang()]) )
														<a href="{{ LaravelLocalization::getLocalizedURL($lang_iso, action('Web\PagesController@show', $page->i18n['slug'][fallback_lang()])) }}" class="btn btn-warning" target="_blank">{{ Lang::get('general.view') }}</a>
													@endif
												</div>
											</div>
										@endif
									</div>

									<div class="row">
										<div class="col-xs-12">
											<div class="form-group error-container">
												{!! Form::label("i18n[body][{$lang_iso}]", Lang::get('account/site.pages.body')) !!}
												{!! Form::textarea("i18n[body][{$lang_iso}]", null, [ 'class'=>'is-wysiwyg form-control', 'dir'=>lang_dir($lang_iso) ]) !!}
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>

					<div role="tabpanel" class="tab-pane tab-main" id="tab-seo">
						<ul class="nav nav-tabs locale-tabs" role="tablist">
							@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
								<li role="presentation"><a href="#tab-site-texts-{{$lang_iso}}" aria-controls="tab-site-texts-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a></li>
							@endforeach
						</ul>
						<div class="tab-content translate-area">
							@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
								<div role="tabpanel" class="tab-pane tab-locale" id="tab-site-texts-{{$lang_iso}}">
									<div class="row">
										<div class="col-xs-12 col-sm-6">
											<div class="form-group">
												{!! Form::label("i18n[seo_title][{$lang_iso}]", Lang::get('account/site.pages.seo_title')) !!}
												<div class="error-container">
													{!! Form::text("i18n[seo_title][{$lang_iso}]", null, [ 'class'=>'form-control', 'dir'=>lang_dir($lang_iso) ]) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label("i18n[seo_keywords][{$lang_iso}]", Lang::get('account/site.pages.seo_keywords')) !!}
												<div class="error-container">
													{!! Form::text("i18n[seo_keywords][{$lang_iso}]", null, [ 'class'=>'form-control', 'dir'=>lang_dir($lang_iso) ]) !!}
												</div>
											</div>
										</div>
										<div class="col-xs-12 col-sm-6">
											<div class="form-group">
												{!! Form::label("i18n[seo_description][{$lang_iso}]", Lang::get('account/site.pages.seo_description')) !!}
												<div class="error-container">
													{!! Form::textarea("i18n[seo_description][{$lang_iso}]", null, [ 'class'=>'form-control', 'rows'=>'5', 'dir'=>lang_dir($lang_iso) ]) !!}
												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>

				</div>

				<br />

				<div class="text-right">
					{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
					{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
				</div>

				<br />

			</div>

		{!! Form::close() !!}

		{!! Form::open([ 'id'=>'map-address-form', 'class'=>'mfp-hide mfp-white-popup' ]) !!}
			<div class="form-group">
				{!! Form::label('address', Lang::get('account/site.pages.configuration.map.address')) !!}
				<div class="error-container">
					{!! Form::text('address', null, [ 'id'=>'address-input', 'class'=>'form-control required' ]) !!}
				</div>
				<div class="help-block">{{ Lang::get('account/site.pages.configuration.map.address.helper') }}</div>
			</div>
			<div class="text-right">
				{!! Form::button( Lang::get('account/site.pages.configuration.map.geolocate'), [ 'type'=>'submit', 'class'=>'btn btn-sm btn-primary']) !!}
			</div>
		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#edit-form');
			var geocoder = new google.maps.Geocoder();

			// Enable first language tab
			form.find('.locale-tabs').each(function(){
				$(this).find('a').eq(0).trigger('click');
			});

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

			form.find('.is-wysiwyg').each(function(){
				var el = $(this);

				$(this).summernote({
					height: 450,
					lang: '{{ summetime_lang() }}',
					callbacks: {
						onChange: function(content) {
							el.val( content );
						}
					}
				});
			});

			var gmap = $('#gmap');
			if ( gmap.length )
			{
				var mapLatLng = {
					lat: parseFloat( form.find('.input-lat').val() || {{ config('app.lat_default') }} ),
					lng: parseFloat( form.find('.input-lng').val() || {{ config('app.lng_default') }} )
				};

				var map = new google.maps.Map(document.getElementById('gmap'), {
					zoom: parseInt( form.find('.input-zoom').val() ),
					center: mapLatLng
				});

				var marker = new google.maps.Marker({
					position: mapLatLng,
					map: map,
					draggable: true
				});

				marker.addListener('dragend',function(event) {
					form.find('.input-lat').val( event.latLng.lat() );
					form.find('.input-lng').val( event.latLng.lng() );
				});

				form.on('change', '.input-zoom', function(){
					map.setZoom( parseInt( $(this).val() ) );
				});

				form.find('.map-address-trigger').magnificPopup({
					type: 'inline',
					preloader: false,
					focus: '#address-input',
					callbacks: {
						beforeOpen: function() {
							$('#address-input').val('');
						}
					}
				});
			}

			$('#map-address-form').validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					if ( gmap.length ) {
						LOADING.show();
						geocoder.geocode({
							'address': $('#address-input').val()
						}, function(results, status) {
							LOADING.hide();
							$.magnificPopup.close();
							if (status === google.maps.GeocoderStatus.OK) {
								map.setCenter( results[0].geometry.location );
								marker.setPosition( results[0].geometry.location );
								form.find('.input-lat').val( results[0].geometry.location.lat() );
								form.find('.input-lng').val( results[0].geometry.location.lng() );
							} else {
								var message = "{{ print_js_string( Lang::get('account/properties.geolocate.error') ) }}: "+status;
								switch (status) {
									case 'ZERO_RESULTS':
										message = "{{ print_js_string( Lang::get('account/properties.geolocate.no_results') ) }}";
										break;
								}
								alertify.error(message);
							}
						});
					} else {
						return false;
					}
				}
			});

		});
	</script>

@endsection
