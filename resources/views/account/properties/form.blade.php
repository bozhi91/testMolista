<?php
	$infocurrency = ($item && $item->infocurrency) ? $item->infocurrency : $current_site->infocurrency;

	// Priorizar países
	if ( empty($current_site->country_ids) )
	{
		$tmp = $countries->toArray();

		$countries = [
				68 => $tmp[68], //España
			/*	157 => $tmp[157], //Mexico
				49 => $tmp[49], //Colombia
				10 => $tmp[10], //Argentina
				46 => $tmp[46], //Chile
				174 => $tmp[174], //Peru
				63 => $tmp[63], //Ecuador*/
			] + [
				'' => '----------------------------',
			] + $tmp;
	}
	else {
		$countries = $countries->toArray();
	}
	$body = "Insert your HTML code here...";
	if(!empty($property)){
		$checkboxDesde = App\Http\Controllers\Account\PropertiesController::getCheckboxDesdeState($property['id']);
        $body = $property->html_property;
	}
?>

<style type="text/css">
	#tab-marketplaces .marketplace-name { display: inline-block; padding-left: 25px; background: left center no-repeat; }
	#tab-visits .column-property { display: none; }
</style>



{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}
	{!! Form::hidden('current_tab', $current_tab) !!}
	{!! Form::hidden('label_color', null) !!}

	<div class="custom-tabs">

		<ul class="nav nav-tabs main-tabs" role="tablist">
			<li role="presentation" class="{{ $current_tab == 'general' ? 'active' : '' }}"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab" data-tab="general">{{ Lang::get('account/properties.tab.general') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'location' ? 'active' : '' }}"><a href="#tab-location" aria-controls="tab-location" role="tab" data-toggle="tab" data-tab="location">{{ Lang::get('account/properties.tab.location') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'text' ? 'active' : '' }}"><a href="#tab-text" aria-controls="tab-text" role="tab" data-toggle="tab" data-tab="text">{{ Lang::get('account/properties.tab.text') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'images' ? 'active' : '' }}"><a href="#tab-images" aria-controls="tab-images" role="tab" data-toggle="tab" data-tab="images">{{ Lang::get('account/properties.tab.images') }}</a></li>
			<li role="presentation" class="{{ $current_tab == 'videos' ? 'active' : '' }}"><a href="#tab-videos" aria-controls="tab-videos" role="tab" data-toggle="tab" data-tab="videos">{{ Lang::get('account/properties.tab.videos') }}</a></li>
			@if ( $item && !$isCreate)
				<li role="presentation" class="{{ $current_tab == 'employees' ? 'active' : '' }}"><a href="#tab-employees" aria-controls="tab-employees" role="tab" data-toggle="tab" data-tab="employees">{{ Lang::get('account/properties.tab.employees') }}</a></li>
				@if ( $marketplaces->count() > 0 )
					<li role="presentation" class="{{ $current_tab == 'marketplaces' ? 'active' : '' }}"><a href="#tab-marketplaces" aria-controls="tab-marketplaces" role="tab" data-toggle="tab" data-tab="marketplaces">{{ Lang::get('account/menu.marketplaces') }}</a></li>
				@endif
				<li role="presentation" class="{{$current_tab == 'visits' ? 'active' : '' }}"><a href="#tab-visits" aria-controls="tab-visits" role="tab" data-toggle="tab" data-tab="visits">{{ Lang::get('account/visits.title') }}</a></li>
			@else
				<li role="presentation" class="{{ $current_tab == 'seller' ? 'active' : '' }}"><a href="#tab-seller" aria-controls="tab-seller" role="tab" data-toggle="tab" data-tab="general">{{ Lang::get('account/properties.tab.seller') }}</a></li>
			@endif

			<li role="presentation" class="{{ $current_tab == 'html' ? 'active' : '' }}">
				<a href="#tab-html" aria-controls="tab-html" role="tab" data-toggle="tab" data-tab="html">HTML</a></li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'location' ? 'active' : '' }}" id="tab-html">
				<h2>{{ Lang::get('general.htmlSnippet') }}</h2>
				<br/>
				<div>

					<textarea name="body" class="summernote" style="height: 300px !important;" contenteditable="false">
					{{ $body }}
					</textarea><br/><br/>

				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'general' ? 'active' : '' }}" id="tab-general">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('ref', Lang::get('account/properties.ref').' *') !!}
							{!! Form::text('ref', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('type', Lang::get('account/properties.type').' *') !!}
							{!! Form::select('type', [ ''=>'' ] + $types, null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('mode', Lang::get('account/properties.mode').' *') !!}
							{!! Form::select('mode', [ ''=>'' ] + $modes, null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="row">
							<div class="col-sm-4" style="margin-top:25px;">
								@if(!empty($property))
									{!! Form::hidden('propertyId', $property['id']) !!}
									{{ Lang::get('web/properties.from') }}
									@if($checkboxDesde=='1')
										<input type="checkbox" name="desde" value="Hourly" checked>
										@else
										<input type="checkbox" name="desde" value="Hourly">
									@endif
								@endif
							</div>
							<div class="col-sm-8">
								{!! Form::hidden('currency', $infocurrency->code) !!}
								{!! Form::label('price', Lang::get('account/properties.price').' *') !!}
								<div class="input-group">
									@if ( $infocurrency->position == 'before' )
										<div class="input-group-addon">{{ $infocurrency->symbol }}</div>
									@endif
									{!! Form::text('price', null, [ 'class'=>'form-control required number', 'min'=>'0' ]) !!}
									@if ( $infocurrency->position == 'after' )
										<div class="input-group-addon">{{ $infocurrency->symbol }}</div>
									@endif
								</div>

							</div>
						</div>

					</div>


					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::hidden('currency', $infocurrency->code) !!}
							{!! Form::label('price_before', Lang::get('account/properties.price_before')) !!}
							<div class="input-group">
								@if ( $infocurrency->position == 'before' )
									<div class="input-group-addon">{{ $infocurrency->symbol }}</div>
								@endif
								{!! Form::text('price_before', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
								@if ( $infocurrency->position == 'after' )
									<div class="input-group-addon">{{ $infocurrency->symbol }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('discount', Lang::get('account/properties.discount')) !!}
							<div class="input-group">
								{!! Form::text('discount', null, [ 'class'=>'form-control', 'readonly' => 'readonly', 'max'=>'0' ]) !!}
								<div class="input-group-addon">%</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group error-container">
							{!! Form::label('discount_show', Lang::get('account/properties.discount_show')) !!}
							{!! Form::select('discount_show', [ '' => '','0'=>Lang::get('general.no'), '1'=>Lang::get('general.yes') ], null, [ 'class'=>'form-control' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[expenses]', Lang::get('account/properties.expenses')) !!}
				            <div class="input-group">
				                @if ( $infocurrency->position == 'before' )
				                    <div class="input-group-addon">{{ $infocurrency->symbol }}</div>
				                @endif
				                {!! Form::text('details[expenses]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                @if ( $infocurrency->position == 'after' )
				                    <div class="input-group-addon">{{ $infocurrency->symbol }}</div>
				                @endif
				            </div>
				        </div>
				    </div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('rooms', Lang::get('account/properties.rooms').' *') !!}
							{!! Form::text('rooms', null, [ 'class'=>'form-control required digits', 'min'=>'0' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('baths', Lang::get('account/properties.baths').' *') !!}
							{!! Form::text('baths', null, [ 'class'=>'form-control required digits', 'min'=>'0' ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[bedrooms]', Lang::get('account/properties.bedrooms')) !!}
				            {!! Form::text('details[bedrooms]', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[toilettes]', Lang::get('account/properties.toilettes')) !!}
				            {!! Form::text('details[toilettes]', null, [ 'class'=>'form-control digits', 'min'=>'0' ]) !!}
				        </div>
				    </div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group">
							<div class="error-container">
								{!! Form::label('ec', Lang::get('account/properties.energy.certificate')) !!}
								{!! Form::select('ec', [''=>'']+$energy_types, null, [ 'class'=>'form-control' ]) !!}
							</div>
							<div class="help-block">
								<label>
									{!! Form::checkbox('ec_pending', 1, null) !!}
									{{ Lang::get('account/properties.energy.certificate.pending') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('construction_year', Lang::get('account/properties.construction_year')) !!}
							{!! Form::text('construction_year', null, [ 'class'=>'form-control digits'  ]) !!}
						</div>
					</div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[property_condition]', Lang::get('account/properties.property_condition')) !!}
				            {!! Form::select('details[property_condition]', [ '' => '',
				                'excelent'=>Lang::get('account/properties.condition.excelent'),
				                'very_good'=>Lang::get('account/properties.condition.very_good'),
				                'good'=>Lang::get('account/properties.condition.good'),
				                'modderate'=>Lang::get('account/properties.condition.modderate'),
				                'poor'=>Lang::get('account/properties.condition.poor'),
				            ], null, [ 'class'=>'form-control' ]) !!}
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[building_condition]', Lang::get('account/properties.building_condition')) !!}
				            {!! Form::select('details[building_condition]', [ '' => '',
				                'excelent'=>Lang::get('account/properties.condition.excelent'),
				                'very_good'=>Lang::get('account/properties.condition.very_good'),
				                'good'=>Lang::get('account/properties.condition.good'),
				                'modderate'=>Lang::get('account/properties.condition.modderate'),
				                'poor'=>Lang::get('account/properties.condition.poor'),
				            ], null, [ 'class'=>'form-control' ]) !!}
				        </div>
				    </div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[professional_enabled]', Lang::get('account/properties.professional_enabled')) !!}
				            {!! Form::select('details[professional_enabled]', [ '' => '','0'=>Lang::get('general.no'), '1'=>Lang::get('general.yes') ], null, [ 'class'=>'form-control' ]) !!}
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[commercial_enabled]', Lang::get('account/properties.commercial_enabled')) !!}
				            {!! Form::select('details[commercial_enabled]', [ '' => '','0'=>Lang::get('general.no'), '1'=>Lang::get('general.yes') ], null, [ 'class'=>'form-control' ]) !!}
				        </div>
				    </div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[property_disposal]', Lang::get('account/properties.property_disposal')) !!}
				            {!! Form::select('details[property_disposal]', [ '' => '', 'front'=>Lang::get('account/properties.property_disposal.front'), 'back'=>Lang::get('account/properties.property_disposal.back'), 'internal' => Lang::get('account/properties.property_disposal.internal') ], null, [ 'class'=>'form-control' ]) !!}
				        </div>
				    </div>
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::label('enabled', Lang::get('account/properties.enabled')) !!}
							@if ( $current_site->property_limit_remaining > 0 || ($item && $current_site->property_limit_remaining >= 0) )
								{!! Form::select('enabled', [
									1 => Lang::get('general.yes'),
									0 => Lang::get('general.no'),
								 ], null, [ 'class'=>'form-control' ]) !!}
							@else
								{!! Form::select('enabled', [
									0 => Lang::get('general.no'),
								 ], null, [ 'class'=>'form-control' ]) !!}
								<div class="help-block">{!! Lang::get('account/warning.properties.helper', [ 'max_properties' => number_format(App\Session\Site::get('plan.max_properties'),0,',','.'), ]) !!}</div>
							@endif
						</div>
					</div>
				</div>

				<hr />
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="form-group error-container">
							{!! Form::hidden('size_unit', 'sqm') !!}
							{!! Form::label('size', Lang::get('account/properties.size').' *') !!}
							<div class="input-group">
								{!! Form::text('size', null, [ 'class'=>'form-control required number', 'min'=>'0' ]) !!}
								<div class="input-group-addon">m²</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[balcony_area]', Lang::get('account/properties.balcony_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[balcony_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[covered_area]', Lang::get('account/properties.covered_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[covered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[semi_covered_area]', Lang::get('account/properties.semi_covered_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[semi_covered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[uncovered_area]', Lang::get('account/properties.uncovered_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[uncovered_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[lot_area]', Lang::get('account/properties.lot_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[lot_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[buildable_area]', Lang::get('account/properties.buildable_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[buildable_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[basement_area]', Lang::get('account/properties.basement_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[basement_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				</div>
				<div class="row">
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[mezzanine_area]', Lang::get('account/properties.mezzanine_area')) !!}
				            <div class="input-group">
				                {!! Form::text('details[mezzanine_area]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
				        </div>
				    </div>
				    <div class="col-xs-12 col-sm-6">
				        <div class="form-group error-container">
				            {!! Form::label('details[size_real]', Lang::get('account/properties.size_real')) !!}
				            <div class="input-group">
				                {!! Form::text('details[size_real]', null, [ 'class'=>'form-control number', 'min'=>'0' ]) !!}
				                <div class="input-group-addon">m²</div>
				            </div>
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
									{!! Form::checkbox('home_slider', 1, null) !!}
									{{ Lang::get('account/properties.home.slider') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('highlighted', 1, null) !!}
									{{ Lang::get('account/properties.highlighted') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('new_item', 1, null) !!}
									{{ Lang::get('account/properties.new.item') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('opportunity', 1, null) !!}
									{{ Lang::get('account/properties.opportunity') }}
								</label>
							</div>
						</div>
					</div>
				</div>
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
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('bank_owned', 1, null) !!}
									{{ Lang::get('account/properties.bank_owned') }}
								</label>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-3">
						<div class="form-group">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('private_owned', 1, null) !!}
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
										{!! Form::checkbox('services[]', $service->id, $item ? $item->hasService($service->id) : null ) !!}
										{{ $service->title }}
									</label>
								</div>
							</div>
						</div>
					@endforeach
				</div>
				<hr />
				<div class="row">
					<div class="col-xs-12 col-sm-12">
				        <div class="form-group error-container">
				            {!! Form::label('url_3d', Lang::get('account/properties.url.3d.title')) !!}
				            <div class="">
				                {!! Form::text('url_3d', null, [ 'class'=>'form-control url' ]) !!}
				            </div>
				        </div>
				    </div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'location' ? 'active' : '' }}" id="tab-location">
				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('country_id', Lang::get('account/properties.country').' *') !!}
							{!! Form::select('country_id', $countries, @$country_id, [ 'class'=>'form-control required country-input', 'data-rel'=>'.state-input, .city-input', 'data-target'=>'.state-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'state') ]) !!}
						</div>
						<div class="form-group error-container">
							<?php $tmp = empty($states) ? [ ''=>'' ] : [ ''=>'' ] + $states->toArray(); ?>
							{!! Form::label('state_id', Lang::get('account/properties.state').' *') !!}
							{!! Form::select('state_id', $tmp, null, [ 'class'=>'form-control required state-input', 'data-rel'=>'.city-input', 'data-target'=>'.city-input', 'data-action'=>action('Ajax\GeographyController@getSuggest', 'city') ]) !!}
						</div>
						<div class="form-group error-container">
							<?php $tmp = empty($cities) ? [ ''=>'' ] : [ ''=>'' ] + $cities->toArray(); ?>
							{!! Form::label('city_id', Lang::get('account/properties.city').' *') !!}
							{!! Form::select('city_id', $tmp, null, [ 'class'=>'form-control required city-input' ]) !!}
						</div>
						<div class="form-group error-container">
							<a href="#add-district" class="add-district-trigger btn btn-default btn-xs pull-right"
							   title="{{ Lang::get('account/properties.districts.create') }}">+</a>

							{!! Form::label('district_id', Lang::get('account/properties.district')) !!}

							<div id="district-select-container">
								<?php $tmp = empty($districts) ? [ ''=>'' ] : [ ''=>'' ] + $districts->toArray(); ?>
								{!! Form::select('district_id', $tmp, @$item->district_id, [ 'class'=>'form-control district-input' ]) !!}
							</div>

							<div id="district-input-container" style="display: none;">
								{!! Form::text('district', null, [ 'class'=>'form-control district-input' ]) !!}
							</div>

							{!! Form::hidden('new_district', false) !!}

						</div>
						@include('account/properties/form-address')
					</div>
					<div class="col-xs-12 col-sm-8">
						<div id="property-map" style="height: 400px; margin-bottom: 10px;"></div>
						<div class="row">
							<div class="col-xs-12 col-sm-4">
								<div class="form-group error-container">
									{!! Form::label('lat', Lang::get('account/properties.lat').' *', [ 'class'=>'normal' ]) !!}
									{!! Form::text('lat', null, [ 'class'=>'form-control required number input-lat', 'readonly'=>'readonly' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<div class="form-group error-container">
									{!! Form::label('lng', Lang::get('account/properties.lng').' *', [ 'class'=>'normal' ]) !!}
									{!! Form::text('lng', null, [ 'class'=>'form-control required number input-lng', 'readonly'=>'readonly' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-4">
								<label>&nbsp;</label>
								<div>
									{!! Form::button(Lang::get('account/properties.geolocate'), [ 'class'=>'btn btn-block btn-default geolocate-trigger' ]) !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'text' ? 'active' : '' }}" id="tab-text">
				<ul class="nav nav-tabs locale-tabs" role="tablist">
					@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
						<li role="presentation" class="autotranslate-flag-area">
							<a href="#lang-{{$lang_iso}}" aria-controls="lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a>
							<i class="fa fa-check autotranslate-flag" aria-hidden="true"></i>
						</li>
					@endforeach
				</ul>
				<div class="tab-content translate-area">
					@foreach ($site_setup['locales_tabs'] as $lang_iso => $lang_name)
						<div role="tabpanel" class="tab-pane tab-locale" id="lang-{{$lang_iso}}">
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('account/properties.title').(($lang_iso == fallback_lang()) ? ' *' : '')) !!}
										<div class="error-container">
											{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control title-input '.(($lang_iso == fallback_lang()) ? 'required' : ''), 'lang'=>$lang_iso, 'dir'=>lang_dir($lang_iso) ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".title-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
									<div class="form-group">
										<div class="pull-right color-select-area">
											{!! Form::text(null, null, [ 'class'=>'label-color-input' ]) !!}
										</div>
										{!! Form::label("i18n[label][{$lang_iso}]", Lang::get('account/properties.label')) !!}
										<div class="error-container">
											{!! Form::text("i18n[label][{$lang_iso}]", null, [ 'class'=>'form-control label-input', 'lang'=>$lang_iso, 'dir'=>lang_dir($lang_iso) ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".label-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
									<div class="autotranslate-credit">
										<a href="http://aka.ms/MicrosoftTranslatorAttribution" target="_blank">{{ Lang::get('general.autotranslate.credits') }} <img src="{{ asset('images/autotranslate/microsoft.png') }}" alt="Microsoft" class="credited"></a>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										{!! Form::label("i18n[description][{$lang_iso}]", Lang::get('account/properties.description')) !!}
										<div class="error-container">
											{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control resize-vertical description-input', 'lang'=>$lang_iso, 'rows'=>'4', 'dir'=>lang_dir($lang_iso) ]) !!}
										</div>
										<div class="help-block text-right">
											<a href="#" class="translate-trigger" data-input=".description-input" data-lang="{{$lang_iso}}">{{ Lang::get('general.autotranslate.trigger') }}</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'images' ? 'active' : '' }}" id="tab-images">
				<div class="row">
					<div class="col-xs-12 col-sm-7">
						<h4>{{ Lang::get('account/properties.images.gallery') }}</h4>
						<hr>
						<div class="alert alert-info images-empty">
							{{ Lang::get('account/properties.images.empty') }}
						</div>
						<div class="alert alert-warning images-warning-size hide">
							<strong>{{ Lang::get('web/properties.images.label.default') }}</strong><br />
							{{ Lang::get('web/properties.images.warning.size') }}
						</div>
						<div class="alert alert-danger images-warning-orientation hide">
							<strong>{{ Lang::get('web/properties.images.label.default') }}</strong><br />
							{{ Lang::get('web/properties.images.warning.orientation') }}
						</div>

						<ul class="image-gallery sortable-image-gallery property-image-gallery">
							@if ( $item && $item->images->count() > 0 )
								@foreach ($item->images->sortBy('position') as $image)
									@include('account.properties.form-image-thumb',[
										'image_url' => $image->image_url,
										'image_id' => $image->id,
										'warning_orientation' => $image->is_vertical,
										'warning_size' => $image->has_size ? 0 : 1,
									])
								@endforeach
							@endif
						</ul>
						<div class="form-group error-container">
							<input type="hidden" name="total_images" value="{{ $item ? $item->images->count() : 0 }}" class="required digits" min="1" />
						</div>
						<div class="visible-xs-block">
							<p>&nbsp;</p>
						</div>
					</div>
					<div class="col-xs-12 col-sm-5">
						<h4>{{ Lang::get('account/properties.images.upload') }}</h4>
						<hr>
						<div class="dropzone-previews" id="dropzone-previews"></div>
						<div class="help-block">{{ Lang::get('account/properties.images.dropzone.helper') }}</div>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'videos' ? 'active' : '' }}" id="tab-videos">
				<div class="row">
					<div class="col-xs-12 col-sm-7">
						<h4>{{ Lang::get('account/properties.video.preview') }}</h4>
						<hr>

						@if(!isset($property) || count($property->videos) <= 0)
							<div class="alert alert-info video-empty">
								{{ Lang::get('account/properties.video.empty') }}
							</div>
						@else
							<div class="alert alert-info video-empty" style="display: none">
								{{ Lang::get('account/properties.video.empty') }}
							</div>

							<ul class="video-gallery sortable-video-gallery">
								@foreach ($property->videos->sortBy('position_video') as $video)
									@include('account.properties.form-video-thumb',[
										'video' => $video,
										'property_id' => $property->id,
										'isCreate' => $isCreate
									])
								@endforeach
							</ul>
						@endif
					</div>
					<div class="col-xs-12 col-sm-5">
						<h4>{{ Lang::get('account/properties.video.title') }}</h4>
						<hr>
						<div class="form-group error-container">
							{!! Form::label('video_link', Lang::get('account/properties.video_link')) !!}
							{!! Form::text('video_link', null, [ 'class'=>'form-control', 'placeholder' => 'https://www.youtube.com/watch?v=xxxxxxxx' ]) !!}
							<div class="help-block">
								<label>{{ Lang::get('account/properties.video.help') }}</label>
							</div>
						</div>
					</div>
				</div>
			</div>


			@if ( $item && !$isCreate)
				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'employees' ? 'active' : '' }}" id="tab-employees">
					@include('account.properties.tab-managers', [
						'item' => $item,
						'employees' => $item->users()->withRole('employee')->get(),
					])
				</div>

				@if ( $marketplaces->count() > 0 )
					<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'marketplaces' ? 'active' : '' }}" id="tab-marketplaces">
						@include('account/properties/form-marketplaces')
					</div>
				@endif

				<div role="tabpanel" class="tab-pane tab-main {{$current_tab == 'visits' ? 'active' : '' }}" id="tab-visits">
					@include('account.visits.ajax-tab', [
						'visits_init' => true,
					])
				</div>

			@else
				<div role="tabpanel" class="tab-pane tab-main {{ $current_tab == 'seller' ? 'active' : '' }}" id="tab-seller">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('employee_id', Lang::get('account/properties.show.property.catch.employee') ) !!}
								{!! Form::select('employee_id', [''=>'&nbsp;']+$managers, Auth::user()->id, [ 'class'=>'has-select-2 form-control required', ]) !!}
							</div>
						</div>
					</div>
					<hr />
					@include('account.properties.catch-form', [
						'item' => isset($item->catches) ? $item->catches->first() : null,
						'price_symbol' => $current_site->infocurrency->symbol,
						'price_position' => $current_site->infocurrency->position,
					])
				</div>
			@endif

		</div>

		<br />

		<div class="text-right">
			{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

		<br />

	</div>

{!! Form::close() !!}

<script type="text/html" id="image_upload_item_tmpl">
	<li>
		<input class="" name="new_images[<%=id%>]" type="file" accept="image/*" />
		<hr>
	</li>
</script>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#edit-form');

		var property_map;
		var property_marker;
		var property_geocoder;
		var property_zoom = {{ $item ? '14' : '6' }};

		var total_images_input = form.find('input[name="total_images"]');

		// Enable first language tab
		form.find('.locale-tabs a').eq(0).trigger('click');

		form.find('.add-district-trigger').on('click', function(e){
			$('#district-select-container').slideToggle();
			$('#district-input-container').slideToggle();

			var $hidden = form.find('[name="new_district"]');
			var val = $hidden.val();
			$hidden.val(val === "true" ? "false" : "true");
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
			messages:
			{
				total_images: {
					required: "{{ print_js_string( Lang::get('account/properties.images.empty.error') ) }}",
					digits: "{{ print_js_string( Lang::get('account/properties.images.empty.error') ) }}",
					min: "{{ print_js_string( Lang::get('account/properties.images.empty.error') ) }}"
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		property_geocoder = new google.maps.Geocoder();

		// Discount
		form.find('[name="price"],[name="price_before"]').keyup(function(){
			var price = form.find('[name="price"]').val();
			if (isNaN(price)) price = 0;
			var price_before = form.find('[name="price_before"]').val();
			if (isNaN(price_before)) price_before = 0;
			var discount  = 0;
			if (price_before > 0)  discount = (price_before - price) * 100 / price_before;
			if (isNaN(discount)) discount = 0;

			form.find('[name="discount"]').val(Math.ceil(discount) * -1);
		}).keyup();

		// Enable map when opening tab
		form.find('.main-tabs a[href="#tab-location"]').on('shown.bs.tab', function (e) {
			var el = $(e.target);

			if ( el.hasClass('property-map-initialized') ) {
				return;
			}

			el.addClass('property-map-initialized');

			var lat = form.find('.input-lat').val();
			var lng = form.find('.input-lng').val();

			if ( !lat || !lng ) {
				lat = '40.4636670';
				lng = '-3.7492200';
			}

			var mapLatLng = { lat: parseFloat(lat), lng: parseFloat(lng) };

			property_map = new google.maps.Map(document.getElementById('property-map'), {
				zoom: property_zoom,
				center: mapLatLng
			});

			property_marker = new google.maps.Marker({
				position: mapLatLng,
				map: property_map,
				draggable: true
			});

			property_marker.addListener('dragend',function(event) {
				form.find('.input-lat').val( event.latLng.lat() );
				form.find('.input-lng').val( event.latLng.lng() );
			});
		});

		// Geolocation
		form.on('click', '.geolocate-trigger', function(e){
			var address = [];
			var error = false;

			if ( form.find('.address-input').val() ) {
				address.push( form.find('.address-input').val() );
			}

			if ( form.find('input[name="zipcode"]').val() ) {
				address.push( form.find('input[name="zipcode"]').val() );
			}

			$.each(['city','state','country'], function(k,v){
				var input = form.find('.'+v+'-input');
				if ( input.valid() ) {
					if ( input.prop('tagName').toLowerCase() == 'select' ) {
						address.push( input.find('option:selected').text() );
					} else {
						address.push( input.val() );
					}
				} else {
					error = true;
				}
			});

			if ( error ) {
				alertify.error("{{ print_js_string( Lang::get('account/properties.geolocate.missing') ) }}");
				return false;
			}

			LOADING.show();
			property_geocoder.geocode({
				'address': address.join(', ')
			}, function(results, status) {
				LOADING.hide();
				if (status === google.maps.GeocoderStatus.OK) {
					property_map.setCenter( results[0].geometry.location );
					property_marker.setPosition( results[0].geometry.location );
					form.find('.input-lat').val( results[0].geometry.location.lat() );
					form.find('.input-lng').val( results[0].geometry.location.lng() );
				} else {
					var message = "{{ print_js_string( Lang::get('account/properties.geolocate.error') ) }}: "+status;
					switch (status) {
						case 'ZERO_RESULTS':
							message = "{{ print_js_string( Lang::get('account/properties.geolocate.no_results') ) }}";
							break;
					}
					alertify.error(message);
				}
			});
		});

		// Location selectors
		form.on('change', '.country-input, .state-input', function(){
			var el = $(this);

			form.find( el.data().rel ).html('<option value=""></option>');

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
						var target = form.find( el.data().target );
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

		// Automatic location
		form.on('change', '.city-input', function(){
			if ( $(this).val() ) {
				property_map.setZoom(13);
				form.find('.geolocate-trigger').trigger('click');
			}
		});

		// Image gallery
		form.find('.image-gallery').sortable({
			stop: initImageWarnings
		});
		form.find('.image-gallery .thumb').each(function(){
			$(this).magnificPopup({
				type: 'image',
				closeOnContentClick: false,
				mainClass: 'mfp-img-mobile',
				image: {
					verticalFit: true
				}
			});
		});
		form.on('click', '.image-delete-trigger', function(e){
			var el = $(this);
			e.preventDefault();
			SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.images.delete') ) }}", function (e) {
				if (e) {
					el.closest('.handler').remove();
					initImageWarnings();
				}
			});
		});

		// Video gallery
		//form.find('.video-gallery').sortable();

		form.find('.video-gallery .thumb').each(function(){
			var link = $(this).data('link');

			$(this).magnificPopup({
				items: { src: link },
				type: 'iframe',
				mainClass: 'mfp-img-mobile',
				closeOnContentClick: false,
			});
		});

		form.on('click', '.video-delete-trigger', function(e){
			var el = $(this);
			e.preventDefault();

			SITECOMMON.confirm("{{ print_js_string( Lang::get('account/properties.video.delete') ) }}", function (e) {
				if (e) {
					el.closest('.handler').remove();
					initVideoWarnings();
				}
			});
		});

		form.on('click', '.image-rotate-trigger', function(e){
			e.preventDefault();

			var el = $(this);
			var thumb = el.closest('.handler').find('.thumb');
			var input = el.parent().find('.rotation-hidden-input');
			var degree = input.val();

			if(!degree) {
				thumb.addClass('rotated-90');
				input.val('90');
			} else if(degree == '90') {
				thumb.removeClass('rotated-90');
				thumb.addClass('rotated-180');
				input.val('180');
			} else if(degree == '180') {
				thumb.removeClass('rotated-180');
				thumb.addClass('rotated-270');
				input.val('270');
			} else if(degree == '270') {
				thumb.removeClass('rotated-270');
				input.val('');
			}
		});

		// Translations
		var translation_flag_fields = '.title-input, .description-input';

		function checkTranslations() {
			form.find('.autotranslate-flag-area').each(function(){
				var el = $(this).addClass('autotranslate-complete');
				var target = $( $(this).find('a').attr('href') );

				var completed = true;

				target.find(translation_flag_fields).each(function() {
					if ( ! $(this).val() ) {
						el.removeClass('autotranslate-complete')
						return false;
					}
				});

			});
		}

		form.on('change', translation_flag_fields, checkTranslations);

		checkTranslations();

		form.on('click', '.translate-trigger', function(e){
			e.preventDefault();

			var el = $(this);
			var group = el.closest('.translate-area');
			var items = group.find( el.data().input );
			var from = $(this).data().lang;
			var text = items.filter('[lang="'+from+'"]').val();
			var languages = {!! json_encode(LaravelLocalization::getSupportedLocales()) !!};

			// No text to translate from
			if (!text) {
				alertify.error("{{ print_js_string( Lang::get('general.autotranslate.error.text') ) }}");
				return false;
			}

			// Show loader
			LOADING.show();

			// Get translation languages
			var to = [];
			items.each(function(){
				to.push( $(this).attr('lang') );
			});

			// Get translations
			$.ajax({
				url: '{{ action('Ajax\AutotranslateController@getIndex') }}',
				dataType: 'json',
				data: {
					text: text,
					from: from,
					to: to
				},
				success: function(data) {
					LOADING.hide();
					if (data.success) {
						var errors = [];
						$.each(data.translations, function(iso,txt){
							if (txt) {
								items.filter('[lang="'+iso+'"]').val(txt);
							} else {
								errors.push(languages[iso].native);
							}
						});
						// Error (no translations, except from language)
						if ( errors.length+1 == to.length ) {
							alertify.error("{{ print_js_string( Lang::get('general.autotranslate.error.all') ) }}");
						// Success
						} else {
							var msg = "{{ print_js_string( Lang::get('general.autotranslate.success') ) }}";
							// Some errors
							if ( errors.length > 0 ) {
								msg += "<br />{{ print_js_string( Lang::get('general.autotranslate.error.some') ) }} " + errors.join(', ') + '.';
							}
							alertify.success(msg);
							checkTranslations();
						}
					} else {
						alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
					}
				},
				error: function() {
					LOADING.hide();
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}
			});
		});

		// Hide country_id select if only one country
		if ( form.find('select[name="country_id"] option').length < 2 ) {
			form.find('select[name="country_id"] option').closest('.form-group').addClass('hide');
		}

		<?php
			$i = 0;
			$label_default = false;
			$label_palette = "[ [";
			$theme_palete = Theme::config('label-palette');
			if (!is_array($theme_palete)) $theme_palete = [];
			foreach ($theme_palete as $color)
			{
				if ( !$label_default )
				{
					$label_default = $color;
				}

				if ( !$i )
				{
					$label_palette .= " '{$color}'";
				}
				else if ( $i%5 == 0 )
				{
					$label_palette .= " ], [ '{$color}'";
				}
				else
				{
					$label_palette .= ", '{$color}'";
				}

				$i++;
			}
			$label_palette .= " ] ]";
		?>
		// Label color picker
		form.find('.label-color-input').each(function(){
			var el = $(this);
			var target = form.find('input[name="label_color"]');

			if ( !target.val() ) {
				target.val('{!! $label_default !!}');
			}

			el.spectrum({
				showPaletteOnly: true,
				showPalette: true,
				color: target.val(),
				palette: {!! $label_palette !!},
				move: function(color) {
				    el.spectrum('toggle');
					target.val( color.toHexString() );
					form.find('.label-color-input').spectrum("set", color);
				}
			});

		});

		// Drop zone
		Dropzone.autoDiscover = false;
		$("#dropzone-previews").addClass('dropzone').dropzone({
			url: '{{ action('Account\PropertiesController@postUpload') }}',
			params: {
				_token: '{{ Session::getToken() }}'
			},
			maxFilesize: {{ Config::get('app.property_image_maxsize') / 1024 }},
			acceptedFiles: 'image/*',
			dictFileTooBig: "{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize') ]) ) }}",
			dictDefaultMessage: "{{ print_js_string( Lang::get('account/properties.images.dropzone.helper') ) }}",
			error: function(file, response) {
				if ( $.type(response) === 'string') {
					if ( response.length > 500 ) {
						alertify.error("{{ print_js_string( Lang::get('account/properties.images.dropzone.error.size', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize') ]) ) }}");
					} else {
						alertify.error(response);
					}
				} else if ( $.type(response) === 'object' && response.message ) {
					alertify.error(response.message);
				} else {
					alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
				}

				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});
			},
			canceled: function(file) {
				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});
			},
			success: function(file,response) {
				var item = $(response.html);

				item.find('.thumb').magnificPopup({
					type: 'image',
					closeOnContentClick: false,
					mainClass: 'mfp-img-mobile',
					image: {
						verticalFit: true
					}
				});

				form.find('.image-gallery').append(item);

				$(file.previewElement).fadeOut(function(){
					$(this).remove()
				});

				initImageTooltips();
				initImageWarnings();
			}
		});

		form.on('change', 'select[name="export_to_all"]', function(){
			if ( $(this).val() == '1' ) {
				form.find('.marketplace-input').prop('checked',true).prop('disabled', true);
			} else {
				form.find('.marketplace-input').prop('disabled', false);
				form.find('.marketplace-input-unpublished').prop('checked',false);
			}
		});

		form.find('.has-select-2').select2();

		form.find('.main-tabs > li > a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			form.find('input[name="current_tab"]').val( $(this).data().tab );
			form.find('.has-select-2').select2();
		});

		@if ( $item )
			if ( form.find('input[name="current_tab"]').val() == 'location' ) {
				form.find('.main-tabs a[href="#tab-location"]').trigger('shown.bs.tab');
			}
			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: '{{ action('Account\Visits\AjaxController@getTab') }}',
				data: {
					property_id: {{ $item->id }}
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
		@else
			property_geocoder.geocode({
				'address': form.find('.country-input option:selected').text()
			}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					form.find('.input-lat').val( results[0].geometry.location.lat() );
					form.find('.input-lng').val( results[0].geometry.location.lng() );
					if ( form.find('input[name="current_tab"]').val() == 'location' ) {
						form.find('.main-tabs a[href="#tab-location"]').trigger('shown.bs.tab');
					}
				} else {
					if ( form.find('input[name="current_tab"]').val() == 'location' ) {
						form.find('.main-tabs a[href="#tab-location"]').trigger('shown.bs.tab');
					}
				}
			});
		@endif

		function initImageWarnings() {
			form.find('.images-warning-size, .images-warning-orientation').addClass('hide');

			total_images_input.val( form.find('.image-gallery .thumb').length );

			if ( total_images_input.val() < 1 ) {
				form.find('.images-empty').show();
			} else {
				form.find('.images-empty').hide();
				var fh = form.find('.property-image-gallery li.handler:first-child');
				if ( fh.hasClass('handler-orange') ) {
					form.find('.images-warning-size').removeClass('hide');
				} else if ( fh.hasClass('handler-red') ) {
					form.find('.images-warning-orientation').removeClass('hide');
				}
			}

			total_images_input.valid();
		}


		function initVideoWarnings() {
			if ( form.find('.video-gallery .thumb').length < 1 ) {
				form.find('.video-empty').show();
			} else {
				form.find('.video-empty').hide();
			}
		}

		function initImageTooltips() {
			form.find('.thumb-has-tooltip').removeClass('thumb-has-tooltip').tooltip();
		}
		initImageWarnings();
		initImageTooltips();

	});
</script>

<!-- Includes for the HTML Editor -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
<!-- Includes for the HTML Editor -->

<script>
    $(document).ready(function() {
        $('.summernote').summernote(
            {
                height: 200,   //set editable area's height
                codemirror: { // codemirror options
                    theme: 'monokai'
                }
            }
        );

    });
</script>