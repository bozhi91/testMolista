@extends('layouts.account')

@section('account_content')

	<div id="account-customers">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/customers.show.h1') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/customers.show.tab.general') }}</a></li>
			<li role="presentation"><a href="#tab-profile" aria-controls="tab-profile" role="tab" data-toggle="tab">{{ Lang::get('account/customers.profile') }}</a></li>
			<li role="presentation"><a href="#tab-properties" aria-controls="tab-properties" role="tab" data-toggle="tab">{{ Lang::get('account/customers.properties') }} (<span id="properties-total">0</span>)</a></li>
			<li role="presentation"><a href="#tab-matches" aria-controls="tab-matches" role="tab" data-toggle="tab">{{ Lang::get('account/customers.matches') }} (<span id="matches-total">0</span>)</a></li>
		</ul>

		<div class="tab-content">

			<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
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
							{!! Form::label(null, Lang::get('account/customers.created') ) !!}
							{!! Form::text(null, @$customer->created_at->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-profile">
				{!! Form::model($profile, [ 'action'=>[ 'Account\CustomersController@postProfile', urlencode($customer->email) ], 'method'=>'POST', 'id'=>'profile-form' ]) !!}
					{!! Form::hidden('current_tab', session('current_tab', '#tab-general')) !!}
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
					{!! Form::hidden('currency', empty($profile->currency) ? 'EUR' : $profile->currency) !!}
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
									@if ( empty($profile->price_max) )
										{!! Form::text('price_min', null, [ 'class'=>'form-control range-rel-input price-min-input number', 'data-rel'=>'.price-max-input', 'data-attr'=>'min', 'min'=>'0' ]) !!}
									@else
										{!! Form::text('price_min', null, [ 'class'=>'form-control range-rel-input price-min-input number', 'data-rel'=>'.price-max-input', 'data-attr'=>'min', 'min'=>'0', 'max'=>$profile->price_max ]) !!}
									@endif
									<div class="input-group-addon">{{ price_symbol(empty($profile->currency) ? 'EUR' : $profile->currency) }}</div>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group error-container">
								{!! Form::label('price_max', Lang::get('account/properties.price.max')) !!}
								<div class="input-group">
									{!! Form::text('price_max', null, [ 'class'=>'form-control range-rel-input price-max-input number', 'data-rel'=>'.price-min-input', 'data-attr'=>'max', 'data-remove'=>1, 'min'=>( empty($profile->price_min) ? 0 : $profile->price_min ) ]) !!}
									<div class="input-group-addon">{{ price_symbol(empty($profile->currency) ? 'EUR' : $profile->currency) }}</div>
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

			<div role="tabpanel" class="tab-pane tab-main property-list-tab" data-total="#properties-total" id="tab-properties">
				<div class="alert alert-info properties-empty hide">{{ Lang::get('account/properties.empty') }}</div>
				<div class="properties-list hide">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{ Lang::get('account/properties.column.title') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($customer->properties as $property)
								<tr>
									<td>{{$property->title}}</td>
									<td class="text-right"><a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="btn btn-default btn-xs" target="_blank">{{ Lang::get('general.view') }}</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main property-list-tab" data-total="#matches-total" id="tab-matches">
				<div class="alert alert-info properties-empty hide">{{ Lang::get('account/properties.empty') }}</div>
				<div class="properties-list hide">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>{{ Lang::get('account/properties.column.title') }}</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($customer->possible_matches as $property)
								<tr>
									<td>{{$property->title}}</td>
									<td class="text-right"><a href="{{ action('Web\PropertiesController@details', $property->slug) }}" class="btn btn-default btn-xs" target="_blank">{{ Lang::get('general.view') }}</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
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

			cont.find('.property-list-tab').each(function(){
				var target = $(this).find('.properties-list');
				var items = target.find('tbody tr').length;

				$( $(this).data().total ).text( SITECOMMON.number_format(items,0,',','.') );

				if ( items > 0 ) {
					target.removeClass('hide');
				} else {
					$(this).find('.properties-empty').removeClass('hide');
				}
			});

			var tabs = cont.find('.main-tabs');
			var current_tab = cont.find('input[name="current_tab"]').val();
			tabs.find('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
				cont.find('input[name="current_tab"]').val( $(this).attr('href') );
				cont.find('.has-select-2').select2();
			});
			if ( current_tab != '#tab-general' ) {
				tabs.find('a[href="' + current_tab + '"]').tab('show');
			}

		});
	</script>

@endsection