<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('name', Lang::get('account/properties.districts.name')) !!}
			{!! Form::text('name', isset($district) ? $district->name : '', [ 'class'=>'form-control required']) !!}
		</div>
	</div>
</div>