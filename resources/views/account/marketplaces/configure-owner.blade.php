<hr />

<h4>{{ Lang::get('account/marketplaces.configuration.owner.title') }}</h4>

<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('marketplace_configuration[owner][fullname]', Lang::get('account/marketplaces.configuration.owner.fullname')) !!}
			{!! Form::text('marketplace_configuration[owner][fullname]', @$configuration->owner->fullname, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('marketplace_configuration[owner][email]', Lang::get('account/marketplaces.configuration.owner.email')) !!}
			{!! Form::text('marketplace_configuration[owner][email]', @$configuration->owner->email, [ 'class'=>'form-control required email' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('marketplace_configuration[owner][cif]', Lang::get('account/marketplaces.configuration.owner.cif')) !!}
			{!! Form::text('marketplace_configuration[owner][cif]', @$configuration->owner->cif, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
</div>
