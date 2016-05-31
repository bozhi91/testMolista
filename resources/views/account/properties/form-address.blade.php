<div class="form-group">
	<div class="error-container">
		{!! Form::label('address', Lang::get('account/properties.address')) !!}
		{!! Form::textarea('address', null, [ 'class'=>'form-control address-input', 'rows'=>'3' ]) !!}
	</div>
	<div class="help-block">
		<label>
			{!! Form::checkbox('show_address', 1, null) !!}
			{{ Lang::get('account/properties.show_address') }}
		</label>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('zipcode', Lang::get('account/properties.zipcode')) !!}
			{!! Form::text('zipcode', null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
</div>
