<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_first_name', Lang::get('account/properties.show.property.seller.name.first').' *' ) !!}
			{!! Form::text('seller_first_name', @$item->seller_first_name, [ 'class'=>'form-control required', ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_last_name', Lang::get('account/properties.show.property.seller.name.last').' *' ) !!}
			{!! Form::text('seller_last_name', @$item->seller_last_name, [ 'class'=>'form-control required', ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_email', Lang::get('account/properties.show.property.seller.email').' *' ) !!}
			{!! Form::text('seller_email', @$item->seller_email, [ 'class'=>'form-control email required', ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_id_card', Lang::get('account/properties.show.property.seller.id') ) !!}
			{!! Form::text('seller_id_card', @$item->seller_id_card, [ 'class'=>'form-control', ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_phone', Lang::get('account/properties.show.property.seller.phone') ) !!}
			{!! Form::text('seller_phone', @$item->seller_phone, [ 'class'=>'form-control', ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('seller_cell', Lang::get('account/properties.show.property.seller.cell') ) !!}
			{!! Form::text('seller_cell', @$item->seller_cell, [ 'class'=>'form-control', ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-placement">
			{!! Form::label('company_name', Lang::get('account/properties.show.property.seller.company_name') ) !!}
			{!! Form::text('company_name', $item->company_name, [ 'class'=>'form-control']) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-placement">
			{!! Form::label('cif', Lang::get('account/properties.show.property.seller.cif') ) !!}
			{!! Form::text('cif', $item->cif, [ 'class'=>'form-control']) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-3">
		<div class="form-group">
			<div class="error-container">
				{!! Form::label('price_min', Lang::get('account/properties.show.property.price.min').' *' ) !!}
				<div class="input-group">
					@if ( @$price_position == 'before' )
						<div class="input-group-addon">{{ @$price_symbol }}</div>
					@endif
					{!! Form::text('price_min', @$item->price_min, [ 'class'=>'form-control required number', 'min'=>1 ]) !!}
					@if ( @$price_position == 'after' )
						<div class="input-group-addon">{{ @$price_symbol }}</div>
					@endif
				</div>
			</div>
			<div class="help-block">{{ Lang::get('account/properties.show.property.price.min.help') }}</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-3">
		<div class="form-group error-container">
			{!! Form::label('commission_fixed', Lang::get('account/properties.show.property.commission.fixed')) !!}
			<div class="input-group">
				@if ( @$price_position == 'before' )
					<div class="input-group-addon">{{ @$price_symbol }}</div>
				@endif
				{!! Form::text('commission_fixed', @$item->commission_fixed, [ 'class'=>'form-control number', 'min'=>0 ]) !!}
				@if ( @$price_position == 'after' )
					<div class="input-group-addon">{{ @$price_symbol }}</div>
				@endif
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-3">
		<div class="form-group error-container">
			{!! Form::label('commission', Lang::get('account/properties.show.property.commission.variable')) !!}
			<div class="input-group">
				{!! Form::text('commission', @$item->commission, [ 'class'=>'form-control number', 'min'=>0, 'max'=>100 ]) !!}
				<div class="input-group-addon">%</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
	</div>
</div>
