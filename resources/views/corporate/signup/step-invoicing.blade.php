@extends('corporate.signup.index', [
	'step' => 'invoicing',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postInvoicing', 'method'=>'post', 'id'=>'signup-form' ]) !!}

				@if ( @$data['payment']['method'] == 'none' )
					<h2 class="text-center">{{ Lang::get('corporate/signup.invoicing.h2') }}</h2>
				@else
					<h2 class="text-center">{{ Lang::get('corporate/signup.payment.h2') }}: {{ Lang::get("account/payment.method.{$data['payment']['method']}") }}</h2>
				@endif

				<div class="plans-container">
					<div class="form-group error-container">
						<div class="form-inline">
							<div class="checkbox">
								<label>
									{!! Form::radio('invoicing[type]', 'individual', null, [ 'class'=>'required' ]) !!}
									{{ Lang::get('corporate/signup.invoicing.type.individual') }}
								</label>
							</div>
							<div class="checkbox">
								<label>
									{!! Form::radio('invoicing[type]', 'company', null, [ 'class'=>'required' ]) !!}
									{{ Lang::get('corporate/signup.invoicing.type.company') }}
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[first_name]', Lang::get('corporate/signup.invoicing.first_name')) !!}
								{!! Form::text('invoicing[first_name]', null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[last_name]', Lang::get('corporate/signup.invoicing.last_name')) !!}
								{!! Form::text('invoicing[last_name]', null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[email]', Lang::get('corporate/signup.invoicing.email')) !!}
								{!! Form::text('invoicing[email]', null, [ 'class'=>'form-control required email' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[tax_id]', Lang::get('corporate/signup.invoicing.tax_id')) !!}
								{!! Form::text('invoicing[tax_id]', null, [ 'class'=>'form-control' ]) !!}
							</div>
						</div>
					</div>
					<div class="address-title">{{ Lang::get('corporate/signup.invoicing.address') }}</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[street]', Lang::get('corporate/signup.invoicing.street')) !!}
								{!! Form::text('invoicing[street]', null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[zipcode]', Lang::get('corporate/signup.invoicing.zipcode')) !!}
								{!! Form::text('invoicing[zipcode]', null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[city]', Lang::get('corporate/signup.invoicing.city')) !!}
								{!! Form::text('invoicing[city]', null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group error-container">
								{!! Form::label('invoicing[country_id]', Lang::get('corporate/signup.invoicing.country')) !!}
								{!! Form::select('invoicing[country_id]', [ ''=>'' ]+$countries, null, [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
					</div>
				</div>

				<div class="coupon-area">
					{!! Form::hidden('invoicing[use_coupon]', null) !!}
					<div class="coupon-question">
						{{ Lang::get('corporate/signup.invoicing.coupon.have') }}
						<a href="#" class="coupon-trigger">{{ Lang::get('corporate/signup.invoicing.coupon.use') }}</a>
					</div>
					<div class="coupon-input hide">
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label('invoicing[coupon]', Lang::get('corporate/signup.invoicing.coupon')) !!}
									{!! Form::text('invoicing[coupon]', null, [ 'class'=>'form-control' ]) !!}
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="nav-area">
					@if ( @$data['plan']['is_free'] )
						<a href="{{ action('Corporate\SignupController@getSite')}}" class="btn btn-primary">{{ Lang::get('corporate/signup.previous') }}</a>
					@else
						<a href="{{ action('Corporate\SignupController@getPayment')}}" class="btn btn-primary">{{ Lang::get('corporate/signup.previous') }}</a>
					@endif
					{!! Form::button('<span class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span>' . Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-primary pull-right' ]) !!}
				</div>

			{!! Form::close() !!}

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#signup-form');
			var address_area = form.find('.address-area');

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.on('click','.coupon-trigger',function(e){
				e.preventDefault();
				form.find('input[name="invoicing[use_coupon]"]').val(1);
				form.find('.coupon-question').addClass('hide');
				form.find('.coupon-input').removeClass('hide');
			});
			if ( form.find('input[name="invoicing[coupon]"]').val() ) {
				form.find('.coupon-trigger').trigger('click');
			}

		});
	</script>
@endsection
