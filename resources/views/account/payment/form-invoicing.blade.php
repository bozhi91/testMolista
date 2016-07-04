<?php
	if ( !isset($countries) || !is_array($countries) )
	{
		$countries = \App\Models\Geography\Country::withTranslations()->orderBy('name')->lists('name','id')->all();
	}
?>

<div id="invoicing-form-area">
	<div class="form-group error-container">
		<div class="form-inline inline-checkboxes">
			<div class="checkbox">
				<label>
					{!! Form::radio('invoicing[type]', 'individual', null, [ 'class'=>'required' ]) !!}
					<strong>{{ Lang::get('corporate/signup.invoicing.type.individual') }}</strong>
				</label>
			</div>
			<div class="checkbox">
				<label>
					{!! Form::radio('invoicing[type]', 'company', null, [ 'class'=>'required' ]) !!}
					<strong>{{ Lang::get('corporate/signup.invoicing.type.company') }}</strong>
				</label>
			</div>
		</div>
	</div>
	<div class="invoicing-type-rel invoicing-type-company {{ (@$invoicing_type == 'company') ? '' : 'hide' }}">
		<div class="row">
			<div class="col-xs-12">
				<div class="form-group error-container">
					{!! Form::label('invoicing[company]', Lang::get('corporate/signup.invoicing.company'), [ 'class'=>'input-label' ]) !!}
					{!! Form::text('invoicing[company]', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[first_name]', Lang::get('corporate/signup.invoicing.first_name'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[first_name]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[last_name]', Lang::get('corporate/signup.invoicing.last_name'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[last_name]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[email]', Lang::get('corporate/signup.invoicing.email'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[email]', null, [ 'class'=>'form-control required email' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[tax_id]', Lang::get('corporate/signup.invoicing.tax_id'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[tax_id]', null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
	</div>
	<div class="address-title">{{ Lang::get('corporate/signup.invoicing.address') }}</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[street]', Lang::get('corporate/signup.invoicing.street'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[street]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[zipcode]', Lang::get('corporate/signup.invoicing.zipcode'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[zipcode]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[city]', Lang::get('corporate/signup.invoicing.city'), [ 'class'=>'input-label' ]) !!}
				{!! Form::text('invoicing[city]', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('invoicing[country_id]', Lang::get('corporate/signup.invoicing.country'), [ 'class'=>'input-label' ]) !!}
				{!! Form::select('invoicing[country_id]', [ ''=>'' ]+$countries, null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function() {
		var cont = $('#invoicing-form-area');

		cont.on('click','input[name="invoicing[type]"]',function(e){
			var t = cont.find('input[name="invoicing[type]"]:checked').val();
			cont.find('.invoicing-type-rel').addClass('hide')
				.filter('.invoicing-type-'+t).removeClass('hide');
		});
	});
</script>
