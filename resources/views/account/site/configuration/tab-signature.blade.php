<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('signature[name]', Lang::get('account/site.configuration.signature.name')) !!}
			{!! Form::text('signature[name]', null, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('signature[phone]', Lang::get('account/site.configuration.signature.phone')) !!}
			{!! Form::text('signature[phone]', null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('signature[email]', Lang::get('account/site.configuration.signature.email')) !!}
			{!! Form::text('signature[email]', null, [ 'class'=>'form-control email' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('signature[address]', Lang::get('account/site.configuration.signature.address')) !!}
			{!! Form::text('signature[address]', null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
</div>
