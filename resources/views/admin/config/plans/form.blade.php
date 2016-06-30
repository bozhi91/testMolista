{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'edit-form' ]) !!}
	{!! Form::hidden('enabled', 1) !!}

	<div class="row">
		<div class="col-xs-12 col-sm-2">
			<div class="form-group error-container">
				{!! Form::label('code', Lang::get('admin/config/plans.code')) !!}
				@if ( empty($item->code) )
					{!! Form::text('code',null, [ 'class'=>'form-control required' ]) !!}
				@else
					{!! Form::text(null, $item->code, [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
				@endif
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-sm-offset-1">
			<div class="form-group error-container">
				{!! Form::label('name', Lang::get('admin/config/plans.name')) !!}
				{!! Form::text('name',null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				<label>&nbsp;</label>
				<div class="checkbox">
					<label>
						{!! Form::checkbox('is_free',1,null) !!}
						{{ Lang::get('admin/config/plans.free.plan') }}
					</label> 
				</div>
			</div>
		</div>
	</div>

	<div class="price-area" {{ @$item->is_free ? 'style="display:none;"' : '' }}>
		<hr />
		<div class="row">
			<div class="col-xs-12 col-sm-2">
				<div class="form-group error-container">
					{!! Form::label('price_year', Lang::get('admin/config/plans.price.year')) !!}
					<div class="input-group">
						{!! Form::text('price_year', null, [ 'class'=>'price-input form-control number required', 'min'=>0 ]) !!}
						<div class="input-group-addon">{{ price_symbol('EUR') }}</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-2">
				<div class="form-group error-container">
					{!! Form::label('stripe_year_id', Lang::get('admin/config/plans.stripe.id')) !!}
					{!! Form::text('stripe_year_id', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
			<div class="col-xs-12 col-sm-2 col-sm-offset-2">
				<div class="form-group error-container">
					{!! Form::label('price_month', Lang::get('admin/config/plans.price.month')) !!}
					<div class="input-group">
						{!! Form::text('price_month', null, [ 'class'=>'price-input form-control number required', 'min'=>0 ]) !!}
						<div class="input-group-addon">{{ price_symbol('EUR') }}</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-2">
				<div class="form-group error-container">
					{!! Form::label('stripe_month_id', Lang::get('admin/config/plans.stripe.id')) !!}
					{!! Form::text('stripe_month_id', null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[support_email]', Lang::get('admin/config/plans.support.email')) !!}
					{!! Form::select('configuration[support_email]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[support_phone]', Lang::get('admin/config/plans.support.phone')) !!}
					{!! Form::select('configuration[support_phone]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('max_employees', Lang::get('admin/config/plans.employees')) !!}
					{!! Form::text('max_employees', null, [ 'class'=>'form-control digits' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::label('max_properties', Lang::get('admin/config/plans.properties')) !!}
				{!! Form::text('max_properties', null, [ 'class'=>'form-control digits' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::label('max_languages', Lang::get('admin/config/plans.languages')) !!}
				{!! Form::text('max_languages', null, [ 'class'=>'form-control digits' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::label('max_space', Lang::get('admin/config/plans.space')) !!}
				<div class="input-group">
					{!! Form::text('max_space', null, [ 'class'=>'form-control digits' ]) !!}
					<div class="input-group-addon">GB</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[qr]', Lang::get('admin/config/plans.qr')) !!}
					{!! Form::select('configuration[qr]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[printing]', Lang::get('admin/config/plans.printing')) !!}
					{!! Form::select('configuration[printing]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[integrations]', Lang::get('admin/config/plans.integrations')) !!}
					{!! Form::select('configuration[integrations]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[reporting]', Lang::get('admin/config/plans.reporting')) !!}
					{!! Form::select('configuration[reporting]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[analytics]', Lang::get('admin/config/plans.analytics')) !!}
					{!! Form::select('configuration[analytics]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[responsive]', Lang::get('admin/config/plans.responsive')) !!}
					{!! Form::select('configuration[responsive]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[filters]', Lang::get('admin/config/plans.properties.filters')) !!}
					{!! Form::select('configuration[filters]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[leads]', Lang::get('admin/config/plans.leads')) !!}
					{!! Form::select('configuration[leads]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[crm]', Lang::get('admin/config/plans.crm')) !!}
					{!! Form::select('configuration[crm]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[logs]', Lang::get('admin/config/plans.logs')) !!}
					{!! Form::select('configuration[logs]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-3">
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('configuration[widgets]', Lang::get('admin/config/plans.widgets')) !!}
					{!! Form::select('configuration[widgets]', [
						1 => Lang::get('general.yes'),
						0 => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="form-group error-container">
				{!! Form::label('extras[transfer]', Lang::get('admin/config/plans.extras.transfer')) !!}
				<div class="input-group">
					{!! Form::text('extras[transfer]', null, [ 'class'=>'form-control number' ]) !!}
					<div class="input-group-addon">{{ price_symbol('EUR') }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="text-right">
		{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
	</div>

{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#edit-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			rules: {
				code: {
					remote: {
						url: '{{ action('Admin\Config\PlansController@getCheck', 'code') }}',
						type: 'get',
						data: {
							exclude: {{ empty($item->id) ? '0' : $item->id }}
						}
					}
				},
				stripe_year_id: {
					required: function() {
						return form.find('input[name="is_free"]').is(':checked') ? false : true;
					}
				},
				stripe_month_id: {
					required: function() {
						return form.find('input[name="is_free"]').is(':checked') ? false : true;
					}
				}
			},
			messages: {
				code: {
					remote: "{{ trim( Lang::get('admin/config/plans.code.used') ) }}"
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		form.on('change','input[name="is_free"]', function(){
			if ( $(this).is(':checked') ) {
				form.find('.price-area').hide().find('.price-input').val(0);
			} else {
				form.find('.price-area').slideDown();
			}
		});
		form.find('input[name="is_free"]').trigger('change');

	});
</script>