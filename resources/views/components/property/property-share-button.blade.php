<a href="#property-share-form" class="btn btn-primary more-info-trigger call-to-action">
	{{ Lang::get('web/properties.recommend.action') }}
</a>

{!! Form::open([ 'action'=>[ 'Web\PropertiesController@sharefriend', $property->slug ], 'method'=>'POST', 'id'=>'property-share-form', 'class'=>'mfp-hide app-popup-block-white' ]) !!}
		<h2 class="page-title">
			{{ Lang::get('web/properties.recommend.header') }}
		</h2>
		<div class="alert alert-success form-success hide">
			{!! Lang::get('web/properties.recommend.success') !!}
			<div class="text-right">
				<a href="#" class="alert-link popup-modal-dismiss">{{ Lang::get('general.continue') }}</a>
			</div>
		</div>
		<div class="form-content">
			<div class="row">
				<div class="cols-xs-12 col-sm-12">
					<div class="form-group error-container">
						{!! Form::label('r_email', Lang::get('web/properties.recommend.recieve_email') ) !!}
						{!! Form::text('r_email', '', [ 'class'=>'form-control required email' ] ) !!}
						{!! Form::hidden('f_email', '') !!}
						{!! Form::hidden('link', Request::url()) !!}
					</div>
				</div>
			</div>
			<div class="row">
				<div class="cols-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('name', Lang::get('web/properties.recommend.send_name') ) !!}
						{!! Form::text('name', old('first_name', SiteCustomer::get('first_name')), [ 'class'=>'form-control required' ] ) !!}
					</div>
				</div>
				<div class="cols-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('email', Lang::get('web/properties.recommend.send_email') ) !!}
						{!! Form::text('email', old('email', SiteCustomer::get('email')), [ 'class'=>'form-control required email' ] ) !!}
					</div>
				</div>
			</div>
			<div class="form-group error-container">
				{!! Form::label('message', Lang::get('web/pages.message') ) !!}
				{!! Form::textarea('message', Lang::get('web/properties.recommend.default_message'), [ 'class'=>'form-control required', 'rows'=>4 ] ) !!}
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
				{!! Form::button(Lang::get('general.send'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ] ) !!}
			</div>
		</div>
	{!! Form::close() !!}
