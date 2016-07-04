@extends('corporate.signup.index', [
	'step' => 'confirm',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-md-8 col-md-offset-2">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postConfirm', 'method'=>'post', 'id'=>'signup-form', 'class'=>'step-form' ]) !!}

				<h2 class="text-center">{{ Lang::get('corporate/signup.confirm.h2') }}</h2>

				<div class="step-content">

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							@if ( $data['invoicing']['type'] == 'company' )
								<div class="confirm-block">
									<div class="confirm-label">{{ Lang::get('corporate/signup.invoicing.company') }}</div>
									<div class="confirm-value">{{ $data['invoicing']['company'] }}</div>
									<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getInvoicing') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
								</div>
							@endif
							<div class="confirm-block">
								<div class="confirm-label">{{ Lang::get('corporate/signup.confirm.name') }}</div>
								<div class="confirm-value">{{ $data['invoicing']['first_name'] }} {{ $data['invoicing']['last_name'] }}</div>
								<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getInvoicing') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
							</div>
							<div class="confirm-block">
								<div class="confirm-label">{{ Lang::get('corporate/signup.invoicing.email') }}</div>
								<div class="confirm-value">{{ $data['invoicing']['email'] }}</div>
								<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getInvoicing') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="confirm-block">
								<div class="confirm-label">{{ Lang::get('corporate/signup.invoicing.address') }}</div>
								<div class="confirm-value">
									{{ $data['invoicing']['street'] }}<br />
									{{ $data['invoicing']['city'] }}{{ empty($data['invoicing']['zipcode']) ? '' : ", {$data['invoicing']['zipcode']}" }}<br />
									{{ $data['invoicing']['country'] }}<br />
								</div>
								<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getInvoicing') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
							</div>
							@if ( $data['invoicing']['tax_id'] )
								<div class="confirm-block">
									<div class="confirm-label">{{ Lang::get('corporate/signup.invoicing.tax_id') }}</div>
									<div class="confirm-value">{{ $data['invoicing']['tax_id'] }}</div>
									<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getInvoicing') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
								</div>
							@endif
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="confirm-block">
								<div class="confirm-label">{{ Lang::get('corporate/signup.confirm.plan') }}</div>
								<div class="confirm-value">
									<span class="text-uppercase">{{ $data['plan']['name'] }}</span>
									@if ( @$data['plan']['is_free'] )
									@else
										<br />
										@if ( @$data['pack']['payment_interval'][$data['pack']['selected']] == 'month' )
											{{ Lang::get('web/plans.price.month') . ' ' . price($data['plan']['price_month'], [ 'decimals'=>0 ]) }}
										@else
											{{ Lang::get('web/plans.price.year') . ' ' . price($data['plan']['price_year'], [ 'decimals'=>0 ]) }}
										@endif
									@endif
								</div>
								<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getPack') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							@if ( @$data['plan']['is_free'] )
							@elseif ( @$data['payment']['method'] )
								<div class="confirm-block">
									<div class="confirm-label">{{ Lang::get('corporate/signup.payment.h2') }}</div>
									<div class="confirm-value">
										{{ Lang::get("account/payment.method.{$data['payment']['method']}") }}
										@if ( $data['payment']['method'] == 'transfer' )
											<br />
											IBAN {{ $data['payment']['iban_account'] }}
										@endif
									</div>
								</div>
							@endif
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="confirm-block">
								<div class="confirm-label">{{ Lang::get('corporate/signup.confirm.subdomain') }}</div>
								<div class="confirm-value">{{ $data['site']['subdomain'] }}.{{ \Config::get('app.application_domain') }}</div>
								<div class="confirm-change"><a href="{{ action('Corporate\SignupController@getSite') }}">{{ Lang::get('corporate/signup.confirm.change') }}</a></div>
							</div>
						</div>
					</div>

					<div class="nav-area">
						<a href="{{ action('Corporate\SignupController@getInvoicing')}}" class="btn btn-nav btn-nav-prev">{{ Lang::get('corporate/signup.previous') }}</a>
						{!! Form::button(Lang::get('corporate/signup.accept'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-next pull-right' ]) !!}
					</div>

				</div>

			{!! Form::close() !!}

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#signup-form');

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

		});
	</script>
@endsection
