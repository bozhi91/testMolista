@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#tab-configuration hr:last-child { display: none; }
	</style>
	<div id="account-marketplaces">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/marketplaces.h1') }}: {{ $marketplace->name }}</h1>

		{!! Form::model($marketplace, [ 'id'=>'marketplace-form', 'action'=>[ 'Account\MarketplacesController@postConfigure', $marketplace->code ] ]) !!}
			<input type="hidden" name="current_tab" value="{{ $current_tab }}" />

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="{{ $current_tab == 'general' ? 'active' : '' }}"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab" data-tab="general">{{ Lang::get('admin/marketplaces.tab.general') }}</a></li>
				<li role="presentation" class="{{ $current_tab == 'configuration' ? 'active' : '' }}"><a href="#tab-configuration" aria-controls="tab-configuration" role="tab" data-toggle="tab" data-tab="configuration">{{ Lang::get('admin/marketplaces.tab.configuration') }}</a></li>
				<li role="presentation" class="{{ $current_tab == 'properties' ? 'active' : '' }}"><a href="#tab-properties" aria-controls="tab-properties" role="tab" data-toggle="tab" data-tab="properties">{{ Lang::get('account/marketplaces.properties') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'general' ? 'active' : '' }}" id="tab-general">
					<div class="row">
						<div class="col-xs-12 col-sm-9">
							@if ( @$marketplace->instructions )
								<h4>{{ Lang::get('account/marketplaces.instructions') }}</h4>
								{!! nl2p($marketplace->instructions) !!}
								<br />
							@endif

							<h4>{{ Lang::get('account/marketplaces.feed.properties.url') }}</h4>
							<p>
								<a href="{{ $current_site->getXmlFeedUrl($marketplace->code,'properties') }}" target="_blank">{{ $current_site->getXmlFeedUrl($marketplace->code,'properties') }}</a>
							</p>

							@if ( @$marketplace->configuration['xml_owners'] )
								<br />
								<h4>{{ Lang::get('account/marketplaces.feed.owners.url') }}</h4>
								<p>
									<a href="{{ $current_site->getXmlFeedUrl($marketplace->code,'owners') }}" target="_blank">{{ $current_site->getXmlFeedUrl($marketplace->code,'owners') }}</a>
								</p>
							@endif
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('marketplace_enabled', Lang::get('account/marketplaces.enabled')) !!}
								{!! Form::select('marketplace_enabled', [
									0 => Lang::get('general.no'),
									1 => Lang::get('general.yes'),
								], null, [ 'class'=>'form-control' ]) !!}
							</div>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'configuration' ? 'active' : '' }}" id="tab-configuration">
					<h4>{{ Lang::get('account/marketplaces.maxproperties.title') }}</h4>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<div class="error-container">
									{!! Form::text('marketplace_maxproperties', null, [ 'class'=>'form-control digits', 'min'=>1 ]) !!}
								</div>
								<div class="help-block">{!! Lang::get('account/marketplaces.maxproperties.helper') !!}</div>
							</div>
						</div>
					</div>

					@if ( !empty($marketplace->additional_configuration['xml_owners']) )
						@include('account.marketplaces.configure-owner')
					@endif

					@if ( !empty($marketplace->additional_configuration['configuration']) )
						@include('account.marketplaces.configure-fields', ['configuration' => $marketplace->additional_configuration['configuration'], 'values' => @$configuration->configuration])
					@endif
				</div>

				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'properties' ? 'active' : '' }}" id="tab-properties">
					@if ( empty($properties) || $properties->count() < 1)
						<div class="alert alert-info">{{ Lang::get('account/properties.empty') }}</div>
					@else
						<table class="table table-striped">
							<thead>
								<tr>
									<?php
										$pagination_url = implode('',[
											action('Account\MarketplacesController@getConfigure', $marketplace->code),
											'?',
											http_build_query(Input::except('page')+[ 'current_tab'=>'properties' ]),
										]);
									?>
									{!! drawSortableHeaders($pagination_url, [
										'reference' => [ 'title' => Lang::get('account/properties.ref') ],
										'title' => [ 'title' => Lang::get('account/properties.column.title') ],
										'creation' => [ 'title' => Lang::get('account/properties.column.created') ],
										'exported' => [ 'title' => Lang::get('account/marketplaces.properties.published'), 'class'=>'text-center text-nowrap' ],
										'enabled' => [ 'title' => Lang::get('account/marketplaces.properties.enabled'), 'class'=>'text-center text-nowrap' ],
									]) !!}
								</tr>
							</thead>
							<tbody>
								@foreach ($properties as $property)
									<tr>
										<td>{{ $property->ref }}</td>
										<td>{{ $property->title }}</td>
										<td>{{  $property->created_at->format('d/m/Y') }}</td>
										<td class="text-center">
											<span class="glyphicon glyphicon-{{ $property->exported_to_marketplace ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</td>
										<td class="text-center">
											<span class="glyphicon glyphicon-{{ $property->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! drawPagination($properties, Input::except('page')+[ 'current_tab'=>'properties' ]) !!}
					@endif
				</div>

			</div>

			<br />

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#account-marketplaces');
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
					}
				},
				rules: {
					"marketplace_configuration[owner][cif]": {
						pattern: '(^[A|B|C|D|E|F|G|H|J|K|L|M|N|P|Q|S|V][0-9]{7}[0-9A-J]$)|(^[X|Y|Z][0-9]{7}[T|R|W|A|G|M|Y|F|P|D|X|B|N|J|Z|S|Q|V|H|L|C|K|E]$)|(^[0-9]{8}[T|R|W|A|G|M|Y|F|P|D|X|B|N|J|Z|S|Q|V|H|L|C|K|E]$)'
					}
				},
				messages: {
					"marketplace_configuration[owner][cif]": {
						pattern: "{{ print_js_string( Lang::get('account/marketplaces.configuration.owner.cif.error') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			cont.find('.main-tabs').find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				cont.find('input[name="current_tab"]').val( $(this).data().tab );
			});

		});
	</script>

@endsection
