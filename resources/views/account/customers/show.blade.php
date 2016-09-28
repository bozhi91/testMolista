@extends('layouts.account')

<?php
	$currency =	empty($profile->currency) ? $current_site->infocurrency : $profile->infocurrency;
?>

@section('account_content')

	<style type="text/css">
		#account-visits-ajax-tab .column-customer { display: none; }
	</style>

	<div id="account-customers">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/customers.show.h1') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="{{$current_tab == 'general' ? 'active' : '' }}"><a href="#tab-general" aria-controls="tab-general" role="tab" data-tab="general" data-toggle="tab">{{ Lang::get('account/customers.show.tab.general') }}</a></li>
			<li role="presentation" class="{{$current_tab == 'profile' ? 'active' : '' }}"><a href="#tab-profile" aria-controls="tab-profile" role="tab" data-tab="profile" data-toggle="tab">{{ Lang::get('account/customers.profile') }}</a></li>
			<li role="presentation" class="{{$current_tab == 'properties' ? 'active' : '' }}"><a href="#tab-properties" aria-controls="tab-properties" role="tab" data-tab="properties" data-toggle="tab">{{ Lang::get('account/customers.properties') }} (<span id="properties-total">{{ number_format($customer->properties->count(),0,',','.') }}</span>)</a></li>
			<li role="presentation" class="{{$current_tab == 'matches' ? 'active' : '' }}"><a href="#tab-matches" aria-controls="tab-matches" role="tab" data-tab="matches" data-toggle="tab">{{ Lang::get('account/customers.matches') }} (<span id="matches-total">{{ number_format($customer->possible_matches->count(),0,',','.') }}</span>)</a></li>
			<li role="presentation" class="{{$current_tab == 'discards' ? 'active' : '' }}"><a href="#tab-discards" aria-controls="tab-discards" role="tab" data-tab="discards" data-toggle="tab">{{ Lang::get('account/customers.discards') }} (<span id="discards-total">{{ number_format($customer->properties_discards->count(),0,',','.') }}</span>)</a></li>
			<li role="presentation" class="{{$current_tab == 'visits' ? 'active' : '' }}"><a href="#tab-visits" aria-controls="tab-visits" role="tab" data-tab="visits" data-toggle="tab">{{ Lang::get('account/visits.title') }}</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'general' ? 'active' : '' }}" id="tab-general">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.name') ) !!}
							{!! Form::text(null, @$customer->first_name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.last_name') ) !!}
							{!! Form::text(null, @$customer->last_name, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.email') ) !!}
							{!! Form::text(null, @$customer->email, [ 'class'=>'form-control email', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.phone') ) !!}
							{!! Form::text(null, @$customer->phone, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.locale') ) !!}
							{!! Form::text(null, @$site_setup['locales_select'][$customer->locale], [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.origin') ) !!}
							{!! Form::text(null, @$customer->origin, [ 'class'=>'form-control', 'readonly'=>'readonly', 'style'=>'text-transform: capitalize;' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label(null, Lang::get('account/customers.created') ) !!}
							{!! Form::text(null, @$customer->created_at->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'profile' ? 'active' : '' }}" id="tab-profile">
				{!! Form::model($profile, [ 'action'=>[ 'Account\CustomersController@postProfile', urlencode($customer->email) ], 'method'=>'POST', 'id'=>'profile-form' ]) !!}
					{!! Form::hidden('current_tab', $current_tab) !!}
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('country_id', Lang::get('account/properties.country')) !!}
								{!! Form::select('country_id', $countries, $country_id, [ 'class'=>'form-control country-input', 'data-rel'=>'.state-input, .city-input', 'data-target'=>'.state-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'state') ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								<?php $tmp = empty($states) ? [ ''=>'' ] : [ ''=>'' ] + $states; ?>
								{!! Form::label('state_id', Lang::get('account/properties.state')) !!}
								{!! Form::select('state_id', $tmp, null, [ 'class'=>'form-control state-input', 'data-rel'=>'.city-input', 'data-target'=>'.city-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'city') ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								<?php $tmp = empty($cities) ? [ ''=>'' ] : [ ''=>'' ] + $cities; ?>
								{!! Form::label('city_id', Lang::get('account/properties.city')) !!}
								{!! Form::select('city_id', $tmp, null, [ 'class'=>'form-control city-input' ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('district', Lang::get('account/properties.district')) !!}
								{!! Form::text('district', null, [ 'class'=>'form-control district-input' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group error-container">
								{!! Form::label('zipcode', Lang::get('account/properties.zipcode')) !!}
								{!! Form::text('zipcode', null, [ 'class'=>'form-control' ]) !!}
							</div>
						</div>
					</div>
					<hr />
					{!! Form::hidden('currency', $currency->code) !!}
					<div class="row">
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('mode', Lang::get('account/properties.mode')) !!}
								{!! Form::select('mode', [ ''=>'' ] + $modes, null, [ 'class'=>'form-control' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('type', Lang::get('account/properties.type')) !!}
								{!! Form::select('type', [ ''=>'' ] + $types, null, [ 'class'=>'form-control' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('price_min', Lang::get('account/properties.price.min')) !!}
								<div class="input-group">
									@if ( $currency->position == 'before' )
										<div class="input-group-addon">{{ $currency->symbol }}</div>
									@endif
									@if ( empty($profile->price_max) )
										{!! Form::text('price_min', null, [ 'class'=>'form-control range-rel-input price-min-input number', 'data-rel'=>'.price-max-input', 'data-attr'=>'min', 'min'=>'0' ]) !!}
									@else
										{!! Form::text('price_min', null, [ 'class'=>'form-control range-rel-input price-min-input number', 'data-rel'=>'.price-max-input', 'data-attr'=>'min', 'min'=>'0', 'max'=>$profile->price_max ]) !!}
									@endif
									@if ( $currency->position == 'after' )
										<div class="input-group-addon">{{ $currency->symbol }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('price_max', Lang::get('account/properties.price.max')) !!}
								<div class="input-group">
									@if ( $currency->position == 'before' )
										<div class="input-group-addon">{{ $currency->symbol }}</div>
									@endif
									{!! Form::text('price_max', null, [ 'class'=>'form-control range-rel-input price-max-input number', 'data-rel'=>'.price-min-input', 'data-attr'=>'max', 'data-remove'=>1, 'min'=>( empty($profile->price_min) ? 0 : $profile->price_min ) ]) !!}
									@if ( $currency->position == 'after' )
										<div class="input-group-addon">{{ $currency->symbol }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr />
					{!! Form::hidden('size_unit', empty($profile->size_unit) ? 'sqm' : $profile->size_unit) !!}
					<div class="row">
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('size_min', Lang::get('account/properties.size.min')) !!}
								<div class="input-group">
									@if ( empty($profile->size_max) )
										{!! Form::text('size_min', null, [ 'class'=>'form-control range-rel-input size-min-input number', 'data-rel'=>'.size-max-input', 'data-attr'=>'min', 'min'=>'0' ]) !!}
									@else
										{!! Form::text('size_min', null, [ 'class'=>'form-control range-rel-input size-min-input number', 'data-rel'=>'.size-max-input', 'data-attr'=>'min', 'min'=>'0', 'max'=>$profile->size_max ]) !!}
									@endif
									<div class="input-group-addon">m²</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('size_max', Lang::get('account/properties.size.max')) !!}
								<div class="input-group">
									{!! Form::text('size_max', null, [ 'class'=>'form-control range-rel-input size-max-input number', 'data-rel'=>'.size-min-input', 'data-attr'=>'max', 'data-remove'=>1, 'min'=>( empty($profile->size_min) ? 0 : $profile->size_min ) ]) !!}
									<div class="input-group-addon">m²</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('rooms', Lang::get('account/properties.rooms')) !!}
								{!! Form::text('rooms', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('baths', Lang::get('account/properties.baths')) !!}
								{!! Form::text('baths', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
							</div>
						</div>
					</div>
					<hr />
					{!! Form::label(null, Lang::get('account/properties.characteristics')) !!}
					<div class="row">
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('more_attributes[newly_build]', 1, null) !!}
										{{ Lang::get('account/properties.newly_build') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('more_attributes[second_hand]', 1, null) !!}
										{{ Lang::get('account/properties.second_hand') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('more_attributes[bank_owned]', 1, null) !!}
										{{ Lang::get('account/properties.bank_owned') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('more_attributes[private_owned]', 1, null) !!}
										{{ Lang::get('account/properties.private_owned') }}
									</label>
								</div>
							</div>
						</div>
					</div>
					<hr />
					{!! Form::label(null, Lang::get('account/properties.services')) !!}
					<div class="row">
						@foreach ($services as $service)
							<div class="col-xs-12 col-sm-3">
								<div class="form-group">
									<div class="checkbox error-container">
										<label>
											{!! Form::checkbox("more_attributes[services][{$service->id}]", $service->id, null) !!}
											{{ $service->title }}
										</label>
									</div>
								</div>
							</div>
						@endforeach
					</div>
					<div class="text-right">
						{!! Form::button(Lang::get('account/customers.profile.update'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
					</div>
				{!! Form::close() !!}
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'properties' ? 'active' : '' }} property-list-tab" data-total="#properties-total" id="tab-properties">
				<div class="alert alert-info properties-empty {{ $customer->properties->count() > 0 ? 'hide' : '' }}">{{ Lang::get('account/properties.empty') }}</div>
				<div class="properties-list {{ $customer->properties->count() < 1 ? 'hide' : '' }}">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{ Lang::get('account/properties.ref') }}</th>
								<th>{{ Lang::get('account/properties.column.title') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($customer->properties as $property)
								<tr>
									<td>{{$property->ref}}</td>
									<td>{{$property->title}}</td>
									<td class="text-right text-nowrap">
										{!! Form::open([ 'action'=>[ 'Account\CustomersController@deleteRemovePropertyCustomer', $property->slug ], 'method'=>'DELETE', 'class'=>'delete-property-form' ]) !!}
											{!! Form::hidden('customer_id', $customer->id) !!}
											{!! Form::hidden('current_tab', 'properties') !!}
											@if ( $event = $property->calendars->where('customer_id', $customer->id)->last() )
												<a href="{{ action('Account\Calendar\BaseController@getEvent', $event->id) }}"><i class="fa fa-calendar-check-o has-tooltip" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('account/calendar.scheduled') }}"></i></a>
											@endif
											<a href="{{ action('Account\Calendar\BaseController@getCreate') }}?property_ids[]={{$property->id}}&customer_id={{@$customer->id}}" class="btn btn-info btn-xs">{{ Lang::get('account/calendar.button.schedule') }}</a>
											{!! Form::button(Lang::get('account/customers.discards.action'), [ 'type'=>'submit', 'class'=>'btn btn-danger btn-xs' ]) !!}
											<a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="btn btn-default btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'matches' ? 'active' : '' }} property-list-tab" data-total="#matches-total" id="tab-matches">
				<div class="alert alert-info properties-empty {{ $customer->possible_matches->count() > 0 ? 'hide' : '' }}">{{ Lang::get('account/properties.empty') }}</div>
				<div class="properties-list {{ $customer->possible_matches->count() < 1 ? 'hide' : '' }}">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{ Lang::get('account/properties.ref') }}</th>
								<th>{{ Lang::get('account/properties.column.title') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($customer->possible_matches as $property)
								<tr>
									<td>{{$property->ref}}</td>
									<td>{{$property->title}}</td>
									<td class="text-right text-nowrap">
										{!! Form::open([ 'action'=>[ 'Account\CustomersController@postAddPropertyCustomer', $property->slug ], 'method'=>'POST', 'class'=>'add-property-form' ]) !!}
											{!! Form::hidden('customer_id', $customer->id) !!}
											{!! Form::hidden('current_tab', 'matches') !!}
											{!! Form::button(Lang::get('account/customers.matches.action'), [ 'type'=>'submit', 'class'=>'btn btn-default btn-xs' ]) !!}
											<a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="btn btn-default btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'discards' ? 'active' : '' }} property-list-tab" data-total="#discards-total" id="tab-discards">
				<div class="alert alert-info properties-empty {{ $customer->properties_discards->count() > 0 ? 'hide' : '' }}">{{ Lang::get('account/properties.empty') }}</div>
				<div class="properties-list {{ $customer->properties_discards->count() < 1 ? 'hide' : '' }}">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{ Lang::get('account/properties.ref') }}</th>
								<th>{{ Lang::get('account/properties.column.title') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($customer->properties_discards as $property)
								<tr>
									<td>{{$property->ref}}</td>
									<td>{{$property->title}}</td>
									<td class="text-right text-nowrap">
										{!! Form::open([ 'action'=>[ 'Account\CustomersController@putUndiscardPropertyCustomer', $property->slug ], 'method'=>'PUT', 'class'=>'undelete-property-form' ]) !!}
											{!! Form::hidden('customer_id', $customer->id) !!}
											{!! Form::hidden('current_tab', 'discards') !!}
											{!! Form::button(Lang::get('account/customers.discards.undelete'), [ 'type'=>'submit', 'class'=>'btn btn-default btn-xs' ]) !!}
											<a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="btn btn-default btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
										{!! Form::close() !!}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'visits' ? 'active' : '' }}" id="tab-visits">
				@include('account.visits.ajax-tab', [
					'visits_init' => true,
				])
			</div>

		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#account-customers');
			var profile_form = $('#profile-form');

			var tabs = cont.find('.main-tabs');
			tabs.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				cont.find('input[name="current_tab"]').val( $(this).data('tab') );
				cont.find('.has-select-2').select2();
			});

			profile_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f){
					LOADING.show();
					f.submit();
				}
			});

			profile_form.on('change', '.range-rel-input', function(){
				var el = $(this);
				if ( el.val() && el.valid() ) {
					profile_form.find( el.data().rel ).attr(el.data().attr, el.val()).valid();
				} else if ( el.data().remove ) {
					profile_form.find( el.data().rel ).removeAttr( el.data().attr ).valid();
				} else {
					profile_form.find( el.data().rel ).attr(el.data().attr, 0).valid();
				}
			});

			profile_form.on('change', '.country-input, .state-input', function(){
				var el = $(this);

				profile_form.find( el.data().rel ).html('<option value=""></option>');

				if ( !el.val() ) {
					return;
				}

				LOADING.show();

				var data = {};
				data[el.attr('name')] = el.val();

				$.ajax({
					dataType: 'json',
					url: el.data().action,
					data: data,
					success: function(data) {
						if ( data ) {
							var target = profile_form.find( el.data().target );
							$.each(data, function(k,v) {
								target.append('<option value="' + v.id + '">' + v.label + '</option>');
							});
						}
						LOADING.hide();
					},
					error: function() {
						LOADING.hide();
					}
				});
			});

			cont.find('form.delete-property-form').each(function(){
				var form = $(this);
				form.validate({
					submitHandler: function(f){
						LOADING.show();
						f.submit();
					}
				});
			});

			cont.find('form.undelete-property-form').each(function(){
				$(this).validate({
					submitHandler: function(f){
						LOADING.show();
						f.submit();
					}
				});
			});

			cont.find('form.undelete-property-form').each(function(){
				$(this).validate({
					submitHandler: function(f){
						LOADING.show();
						f.submit();
					}
				});
			});

			cont.find('form.add-property-form').each(function(){
				$(this).validate({
					submitHandler: function(f){
						LOADING.show();
						f.submit();
					}
				});
			});

			cont.find('.has-tooltip').tooltip();

			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '{{ action('Account\Visits\AjaxController@getTab') }}',
				data: {
					customer_id: {{ $customer->id }}
				},
				success: function(data) {
					if ( data.success ) {
						$('#account-visits-ajax-tab').html( data.html );
					} else {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
					}
				},
				error: function() {
						$('#account-visits-ajax-tab').html('<div class="alert alert-danger">{{ print_js_string( Lang::get('general.messages.error') ) }}</div>')
				}
			});

		});
	</script>

@endsection
