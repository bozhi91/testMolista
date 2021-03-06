@extends('corporate.signup.index', [
	'step' => 'invoicing',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postInvoicing', 'method'=>'post', 'id'=>'signup-form', 'class'=>'step-form' ]) !!}

				@if ( @$data['payment']['method'] == 'none' )
					<h2 class="text-center">{{ Lang::get('corporate/signup.invoicing.h2') }}</h2>
				@else
					<h2 class="text-center">{{ Lang::get('corporate/signup.payment.h2') }}: {{ Lang::get("account/payment.method.{$data['payment']['method']}") }}</h2>
				@endif

				<div class="step-content">

					@include('account.payment.form-invoicing', [
						'invoicing_type' => @$data['invoicing']['type'],
					])

					<div class="coupon-area hide">
						{!! Form::hidden('invoicing[use_coupon]', null) !!}
						<div class="coupon-question">
							{{ Lang::get('corporate/signup.invoicing.coupon.have') }}
							<a href="#" class="coupon-switch"><strong>{{ Lang::get('corporate/signup.invoicing.coupon.use') }}</strong></a>
						</div>
						<div class="coupon-input hide">
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<div class="form-group error-container">
										{!! Form::label('invoicing[coupon]', Lang::get('corporate/signup.invoicing.coupon'), [ 'class'=>'input-label' ]) !!}
										{!! Form::text('invoicing[coupon]', null, [ 'class'=>'form-control' ]) !!}
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="nav-area">
						@if ( @$data['plan']['is_free'] )
							<a href="{{ action('Corporate\SignupController@getSite')}}" class="btn btn-nav btn-nav-prev">{{ Lang::get('corporate/signup.previous') }}</a>
						@else
							<a href="{{ action('Corporate\SignupController@getPayment')}}" class="btn btn-nav btn-nav-prev">{{ Lang::get('corporate/signup.previous') }}</a>
						@endif
						{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-next pull-right' ]) !!}
					</div>

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
				rules: {
					"invoicing[company]": {
						required: function() {
							return form.find('input[name="invoicing[type]"]:checked').val() == 'company';
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.on('click','.coupon-switch',function(e){
				e.preventDefault();
				if ( form.find('.coupon-question').hasClass('hide') ) {
					form.find('input[name="invoicing[use_coupon]"]').val(o);
					form.find('.coupon-question').removeClass('hide');
					form.find('.coupon-input').addClass('hide');
				} else {
					form.find('input[name="invoicing[use_coupon]"]').val(1);
					form.find('.coupon-question').addClass('hide');
					form.find('.coupon-input').removeClass('hide');
				}
			});
			if ( form.find('input[name="invoicing[coupon]"]').val() ) {
				form.find('.coupon-question .coupon-switch').trigger('click');
			}

		});
	</script>
@endsection
