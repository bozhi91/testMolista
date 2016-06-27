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
								<div class="help-block">{{ Lang::get('admin/sites.languages.english', [ 'fallback_locale'=>fallback_lang_text() ]) }}</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container owners-select-container">
								{!! Form::label('owners_ids[]', Lang::get('admin/sites.owners')) !!}
								@foreach ($owners as $owner_id=>$owner_name)
									<input type="hidden" name="owners_ids[]" value="{{$owner_id}}" data-title="{{$owner_name}}" />
								@endforeach
								{!! Form::select('owners_ids[]', $companies, null, [ 'class'=>'form-control has-select-2', 'size'=>'1', 'multiple'=>'multiple' ]) !!}
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
						<div class="col-xs-12 col-sm-6">
							<label>&nbsp;</label>
							<div class="text-right">
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

			form.find('.has-select-2').select2();

			var owners_str = '';
			form.find('input[name="owners_ids[]"]').each(function(){
				owners_str += '<li class="select2-selection__choice">' + $(this).data().title + '</li>';
			});
			form.find('select[name="owners_ids[]"]').on('change', function(){
				$(this).closest('.form-group').find('.select2-selection__rendered').prepend(owners_str);
			}).closest('.form-group').find('.select2-selection__rendered').prepend(owners_str);
		});
	</script>

@endsection