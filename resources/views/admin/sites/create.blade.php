@extends('layouts.admin')

@section('content')

<div class="container">

	@include('common.messages', [ 'dismissible'=>true ])

	{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Admin\SitesController@store' ], 'id'=>'site-form' ]) !!}

		<h1>{{ Lang::get('admin/sites.create.title') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-site-config" aria-controls="tab-site-config" role="tab" data-toggle="tab">{{ Lang::get('admin/sites.tab.config') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-site-config">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('title', Lang::get('admin/sites.title')) !!}
							{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('subdomain', Lang::get('admin/sites.subdomain')) !!}
							<div class="input-group">
								<div class="input-group-addon">{{env('APP_PROTOCOL','http')}}://</div>
								{!! Form::text('subdomain', null, [ 'class'=>'form-control required alphanumericHypen' ]) !!}
								<div class="input-group-addon">.{{env('APP_DOMAIN','molista.com')}}</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
							{!! Form::label('locales[]', Lang::get('admin/sites.languages')) !!}
							<?php
								$tmp = [];
								foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def) 
								{
									$tmp[$lang_iso] = $lang_def['native'];
								}
							?>
							<div class="error-container">
								{!! Form::select('locales[]', $tmp, array_keys($tmp), [ 'class'=>'form-control required has-select-2', 'size'=>1, 'multiple'=>'multiple' ]) !!}
							</div>
							<div class="help-block">{{ Lang::get('admin/sites.languages.english') }}</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('owners[]', Lang::get('admin/sites.owners')) !!}
							{!! Form::select('owners[]', $companies, null, [ 'class'=>'form-control required has-select-2', 'size'=>1, 'multiple'=>'multiple' ]) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('custom_theme', Lang::get('admin/sites.theme.custom')) !!}
						<?php
							$themes = [];
							foreach (Config::get('themes.themes') as $theme => $def) 
							{
								if ( empty($def['custom']) ) 
								{
									continue;
								}
								$themes[$theme] = empty($def['title']) ? ucfirst($theme) : $def['title'];
							}
						?>
						{!! Form::select('custom_theme', [ ''=>'' ]+$themes, null, [ 'class'=>'form-control' ]) !!}
					</div>
				</div>
			</div>

		</div>

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
		</div>

	{!! Form::close() !!}

</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#site-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			invalidHandler: function(e, validator){
				if ( validator.errorList.length ) {
					var el = $(validator.errorList[0].element);
					if ( el.closest('.tab-locale').length ) {
						form.find('.locale-tabs a[href="#' + el.closest(".tab-pane").attr('id') + '"]').tab('show');
					}
				}
			},
			rules: {
				subdomain: {
					remote: {
						url: '{{action('Ajax\SiteController@getValidate','subdomain')}}',
					}
				}
			},
			messages: {
				subdomain: {
					remote: "{{ print_js_string( Lang::get('admin/sites.subdomain.error.taken') ) }}",
					alphanumericHypen: "{{ print_js_string( Lang::get('validation.alphanumericHypen') ) }}"
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		form.find('.tab-main.active').find('.has-select-2').removeClass('has-select-2').select2();

		form.find('.main-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			$( e.target.getAttribute('href') ).find('.has-select-2').removeClass('has-select-2').select2();
		});

	});
</script>

@endsection