@extends('layouts.account')

@section('account_content')

	<div class="site-configuration">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.configuration.h1') }}</h1>

		{!! Form::model(@$site, [ 'method'=>'POST', 'action'=>'Account\Site\ConfigurationController@postIndex', 'files'=>true, 'id'=>'admin-site-configuration-form' ]) !!}
			{!! Form::hidden('current_tab', $current_tab) !!}

			<div class="custom-tabs">

				<ul class="nav nav-tabs main-tabs" role="tablist">
					<li role="presentation" class="{{ $current_tab == 'config' ? 'active' : '' }}"><a href="#tab-site-config" aria-controls="tab-site-config" role="tab" data-toggle="tab" data-tab="config">{{ Lang::get('account/site.configuration.tab.config') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'theme' ? 'active' : '' }}"><a href="#tab-site-theme" aria-controls="tab-site-theme" role="tab" data-toggle="tab" data-tab="theme">{{ Lang::get('account/site.configuration.tab.theme') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'signature' ? 'active' : '' }}"><a href="#tab-site-signature" aria-controls="tab-site-signature" role="tab" data-toggle="tab" data-tab="signature">{{ Lang::get('account/site.configuration.tab.signature') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'mail' ? 'active' : '' }}"><a href="#tab-site-mail" aria-controls="tab-site-mail" role="tab" data-toggle="tab" data-tab="mail">{{ Lang::get('account/site.configuration.tab.mail') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'texts' ? 'active' : '' }}"><a href="#tab-site-texts" aria-controls="tab-site-texts" role="tab" data-toggle="tab" data-tab="texts">{{ Lang::get('account/site.configuration.tab.texts') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'social' ? 'active' : '' }}"><a href="#tab-site-social" aria-controls="tab-site-social" role="tab" data-toggle="tab" data-tab="social">{{ Lang::get('account/site.configuration.tab.social') }}</a></li>
					<li role="presentation" class="{{ $current_tab == 'alerts' ? 'active' : '' }}"><a href="#tab-site-alerts" aria-controls="tab-site-alerts" role="tab" data-toggle="tab" data-tab="alerts">{{ Lang::get('account/site.configuration.tab.alerts') }}</a></li>
				</ul>

				<div class="tab-content">

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'config' ? 'active' : '' }}" id="tab-site-config">
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('site_currency', Lang::get('account/site.configuration.currency')) !!}
									{!! Form::select('site_currency', $currencies, null, [ 'class'=>'currency-select form-control required' ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<div class="error-container">
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
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<div class="error-container">
										<label>{{ Lang::get('account/site.configuration.favicon') }}</label>
										{!! Form::file('favicon', [ 'class'=>'form-control', 'accept'=> 'image/x-icon,image/vnd.microsoft.icon' ]) !!}
									</div>
									<div class="help-block">
										{!! Lang::get('account/site.configuration.favicon.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('timezone', Lang::get('account/site.configuration.timezone')) !!}
									{!! Form::select('timezone', [ ''=>'' ]+$timezones, null, [ 'class'=>'form-control required' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('customer_register', Lang::get('account/site.configuration.client.register')) !!}
									{!! Form::select('customer_register', [
										'1' => Lang::get('general.yes'),
										'0' => Lang::get('general.no'),
									], null, [ 'class'=>'form-control' ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<div class="error-container">
										{!! Form::label('ga_account', Lang::get('account/site.configuration.ga.account')) !!}
										{!! Form::text('ga_account', null, [ 'class'=>'form-control' ]) !!}
									</div>
									<div class="help-block">{{ Lang::get('account/site.configuration.ga.account.helper') }}</div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									{!! Form::label('hide_molista', Lang::get('account/site.configuration.hide.molista', [ 'webname'=>env('WHITELABEL_WEBNAME','Molista') ])) !!}
									<div class="error-container">
										@if ( $current_site->can_hide_molista )
											{!! Form::select('hide_molista', [
												'1' => Lang::get('general.yes'),
												'0' => Lang::get('general.no'),
											], null, [ 'class'=>'form-control' ]) !!}
										@elseif ( $current_site->hide_molista )
											{!! Form::select('hide_molista', [
												'1' => Lang::get('general.yes'),
											], null, [ 'class'=>'form-control' ]) !!}
										@else
											{!! Form::select('hide_molista', [
												'0' => Lang::get('general.no'),
											], null, [ 'class'=>'form-control' ]) !!}
										@endif
									</div>
									@if ( !$current_site->can_hide_molista )
										<div class="help-block">{{ Lang::get('account/site.configuration.hide.molista.helper') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="{{ ( $max_languages == 1 ) ? 'hide' : '' }}"
							<hr />
							<div class="error-container">
								<label>{{ Lang::get('account/site.configuration.languages') }}</label>
								<div class="row">
									<div class="col-xs-12 col-sm-2">
										<div class="form-group">
											<div class="checkbox">
												<label class="normal">
													{!! Form::checkbox('locales_array[]', fallback_lang(), null, [ 'class'=>'required locale-input', 'readonly'=>'readonly', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
													{{ fallback_lang_text() }}
												</label>
											</div>
										</div>
									</div>
									@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
										@if ( $lang_iso != fallback_lang() )
											<div class="col-xs-12 col-sm-2">
												<div class="form-group">
													<div class="checkbox">
														<label class="normal">
															{!! Form::checkbox('locales_array[]', $lang_iso, null, [ 'class'=>'required locale-input', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
															{{ $lang_def['native'] }}
														</label>
													</div>
												</div>
											</div>
										@endif
									@endforeach
								</div>
							</div>
						</div>

					</div>

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'theme' ? 'active' : '' }}" id="tab-site-theme">
						@include('account/site/configuration.tab-theme')
					</div>

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'signature' ? 'active' : '' }}" id="tab-site-signature">
						@include('account/site/configuration.tab-signature')
					</div>

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'mail' ? 'active' : '' }}" id="tab-site-mail">
						@include('account/site/configuration.tab-emails')
					</div>

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'texts' ? 'active' : '' }}" id="tab-site-texts">
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
												{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('account/site.configuration.title')) !!}
												<div class="error-container">
													{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control title-input'.((fallback_lang() == $lang_iso) ? ' required' : ''), 'data-locale'=>$lang_iso, 'lang'=>$lang_iso, 'dir'=>lang_dir($lang_iso) ]) !!}
												</div>
												<div class="help-block text-right">
													<a href="#" class="translate-trigger" data-input=".title-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
												</div>
											</div>
											<div class="form-group">
												{!! Form::label("i18n[subtitle][{$lang_iso}]", Lang::get('account/site.configuration.subtitle')) !!}
												<div class="error-container">
													{!! Form::text("i18n[subtitle][{$lang_iso}]", null, [ 'class'=>'form-control subtitle-input', 'data-locale'=>$lang_iso, 'lang'=>$lang_iso, 'dir'=>lang_dir($lang_iso) ]) !!}
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
													{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control description-input', 'data-locale'=>$lang_iso, 'lang'=>$lang_iso, 'rows'=>'4', 'dir'=>lang_dir($lang_iso) ]) !!}
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

					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'social' ? 'active' : '' }}" id="tab-site-social">
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
					
					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'alerts' ? 'active' : '' }}" id="tab-site-alerts">
						<label>{{  Lang::get('account/site.configuration.alerts.price.down') }}</label>
						<div class="row">
							<div class="col-xs-12 col-sm-3">
								<div class="form-group">
									<div class="checkbox">
										<label class="normal">
											<?php $val = $site->alert_config === null ? 1 : $site->alert_config['bajada']['agentes'] ?>
											{!! Form::hidden('alerts[bajada][agentes]', 0) !!}
											{!! Form::checkbox('alerts[bajada][agentes]', 1, $val) !!}
											{{ Lang::get('account/site.configuration.alerts.agents') }}
										</label>
									</div>
								</div>
							</div>
							<div class="col-xs-12 col-sm-3">
								<div class="form-group">
									<div class="checkbox">
										<label class="normal">
											<?php $val = $site->alert_config === null ? 1 : $site->alert_config['bajada']['customers'] ?>
											{!! Form::hidden('alerts[bajada][customers]', 0) !!}
											{!! Form::checkbox('alerts[bajada][customers]', 1, $val) !!}
											{{ Lang::get('account/site.configuration.alerts.customers') }}
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>

			<br />

			<div class="text-right">
				{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#admin-site-configuration-form');

			$.validator.addMethod("ga_account_validation", function(value, element) {
				return this.optional(element) || /(UA|YT|MO)-\d+-\d+/i.test(value);
			}, "{{ print_js_string( Lang::get('account/site.configuration.ga.account.error') ) }}");


			// Form validation
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					if ( element.attr('name') == 'theme' ) {
						element.closest('.error-container').prepend( error.addClass('alert alert-danger').css({ display: 'block' }) );
					} else {
						element.closest('.error-container').append(error);
					}
				},
				invalidHandler: function(e, validator){
					if ( validator.errorList.length ) {
						var el = $(validator.errorList[0].element);
						form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
						if ( el.closest('.tab-locale').length ) {
							form.find('.locale-tabs a[href="#' + el.closest(".tab-locale").attr('id') + '"]').tab('show');
						}
						if ( el.closest('.mailer-tab-pane').length ) {
							form.find('.mailer-tabs a[href="#' + el.closest('.mailer-tab-pane').attr('id') + '"]').tab('show');
						}
					}
				},
				rules: {
					'ga_account': {
						ga_account_validation: true
					},
					"subdomain" : {
						remote: {
							url: '{{ action('Account\Site\ConfigurationController@getCheck', 'subdomain') }}',
							type: 'get',
							data: {
								id: '{{@$site->id}}',
								subdomain: function() { return form.find('input[name="subdomain"]').val() }
							}
						},
					},
					'mailer[out][protocol]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[out][login]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[out][pass]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[out][host]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[out][port]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[in][protocol]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[in][login]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[in][pass]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[in][host]': {
						required: function(element) {
							return $('#mailer-service-input').val() == 'custom';
						}
					},
					'mailer[in][port]': {
						required: function(element) {
							if ( $('#mailer-service-input').val() == 'custom' ) {
								switch ( form.find('select[name="mailer[in][protocol]"]').val() ) {
									case 'pop3':
									case 'imap':
										return true;
								}
							}
							return false;
						}
					}
				},
				messages: {
					'ga_account': {
						ga_account_validation: "{{ print_js_string( Lang::get('account/site.configuration.ga.account.error') ) }}"
					},
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
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					},
					error: function() {
						LOADING.hide();
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				});
			});

			form.find('.mailer-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				$('#mailer-service-input').val( $(e.target).data().service );
			});

			// Test email configuration
			form.find('.mail-group-area').on('change','input, select', function(){
				$(this).closest('.mail-group-area').data('changed',1);
			});
			form.on('click', '.btn-test-email', function(e){
				e.preventDefault();

				var cont = $(this).closest('.mail-group-area');

				if ( cont.data().changed ) {
					alertify.error("{{ print_js_string( Lang::get('account/site.configuration.mailing.test.changed') ) }}");
					return false;
				}

				LOADING.show();
				$.ajax({
					url: '{{ action('Account\Site\ConfigurationController@postTestMailerConfiguration') }}',
					type: 'POST',
					dataType: 'json',
					data: {
						_token: '{{ Session::getToken() }}',
						protocol: cont.find('.input-protocol').val()
					},
					success: function(data) {
						LOADING.hide();
						if (data.success) {
							alertify.success("{{ print_js_string( Lang::get('account/site.configuration.mailing.test.success') ) }}");
						} else {
							alertify.error("{{ print_js_string( Lang::get('account/site.configuration.mailing.test.error') ) }}");
						}
					},
					error: function() {
						LOADING.hide();
						alertify.error("{{ print_js_string( Lang::get('account/site.configuration.mailing.test.error') ) }}");
					}
				});
			});

			var currency_select = form.find('.currency-select');
			form.on('change', '.currency-select', function() {
				var el = $(this);

				SITECOMMON.confirm("{{ print_js_string( Lang::get('account/site.configuration.currency.warning') ) }}", function (e) {
					if (e) {
						currency_select.data('current', currency_select.val());
					} else {
						currency_select.val( currency_select.data().current );
					}
				});
			});
			currency_select.data('current', currency_select.val());


			form.find('.main-tabs').find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				form.find('input[name="current_tab"]').val( $(this).data().tab );
			});
		});
	</script>

@endsection
