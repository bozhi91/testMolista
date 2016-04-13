@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		{!! Form::model($site, [ 'method'=>'PATCH', 'action'=>[ 'Admin\SitesController@update', $site->id ], 'id'=>'site-form' ]) !!}

			<h1 class="list-title">{{ Lang::get('admin/sites.edit.title') }}</h1>

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab-site-config" aria-controls="tab-site-config" role="tab" data-toggle="tab">{{ Lang::get('admin/sites.tab.config') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main active" id="tab-site-config">
					<div class="row">
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
						<div class="col-xs-12 col-sm-6">
							{!! Form::label('domain', Lang::get('admin/sites.domain')) !!}
							@if ( count($site->domains) < 1 )
								<div class="form-group error-container">
									{!! Form::text('domains_array[new]', null, [ 'class'=>'form-control url domain-input', 'data-id'=>'' ]) !!}
								</div>
							@else
								@foreach ($site->domains->sortByDesc('default') as $domain)
									<div class="form-group error-container">
										{!! Form::text("domains_array[{$domain->id}]", null, [ 'class'=>'form-control url domain-input', 'data-id'=>$domain->id ]) !!}
									</div>
								@endforeach
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								{!! Form::label('locales_array[]', Lang::get('admin/sites.languages')) !!}
								<?php
									$tmp = [];
									foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def) 
									{
										$tmp[$lang_iso] = $lang_def['native'];
									}
								?>
								<div class="error-container">
									{!! Form::select('locales_array[]', $tmp, null, [ 'class'=>'form-control required has-select-2', 'size'=>'1', 'multiple'=>'multiple' ]) !!}
								</div>
								<div class="help-block">{{ Lang::get('admin/sites.languages.english') }}</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('owners_ids[]', Lang::get('admin/sites.owners')) !!}
								{!! Form::select('owners_ids[]', $companies, null, [ 'class'=>'form-control required has-select-2', 'size'=>'1', 'multiple'=>'multiple' ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								<div class="checkbox">
									<label>
										{!! Form::checkbox('enabled', 1, null, [ 'class'=>'' ]) !!}
										{{ Lang::get('admin/sites.enabled') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							@if ( count($site->owners_ids) > 0 && Auth::user()->can('user-login') )
								<a href="{{action('Admin\SitesController@show', $site->id)}}" class="btn btn-sm btn-default" target="_blank">
									<span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>
									{{ Lang::get('admin/sites.goto.admin') }}
								</a>
							@endif
							<a href="{{$site->main_url}}" class="btn btn-sm btn-default" target="_blank">
								<span class="glyphicon glyphicon-link" aria-hidden="true"></span>
								{{ Lang::get('admin/sites.goto.site') }}
							</a>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane tab-main" id="tab-site-managers">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('owners_ids[]', Lang::get('admin/sites.owners')) !!}
								{!! Form::select('owners_ids[]', $companies, null, [ 'class'=>'form-control required has-select-2', 'multiple'=>'multiple' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('employees_ids[]', Lang::get('admin/sites.employees')) !!}
								{!! Form::select('employees_ids[]', $employees, null, [ 'class'=>'form-control has-select-2', 'multiple'=>'multiple' ]) !!}
							</div>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane tab-main" id="tab-site-seo">
					<ul class="nav nav-tabs locale-tabs" role="tablist">
						@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
							<li role="presentation"><a href="#tab-site-texts-{{$lang_iso}}" aria-controls="tab-site-texts-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_def['native']}}</a></li>
						@endforeach
					</ul>
					<div class="tab-content">
						@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
							<div role="tabpanel" class="tab-pane tab-locale" id="tab-site-texts-{{$lang_iso}}">
								<div class="row">
									<div class="col-xs-12 col-sm-6">
										<div class="form-group error-container">
											<label>{{ Lang::get('admin/sites.title') }}</label>
											{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control title-input', 'data-locale'=>$lang_iso ]) !!}
										</div>
										<div class="form-group error-container">
											<label>{{ Lang::get('admin/sites.subtitle') }}</label>
											{!! Form::text("i18n[subtitle][{$lang_iso}]", null, [ 'class'=>'form-control subtitle-input', 'data-locale'=>$lang_iso ]) !!}
										</div>
									</div>
									<div class="col-xs-12 col-sm-6">
										<div class="form-group error-container">
											<label>{{ Lang::get('admin/sites.description') }}</label>
											{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control description-input', 'rows'=>'5', 'data-locale'=>$lang_iso ]) !!}
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

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#site-form');

			form.find('.locale-tabs a').eq(0).trigger('click');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				invalidHandler: function(e, validator){
					if ( validator.errorList.length ) {
						var el = $(validator.errorList[0].element);
						if ( el.closest('.tab-main').length ) {
							form.find('.main-tabs a[href="#' + el.closest(".tab-main").attr('id') + '"]').tab('show');
						}
					}
				},
				rules: {
					subdomain: {
						remote: {
							url: '{{action('Ajax\SiteController@getValidate','subdomain')}}',
							data: { id: {{$site->id}} }
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

			form.find('.domain-input').each(function(){
				var el = $(this);

				$(this).rules('add', {
					remote: {
						url: '{{action('Ajax\SiteController@getValidate','domain')}}',
						data: { 
							domain: function() { return el.val(); },
							id: {{$site->id}} 
						}
					},
					messages: {
						remote: "{{ print_js_string( Lang::get('admin/sites.domain.error.taken') ) }}"
					}
				});

			});

			form.find('.tab-main.active').find('.has-select-2').removeClass('has-select-2').select2();

			form.find('.main-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				$( e.target.getAttribute('href') ).find('.has-select-2').removeClass('has-select-2').select2();
			});

		});
	</script>

@endsection