<div class="property-moreinfo-button">
	<a href="#property-moreinfo-form" class="btn btn-primary call-to-action more-info-trigger">{{ Lang::get('web/properties.call.to.action') }}</a>
</div>

<!-- Modal -->
{!! Form::open([ 'action'=>[ 'Web\PropertiesController@moreinfo', $property->slug ], 'method'=>'POST', 'id'=>'property-moreinfo-form', 'class'=>'mfp-hide app-popup-block-white' ]) !!}
	<h2 class="page-title">{{ Lang::get('web/properties.call.to.action') }}</h2>
	<div class="alert alert-success form-success hide">
		{!! Lang::get('web/properties.moreinfo.success') !!}
		<div class="text-right">
			<a href="#" class="alert-link popup-modal-dismiss">{{ Lang::get('general.continue') }}</a>
		</div>
	</div>
	<div class="form-content">
		<div class="row">
			<div class="cols-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('first_name', Lang::get('web/customers.register.name.first') ) !!}
					{!! Form::text('first_name', old('first_name', SiteCustomer::get('first_name')), [ 'class'=>'form-control required' ] ) !!}
				</div>
			</div>
			<div class="cols-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('last_name', Lang::get('web/customers.register.name.last') ) !!}
					{!! Form::text('last_name', old('first_name', SiteCustomer::get('last_name')), [ 'class'=>'form-control required' ] ) !!}
				</div>
			</div>
		</div>
		<div class="form-group error-container">
			{!! Form::label('email', Lang::get('web/customers.register.email') ) !!}
			{!! Form::text('email', old('email', SiteCustomer::get('email')), [ 'class'=>'form-control required email' ] ) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('phone', Lang::get('web/customers.register.phone') ) !!}
			{!! Form::text('phone', old('phone', SiteCustomer::get('phone')), [ 'class'=>'form-control required' ] ) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('message', Lang::get('web/pages.message') ) !!}
			{!! Form::textarea('message', old('message'), [ 'class'=>'form-control required', 'rows'=>4 ] ) !!}
		</div>
		<div class="alert alert-danger alert-dismissible form-error hide">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<div class="alert-content"></div>
		</div>

		<div style="height: 1px; width: 1px; overflow: hidden;">
			<input type="checkbox" name="accept_legal_terms" value="1" class="" />
			Do you agree to the terms and conditions of using our services?
		</div>

		<div class="form-group text-right">
			{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default popup-modal-dismiss pull-left' ] ) !!}
			{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ] ) !!}
		</div>
	</div>
{!! Form::close() !!}
<!-- Modal -->
