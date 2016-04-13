@extends('layouts.account')

@section('account_content')

 	@include('common.messages', [ 'dismissible'=>true ])

	<h1 class="page-title">{{ Lang::get('account/site.configuration.h1') }}</h1>

	{!! Form::model(@$site, [ 'method'=>'POST', 'action'=>'Account\Site\ConfigurationController@postIndex', 'files'=>true, 'id'=>'admin-site-configuration-form' ]) !!}

		<div class="custom-tabs">

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab-site-config" aria-controls="tab-site-config" role="tab" data-toggle="tab">{{ Lang::get('account/site.configuration.tab.config') }}</a></li>
				<li role="presentation"><a href="#tab-site-texts" aria-controls="tab-site-texts" role="tab" data-toggle="tab">{{ Lang::get('account/site.configuration.tab.texts') }}</a></li>
				<li role="presentation"><a href="#tab-site-social" aria-controls="tab-site-social" role="tab" data-toggle="tab">{{ Lang::get('account/site.configuration.tab.social') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main active" id="tab-site-config">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('subdomain', Lang::get('account/site.configuration.subdomain')) !!}
								<div class="input-group">
									<div class="input-group-addon">{{ Config::get('app.application_protocol') }}://</div>
									{!! Form::text('subdomain', null, [ 'class'=>'form-control required alphanumericHypen' ]) !!}
									<div class="input-group-addon">.{{ Config::get('app.application_domain') }}</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							{!! Form::label('domains_array', Lang::get('account/site.configuration.domains')) !!}
							@if ( count($site->domains) < 1 )
								<div class="form-group error-container">
									{!! Form::text('domains_array[new]', null, [ 'class'=>'form-control url domain-input', 'data-id'=>'' ]) !!}
								</div>
							@else
								@foreach ($site->domains as $domain)
									<div class="form-group error-container">
										{!! Form::text("domains_array[{$domain->id}]", null, [ 'class'=>'form-control url domain-input', 'data-id'=>$domain->id ]) !!}
									</div>
								@endforeach
							@endif
						</div>
					</div>

					<hr />

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								<label>{{ Lang::get('account/site.configuration.theme') }}</label>
								<?php
									$themes = [];
									foreach (Config::get('themes.themes') as $theme => $def) 
									{
										if ( empty($def['public']) ) 
										{
											continue;
										}
										$themes[$theme] = empty($def['title']) ? ucfirst($theme) : $def['title'];
									}
								?>
								{!! Form::select('theme', [ ''=>'' ]+$themes, null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								@if ( @$site->logo )
									<a href="{{ asset("sites/{$site->id}/{$site->logo}") }}" target="_blank"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></a>
									<label>{{ Lang::get('account/site.configuration.logo') }}</label>
									{!! Form::file('logo', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
								@else
									<label>{{ Lang::get('account/site.configuration.logo') }}</label>
									{!! Form::file('logo', [ 'class'=>'form-control required', 'accept'=>'image/*' ]) !!}
								@endif
							</div>
							<div class="help-block">
								{!! Lang::get('account/site.configuration.logo.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								<label>{{ Lang::get('account/site.configuration.favicon') }}</label>
								{!! Form::file('favicon', [ 'class'=>'form-control', 'accept'=>'image/x-icon' ]) !!}
							</div>
							<div class="help-block">
								{!! Lang::get('account/site.configuration.favicon.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
							</div>
						</div>
					</div>

					<hr />

					<div class="form-group error-container">
						<label>{{ Lang::get('account/site.configuration.languages') }}</label>
						<div class="row">
							<div class="col-xs-12 col-sm-2">
								<div class="checkbox">
									<label class="normal">
										{!! Form::checkbox('locales_array[]', 'en', null, [ 'class'=>'required locale-input', 'readonly'=>'readonly', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
										English
									</label>
								</div>
							</div>
							@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
								@if ( $lang_iso != 'en' )
									<div class="col-xs-12 col-sm-2">
										<div class="checkbox">
											<label class="normal">
												{!! Form::checkbox('locales_array[]', $lang_iso, null, [ 'class'=>'required locale-input', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
												{{ $lang_def['native'] }}
											</label>
										</div>
									</div>
								@endif
							@endforeach
						</div>
					</div>

				</div>

				<div role="tabpanel" class="tab-pane tab-main" id="tab-site-texts">
					<ul class="nav nav-tabs locale-tabs" role="tablist">
						@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
							<li role="presentation"><a href="#tab-site-texts-{{$lang_iso}}" aria-controls="tab-site-texts-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_def['native']}}</a></li>
						@endforeach
					</ul>
					<div class="tab-content translate-area">
						@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
							<div role="tabpanel" class="tab-pane tab-locale" id="tab-site-texts-{{$lang_iso}}">
								<div class="row">
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('account/site.configuration.title')) !!}
											<div class="error-container">
												{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control title-input', 'data-locale'=>$lang_iso, 'lang'=>$lang_iso ]) !!}
											</div>
											<div class="help-block text-right">
												<a href="#" class="translate-trigger" data-input=".title-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
											</div>
										</div>
										<div class="form-group">
											{!! Form::label("i18n[subtitle][{$lang_iso}]", Lang::get('account/site.configuration.subtitle')) !!}
											<div class="error-container">
												{!! Form::text("i18n[subtitle][{$lang_iso}]", null, [ 'class'=>'form-control subtitle-input', 'data-locale'=>$lang_iso, 'lang'=>$lang_iso ]) !!}
											</div>
											<div class="help-block text-right">
												<a href="#" class="translate-trigger" data-input=".subtitle-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
											</div>
										</div>
									</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group">
											{!! Form::label("i18n[description][{$lang_iso}]", Lang::get('account/site.configuration.description')) !!}
											<div class="error-container">
												{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control description-input', 'data-locale'=>$lang_iso, 'lang'=>$lang_iso, 'rows'=>'4' ]) !!}
											</div>
											<div class="help-block text-right">
												<a href="#" class="translate-trigger" data-input=".description-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
											</div>
										</div>
										<div class="autotranslate-credit">
											<a href="http://aka.ms/MicrosoftTranslatorAttribution" target="_blank">{{ Lang::get('general.autotranslate.credits') }} <img src="{{ asset('images/autotranslate/microsoft.png') }}" alt="Microsoft" class="credited"></a>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				</div>

				<div role="tabpanel" class="tab-pane tab-main" id="tab-site-social">
					<div class="form-horizontal">
						<div class="form-group">
							{!! Form::label('social_array[facebook]', 'Facebook', [ 'class'=>'col-sm-2' ]) !!}
							<div class="col-sm-10">
								<div class="error-container">
									{!! Form::text('social_array[facebook]', null, [ 'class'=>'form-control url' ]) !!}
								</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('social_array[twitter]', 'Twitter', [ 'class'=>'col-sm-2' ]) !!}
							<div class="col-sm-10">
								<div class="error-container">
									{!! Form::text('social_array[twitter]', null, [ 'class'=>'form-control url' ]) !!}
								</div>
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('social_array[instagram]', 'Instagram', [ 'class'=>'col-sm-2' ]) !!}
							<div class="col-sm-10">
								<div class="error-container">
									{!! Form::text('social_array[instagram]', null, [ 'class'=>'form-control url' ]) !!}
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-yellow']) !!}
		</div>

	{!! Form::close() !!}

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#admin-site-configuration-form');

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
				rules: {
					"subdomain" : {
						remote: {
							url: '{{ action('Account\Site\ConfigurationController@getCheck', 'subdomain') }}',
							type: 'get',
							data: {
								id: '{{@$site->id}}',
								subdomain: function() { return form.find('input[name="subdomain"]').val() }
							}
						},
					}
				},
				messages: {
					"subdomain" : {
						alphanumericHypen: "{{ print_js_string( Lang::get('account/site.configuration.subdomain.alpha') ) }}",
						remote: "{{ print_js_string( Lang::get('account/site.configuration.subdomain.error') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			// Enable first language tab
			form.find('.locale-tabs a').eq(0).trigger('click');

			// Make titles required
			form.on('change', '.locale-input', function(){
				if ( $(this).attr('readonly') == 'readonly') {
					$(this).prop('checked', true);
				}

				form.find('.title-input').removeClass('required');
				form.find('.locale-input:checked').each(function(){
					form.find('.title-input[data-locale="' + $(this).val() + '"]').addClass('required');
				});
			});
			form.find('.locale-input').eq(0).trigger('change');

			// Domains
			form.find('.domain-input').each(function(){
				var el = $(this);
				$(this).rules('add', {
					remote: {
						url: '{{ action('Account\Site\ConfigurationController@getCheck', 'domain') }}',
						type: 'get',
						data: {
							id: function() { return el.data().id },
							domain: function() { return el.val() }
						}
					},
					messages: {
						remote: "{{ print_js_string( Lang::get('account/site.configuration.domains.error') ) }}"
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
		});
	</script>

@endsection