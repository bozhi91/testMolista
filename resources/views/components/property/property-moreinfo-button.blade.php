<div class="property-moreinfo-button">
	<a href="#property-moreinfo-form" class="btn btn-primary call-to-action more-info-trigger">{{ Lang::get('web/properties.call.to.action') }}</a>
</div>

	<?php
		use Illuminate\Support\Facades\DB;
		$site_id = session('SiteSetup')['site_id'];
		$result = DB::table('pages')
			->join('pages_translations', 'pages.id', '=', 'pages_translations.page_id')
			->select('pages.id')
			->where('pages_translations.slug','like','%legal%')
			->where('pages.site_id',$site_id)
			->get();

		//if the site has own privacy-policy page, go to this page
		if(count($result)>0){
			$result = DB::table('sites_domains')
				->select('domain')
				->where('site_id',$site_id)
				->first();
			$privacy_url = $result->domain."/pages/legal";
		}
		else{
			$privacy_url = "https://molista.com/legal/#privacy-policy";
		}
	?>

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

		@if ($current_site->recaptcha_enabled)
		<div class="form-group">
			<div class="g-recaptcha" data-sitekey="{{ Config::get("recaptcha.sitekey") }}"></div>
		</div>
		@endif

		<div class="row">
			<div class="cols-xs-12" style="padding-left:20px;">
				<label>
					<input type="checkbox" id="accept" onclick="if( $('#accept:checkbox:checked').length > 0)$('#subm').prop('disabled',false);else $('#subm').prop('disabled',true);"/>
					He leído y acepto
					<a target=_blank href={{$privacy_url}} style="color:#333333"> los términos y condiciones</a>
				</label>
			</div>
		</div>

		<div class="form-group text-right">
			{!! Form::button(Lang::get('general.cancel'), [ 'class'=>'btn btn-default popup-modal-dismiss pull-left' ] ) !!}
			{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'id'=>'subm', 'disabled'=>'true', 'class'=>'btn btn-primary' ] ) !!}
		</div>
	</div>
{!! Form::close() !!}
<!-- Modal -->


@if ($current_site->recaptcha_enabled)
<script src='https://www.google.com/recaptcha/api.js'></script>
@endif
