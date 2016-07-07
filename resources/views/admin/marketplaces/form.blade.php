{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'marketplace-form', 'files'=>true ]) !!}

	<ul class="nav nav-tabs main-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.general') }}</a></li>
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
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('class_path', Lang::get('admin/marketplaces.class.path')) !!}
						{!! Form::text('class_path',null, [ 'class'=>'form-control required' ]) !!}
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
						{!! Form::label('country_id', Lang::get('admin/marketplaces.country')) !!}
						{!! Form::select('country_id', [ ''=>'' ]+$countries, null, [ 'class'=>'form-control' ]) !!}
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
						{!! Form::label('enabled', Lang::get('admin/marketplaces.enabled')) !!}
						{!! Form::select('enabled', [
							1 => Lang::get('general.yes'),
							0 => Lang::get('general.no'),
						], null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
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
									{!! Form::textarea("i18n[instructions][{$locale_key}]", null, [ 'class'=>'form-control', 'rows'=>10 ]) !!}
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
			}
		});

	});
</script>
