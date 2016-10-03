{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'marketplace-form', 'files'=>true ]) !!}

	<ul class="nav nav-tabs main-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.general') }}</a></li>
		<li role="presentation"><a href="#tab-country" aria-controls="tab-country" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.countries') }}</a></li>
		<li role="presentation"><a href="#tab-configuration" aria-controls="tab-configuration" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.configuration') }}</a></li>
		<li role="presentation"><a href="#tab-instructions" aria-controls="tab-instructions" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.instructions') }}</a></li>
	</ul>

	<div class="tab-content">

		<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('code', Lang::get('admin/marketplaces.code')) !!}
						@if ( $item )
							{!! Form::text('code', null, [ 'class'=>'form-control required alphanumeric', 'readonly'=>'readonly' ]) !!}
						@else
							{!! Form::text('code', null, [ 'class'=>'form-control required alphanumeric' ]) !!}
						@endif
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('name', Lang::get('admin/marketplaces.title')) !!}
						{!! Form::text('name',null, [ 'class'=>'form-control required' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('class_path', Lang::get('admin/marketplaces.class.path')) !!}
						{!! Form::text('class_path',null, [ 'class'=>'form-control required' ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group">
						<div class="error-container">
							@if ( @$item->logo )
								<a href="{{ asset("marketplaces/{$item->logo}") }}" target="_blank" class="pull-right">
									<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
								</a>
							@endif
							{!! Form::label('logo', 'Logo') !!}
							{!! Form::file('logo', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
						</div>
						<div class="help-block"><p>Size: 16x16 pixels</p>{!! Lang::get('general.image.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize') ]) !!}</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('url', Lang::get('admin/marketplaces.url')) !!}
						{!! Form::text('url',null, [ 'class'=>'form-control url' ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('requires_contact', Lang::get('admin/marketplaces.contact')) !!}
						{!! Form::select('requires_contact', [
							0 => Lang::get('general.no'),
							1 => Lang::get('general.yes'),
						], null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('enabled', Lang::get('admin/marketplaces.enabled')) !!}
						{!! Form::select('enabled', [
							1 => Lang::get('general.yes'),
							0 => Lang::get('general.no'),
						], null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane tab-main" id="tab-country">
			<div class="alert alert-danger country-input-error hide">
				{!! Lang::get('admin/marketplaces.country.error') !!}
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-2 col-md-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="" class="country-input-all-none country-input-all" />
							{{ Lang::get('general.select.all') }}
						</label>
					</div>
				</div>
				<div class="col-xs-12 col-sm-2 col-md-3">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="" class="country-input-all-none country-input-none" />
							{{ Lang::get('general.select.none') }}
						</label>
					</div>
				</div>
			</div>
			<hr />
			<div class="row country-inputs">
				@foreach ($countries as $country_id => $country_title)
					<div class="col-xs-12 col-sm-2 col-md-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="countries_ids[]" value="{{ $country_id }}" class="country-input" />
								{{ $country_title }}
							</label>
						</div>
					</div>
				@endforeach
			</div>
		</div>

		<div role="tabpanel" class="tab-pane tab-main" id="tab-configuration">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('configuration[thumbs_flag]', Lang::get('admin/marketplaces.thumbs.flag')) !!}
						{!! Form::text('configuration[thumb_flag]', null, [ 'class'=>'form-control alphanumeric' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('configuration[xml_owners]', Lang::get('admin/marketplaces.xml.owners')) !!}
						{!! Form::select('configuration[xml_owners]', [
							0 => Lang::get('general.no'),
							1 => Lang::get('general.yes'),
						], null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('upload_type', Lang::get('admin/marketplaces.upload_type')) !!}
						{!! Form::select('upload_type', [
							'url' => 'URL',
							'url_single' => 'Unified URL',
							'ftp' => 'FTP',
						], null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
			</div>

			@if ($item)
			<div class="upload-type upload-type-url_single">
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group error-container">
							{!! Form::label('url_single_url', 'URL') !!}
							{!! Form::text('url_single_url', action('Web\FeedsController@unifiedFeed', [$item->code, $item->integration_secret]), [ 'class'=>'form-control', 'readonly' => 'readonly' ]) !!}
						</div>
					</div>
				</div>
			</div>
			@endif

			<div class="upload-type upload-type-ftp">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][host]', Lang::get('admin/marketplaces.ftp.host')) !!}
							{!! Form::text('configuration[ftp][host]', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-6 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][port]', Lang::get('admin/marketplaces.ftp.port')) !!}
							{!! Form::text('configuration[ftp][port]', null, [ 'class'=>'form-control numeric' ]) !!}
						</div>
					</div>
					<div class="col-xs-6 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][timeout]', Lang::get('admin/marketplaces.ftp.timeout')) !!}
							{!! Form::text('configuration[ftp][timeout]', null, [ 'class'=>'form-control numeric' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][username]', Lang::get('admin/marketplaces.ftp.username')) !!}
							{!! Form::text('configuration[ftp][username]', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][password]', Lang::get('admin/marketplaces.ftp.password')) !!}
							<div class="input-group">
								{!! Form::text('configuration[ftp][password]', null, [ 'class'=>'form-control' ]) !!}
								<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password" style="cursor: pointer;" aria-hidden="true"></span></div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][root]', Lang::get('admin/marketplaces.ftp.root')) !!}
							{!! Form::text('configuration[ftp][root]', null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-6 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][passive]', Lang::get('admin/marketplaces.ftp.mode')) !!}
							{!! Form::select('configuration[ftp][passive]', [
								1 => 'Passive',
								0 => 'Active',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-6 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('configuration[ftp][ssl]', Lang::get('admin/marketplaces.ftp.ssl')) !!}
							{!! Form::select('configuration[ftp][ssl]', [
								0 => 'No',
								1 => 'Yes',
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane tab-main" id="tab-instructions">
			<ul class="nav nav-tabs locale-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tabs-lang-{{ fallback_lang() }}" aria-controls="tabs-lang-{{ fallback_lang() }}" role="tab" data-toggle="tab">{{ @$locales[fallback_lang()] }}</a></li>
				@foreach ($locales as $locale_key => $locale_name)
					@if ( $locale_key != fallback_lang() )
						<li role="presentation"><a href="#tabs-lang-{{ $locale_key }}" aria-controls="tabs-lang-{{ $locale_key }}" role="tab" data-toggle="tab">{{ $locale_name }}</a></li>
					@endif
				@endforeach
			</ul>
			<div class="tab-content">
				@foreach ($locales as $locale_key => $locale_name)
					<div role="tabpanel" class="tab-pane tab-locale {{ ( $locale_key == fallback_lang() ) ? 'active' : '' }}" id="tabs-lang-{{ $locale_key }}">
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group error-container">
									{!! Form::textarea("i18n[instructions][{$locale_key}]", null, [ 'class'=>'form-control', 'rows'=>10, 'dir'=>lang_dir($locale_key) ]) !!}
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>

	</div>

	<div class="text-right">
		{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
	</div>

{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#marketplace-form');
		var countries_ids = {!! @json_encode($item->countries_ids) !!};

		$.each(countries_ids, function(k,id){
			form.find('input.country-input[value="' +id + '"]').prop('checked', true);
		});

		form.find('[name="upload_type"]').change(function(){
			form.find('.upload-type').hide();
			form.find('.upload-type-'+this.value).show();
		}).change();

		form.validate({
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
				code: {
					remote: {
						url: '{{ action('Admin\MarketplacesController@getCheck', 'code') }}',
						type: 'get',
						data: {
							exclude: {{ empty($item) ? 0 : $item->id }}
						}
					}
				}
			},
			messages: {
				email: {
					remote: "{{ trim( Lang::get('admin/marketplaces.code.used') ) }}"
				}
			},
			submitHandler: function(f) {
				if ( form.find('.country-input:checked').length > 0 ) {
					LOADING.show();
					return f.submit();
				}

				form.find('.country-input-error').removeClass('hide');
				form.find('.main-tabs a[href="#tab-country"]').tab('show');

				$('html,body').animate({ scrollTop: $('#tab-country').offset().top },'fast');
			}
		});

		form.on('click', '.show-hide-password', function(e){
			e.preventDefault();
			form.find('input[name="configuration[ftp][password]"]').togglePassword();
		});
		form.find('input[name="configuration[ftp][password]"]').hidePassword();

		form.on('change', '.country-input', function(){
			if ( $(this).is(':checked') ) {
				form.find('.country-input-error').addClass('hide');
			}

			form.find('.country-input-all-none').prop('checked', false);

			if ( form.find('.country-input:checked').length == 0 ) {
				form.find('.country-input-none').prop('checked', true);
			} else if ( form.find('.country-input:unchecked').length == 0 ) {
				form.find('.country-input-all').prop('checked', true);
			}
		});

		form.on('change', '.country-input-all-none', function(){
			if ( $(this).is(':unchecked') ) {
				return true;
			}

			if ( $(this).hasClass('country-input-all') ) {
				form.find('.country-input').prop('checked', true);
				form.find('.country-input-none').prop('checked', false);
				form.find('.country-input-error').addClass('hide');
			} else if ( $(this).hasClass('country-input-none') ) {
				form.find('.country-input').prop('checked', false);
				form.find('.country-input-all').prop('checked', false);
			}

		});

	});
</script>
