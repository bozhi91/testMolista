@extends('layouts.account')

@section('account_content')

	<div id="admin-customers">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/customers.show.h1') }}</h1>

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/customers.show.tab.general') }}</a></li>
			<li role="presentation"><a href="#tab-profile" aria-controls="tab-profile" role="tab" data-toggle="tab">{{ Lang::get('account/customers.profile') }}</a></li>
			<li role="presentation"><a href="#tab-properties" aria-controls="tab-properties" role="tab" data-toggle="tab">{{ Lang::get('account/customers.properties') }} ({{ number_format($customer->properties->count(), 0, ',', '.') }})</a></li>
			<li role="presentation"><a href="#tab-matches" aria-controls="tab-matches" role="tab" data-toggle="tab">{{ Lang::get('account/customers.matches') }} ({{ number_format($customer->possible_matches->count(), 0, ',', '.') }})</a></li>
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
								{!! Form::label('size_max', Lang::get('account/properties.size.min')) !!}
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
										{!! Form::checkbox('newly_build', 1, null) !!}
										{{ Lang::get('account/properties.newly_build') }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-3">
							<div class="form-group">
								<div class="checkbox error-container">
									<label>
										{!! Form::checkbox('second_hand', 1, null) !!}
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
											{!! Form::checkbox('services[]', $service->id, null) !!}
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

			<div role="tabpanel" class="tab-pane tab-main" id="tab-properties">
				<p>Current properties</p>
			</div>

			<div role="tabpanel" class="tab-pane tab-main" id="tab-matches">
				<p>Possible matches</p>
			</div>

		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var profile_form = $('#profile-form');

			profile_form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f){
					LOADING.show();
					$.ajax({
						dataType: 'json',
						type: 'post',
						url: profile_form.attr('action'),
						data: profile_form.serialize(),
						success: function(data) {
							if ( data.success ) {
								alertify.success("{{ print_js_string( Lang::get('general.messages.success.saved') ) }}");
							} else {
								alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
							}
							LOADING.hide();
						},
						error: function() {
							LOADING.hide();
							alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
						}
					});
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

		});
	</script>

@endsection