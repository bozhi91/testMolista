@extends('layouts.admin')

@section('content')

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])


		<h1 class="list-title">{{ Lang::get('admin/sites.edit.title') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="{{ $current_tab == 'site' ? 'active' : '' }}"><a href="#tab-site-config" aria-controls="tab-site-config" role="tab" data-toggle="tab">{{ Lang::get('admin/sites.tab.config') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'plan' ? 'active' : '' }}"><a href="#tab-site-plan" aria-controls="tab-site-plan" role="tab" data-toggle="tab">{{ Lang::get('admin/sites.tab.plan') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'invoices' ? 'active' : '' }}"><a href="#tab-site-invoices" aria-controls="tab-site-invoices" role="tab" data-toggle="tab">{{ Lang::get('admin/sites.tab.invoices') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'site' ? 'active' : '' }}" id="tab-site-config">
				{!! Form::model($site, [ 'method'=>'PATCH', 'action'=>[ 'Admin\SitesController@update', $site->id ], 'id'=>'site-form' ]) !!}
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
					<div class="text-right">
						{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-default hide' ]) !!}
					</div>
				{!! Form::close() !!}

				@if ( @$plan_details )
					<hr />
					<h3>Invoicing info</h3>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, 'Type') !!}
								{!! Form::text(null, Lang::get("corporate/signup.invoicing.type.{$plan_details->invoicing['type']}"), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						@if ( $plan_details->invoicing['type'] == 'company' )
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label(null, Lang::get('corporate/signup.invoicing.company')) !!}
									{!! Form::text(null, @$plan_details->invoicing['company'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
								</div>
							</div>
						@endif
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.first_name')) !!}
								{!! Form::text(null, @$plan_details->invoicing['first_name'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.last_name')) !!}
								{!! Form::text(null, @$plan_details->invoicing['last_name'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.email')) !!}
								{!! Form::text(null, @$plan_details->invoicing['email'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.tax_id')) !!}
								{!! Form::text(null, @$plan_details->invoicing['tax_id'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.street')) !!}
								{!! Form::text(null, @$plan_details->invoicing['street'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.zipcode')) !!}
								{!! Form::text(null, @$plan_details->invoicing['zipcode'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.city')) !!}
								{!! Form::text(null, @$plan_details->invoicing['city'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label(null, Lang::get('corporate/signup.invoicing.country')) !!}
								{!! Form::text(null, @$plan_details->invoicing['country'], [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							</div>
						</div>
					</div>
				@endif
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'plan' ? 'active' : '' }}" id="tab-site-plan">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/expirations.plan')) !!}
							{!! Form::text(null, $site->plan->name, [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/expirations.payment.method')) !!}
							@if ( $site->payment_interval )
								{!! Form::text(null, Lang::get("web/plans.price.{$site->payment_interval}"), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@else
								{!! Form::text(null, null, [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/expirations.payment.interval')) !!}
							@if ( $site->payment_method )
								{!! Form::text(null, Lang::get("account/payment.method.{$site->payment_method}"), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@else
								{!! Form::text(null, null, [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@endif
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/expirations.paid.until')) !!}
							@if ( $site->paid_until )
								{!! Form::text(null, date("d/m/Y", strtotime($site->paid_until)), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@else
								{!! Form::text(null, null, [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('admin/sites.transfer')) !!}
							@if ( $site->web_transfer_requested )
								{!! Form::text(null, Lang::get('general.yes'), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@else
								{!! Form::text(null, Lang::get('general.no'), [ 'class'=>'form-control', 'disabled'=>'disabled' ]) !!}
							@endif
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'invoices' ? 'active' : '' }}" id="tab-site-invoices">
				<div class="add-invoice-area">
					{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Admin\SitesController@postInvoice', $site->id ], 'id'=>'invoice-form', 'files'=>true ]) !!}
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('title', Lang::get('admin/sites.invoices.title')) !!}
									{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('amount', Lang::get('admin/sites.invoices.amount')) !!}
									{!! Form::text('amount', null, [ 'class'=>'form-control required number' ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('document', Lang::get('admin/sites.invoices.document')) !!}
									{!! Form::file('document', [ 'class'=>'form-control required', 'accept'=>'application/pdf' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('uploaded_at', Lang::get('admin/sites.invoices.uploaded_at')) !!}
									{!! Form::text('uploaded_at', null, [ 'class'=>'form-control required', 'accept'=>'application/pdf' ]) !!}
								</div>
							</div>
						</div>
						<div class="text-right">
							{!! Form::button( Lang::get('admin/sites.invoices.new.button'), [ 'type'=>'submit', 'class'=>'btn btn-default btn-sm' ]) !!}
						</div>
						<hr />
					{!! Form::close() !!}
				</div>
				@if ( $invoices->count() < 1 )
					{{ Lang::get('account/payment.invoices.empty') }}
				@else
					<table class="table">
						<thead>
							<tr>
								<th>{{ Lang::get('admin/sites.invoices.uploaded_at') }}</th>
								<th>{{ Lang::get('admin/sites.invoices.title') }}</th>
								<th class="text-right">{{ Lang::get('admin/sites.invoices.amount') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($invoices as $invoice)
								<tr>
									<td>{{ $invoice->uploaded_at->format('d/m/Y') }}</td>
									<td>{{ $invoice->title }}</td>
									<td class="text-right">{{ price($invoice->amount) }}</td>
									<td class="text-right">
										{!! Form::open([ 'method'=>'DELETE', 'action'=>[ 'Admin\SitesController@deleteInvoice', $invoice->id ], 'class'=>'delete-form' ]) !!}
											{!! Form::button(Lang::get('general.delete'), [ 'type'=>'submit', 'class'=>'btn btn-xs btn-default' ]) !!}
											<a href="{{ action('Admin\SitesController@getInvoice', [ $invoice->id, $invoice->invoice_filename ]) }}" class="btn btn-xs btn-default" target="_blank">{{ Lang::get('general.view') }}</a>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($invoices, [ 'current_tab'=>'invoices' ]+Input::only('limit')) !!}
				@endif
			</div>

		</div>

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		</div>


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

			var form_invoice = $('#invoice-form')
			form_invoice.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}			});
			form_invoice.find('input[name="uploaded_at"]').datetimepicker({
				format: 'YYYY-MM-DD'
			});

			$('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( Lang::get('admin/sites.invoices.warning.delete') ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});
		});
	</script>

@endsection