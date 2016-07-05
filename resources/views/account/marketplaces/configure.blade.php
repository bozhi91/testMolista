@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#tab-configuration hr:last-child { display: none; }
	</style>
	<div id="account-marketplaces">

        @include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/marketplaces.h1') }}: {{ $marketplace->name }}</h1>

		{!! Form::model($marketplace, [ 'id'=>'marketplace-form', 'action'=>[ 'Account\MarketplacesController@postConfigure', $marketplace->code ] ]) !!}

			<ul class="nav nav-tabs main-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.general') }}</a></li>
				<li role="presentation"><a href="#tab-configuration" aria-controls="tab-configuration" role="tab" data-toggle="tab">{{ Lang::get('admin/marketplaces.tab.configuration') }}</a></li>
			</ul>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
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

				<div role="tabpanel" class="tab-pane tab-main" id="tab-configuration">
					@if ( empty($marketplace->additional_configuration) )
						<p>{{ Lang::get('account/marketplaces.configuration.none') }}</p>
					@else
						@if ( !empty($marketplace->additional_configuration['xml_owners']) )
							@include('account.marketplaces.configure-owner')
						@else
							<?php echo '<pre>' . print_r($marketplace->additional_configuration, true) . '</pre>'; ?>
						@endif
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

		});
	</script>

@endsection