@extends('corporate.signup.index', [
	'step' => 'payment',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postPayment', 'method'=>'post', 'id'=>'signup-form', 'class'=>'step-form' ]) !!}

				<h2 class="text-center">{{ Lang::get('corporate/signup.payment.h2') }}</h2>

				<div class="step-content">

					<div class="step-padder">
						<div class="form-group error-container">
							{!! Form::label('payment[method]', Lang::get('corporate/signup.payment.choose'), [ 'class'=>'input-label text-center' ]) !!}
							{!! Form::select('payment[method]', $paymethods, null, [ 'class'=>'form-control text-center required' ]) !!}
						</div>
						<div class="form-group error-container payment-method-rel payment-method-rel-transfer hide">
							{!! Form::label('payment[iban_account]', Lang::get('corporate/signup.payment.iban'), [ 'class'=>'input-label text-center' ]) !!}
							{!! Form::text('payment[iban_account]', null, [ 'class'=>'form-control text-center' ]) !!}
						</div>
					</div>

					<div class="nav-area">
						<a href="{{ action('Corporate\SignupController@getSite')}}" class="btn btn-nav btn-nav-prev">{{ Lang::get('corporate/signup.previous') }}</a>
						{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-prev pull-right' ]) !!}
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
					"payment[iban_account]": {
						required: function() {
							return form.find('select[name="payment[method]"]').val() == 'transfer';
						}
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.find('select[name="payment[method]"]').on('change', function(){
				form.find('.payment-method-rel').addClass('hide').filter('.payment-method-rel-' + $(this).val() ).removeClass('hide');
			}).trigger('change');
		});
	</script>
@endsection
