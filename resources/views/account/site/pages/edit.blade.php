@extends('layouts.account')

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
							</div>
							<p>&nbsp</p>
						@elseif ( $page->type == 'map' )
							<div class="row">
								<div class="col-xs-12 col-xs-4">
									<div class="form-group error-container">
										{!! Form::label("configuration[map][lat]", Lang::get('account/site.pages.configuration.map.lat')) !!}
										{!! Form::text("configuration[map][lat]", null, [ 'class'=>'form-control required number' ]) !!}
									</div>
								</div>
								<div class="col-xs-12 col-xs-4">
									<div class="form-group error-container">
										{!! Form::label("configuration[map][lng]", Lang::get('account/site.pages.configuration.map.lng')) !!}
										{!! Form::text("configuration[map][lng]", null, [ 'class'=>'form-control required number' ]) !!}
									</div>
								</div>
								<div class="col-xs-12 col-xs-4">
									<div class="form-group error-container">
										{!! Form::label("configuration[map][zoom]", Lang::get('account/site.pages.configuration.map.zoom')) !!}
										{!! Form::selectRange("configuration[map][zoom]", 0, 21, null, [ 'class'=>'form-control required number' ]) !!}
									</div>
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
												{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control '.(($lang_iso == 'en') ? 'required' : '') ]) !!}
											</div>
										</div>
										@if ( $page->enabled )
											<div class="col-xs-12 col-sm-6 hidden-xs">
												<label>&nbsp;</label>
												<div class="text-right">
													@if ( empty($page->i18n['slug'][$lang_iso]) )
														<a href="{{ action('Web\PagesController@show', $page->i18n['slug'][$lang_iso]) }}" class="btn btn-warning" target="_blank">{{ Lang::get('general.view') }}</a>
													@else
														<a href="{{ action('Web\PagesController@show', $page->i18n['slug']['en']) }}" class="btn btn-warning" target="_blank">{{ Lang::get('general.view') }}</a>
													@endif
												</div>
											</div>
										@endif
									</div>

									<div class="row">
										<div class="col-xs-12">
											<div class="form-group error-container">
												{!! Form::label("i18n[body][{$lang_iso}]", Lang::get('account/site.pages.body')) !!}
												{!! Form::textarea("i18n[body][{$lang_iso}]", null, [ 'class'=>'is-wysiwyg form-control' ]) !!}
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
													{!! Form::text("i18n[seo_title][{$lang_iso}]", null, [ 'class'=>'form-control' ]) !!}
												</div>
											</div>
											<div class="form-group">
												{!! Form::label("i18n[seo_keywords][{$lang_iso}]", Lang::get('account/site.pages.seo_keywords')) !!}
												<div class="error-container">
													{!! Form::text("i18n[seo_keywords][{$lang_iso}]", null, [ 'class'=>'form-control' ]) !!}
												</div>
											</div>
										</div>
										<div class="col-xs-12 col-sm-6">
											<div class="form-group">
												{!! Form::label("i18n[seo_description][{$lang_iso}]", Lang::get('account/site.pages.seo_description')) !!}
												<div class="error-container">
													{!! Form::textarea("i18n[seo_description][{$lang_iso}]", null, [ 'class'=>'form-control', 'rows'=>'5' ]) !!}
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
					{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
				</div>

				<br />

			</div>

		{!! Form::close() !!}


	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#edit-form');

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

		});
	</script>

@endsection