@extends('corporate.signup.index', [
	'step' => 'pack',
])

<?php
	$user_type = old('user.type') ? old('user.type') : (empty($data['user']['type']) ? 'new' : $data['user']['type']);
?>

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">

			@include('common.messages', [ 'dismissible'=>true ])


			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postPack', 'method'=>'post', 'id'=>'signup-form', 'class'=>'step-form' ]) !!}

				<h2 class="text-center">{{ Lang::get('corporate/signup.pack.h2') }}</h2>

				<div class="step-content">

					<div class="plans-container">
						@foreach ($plans as $plan)
							<div class="form-inline plan-row">
								<div class="form-group">
									<div class="checkbox">
										<label class="pack-label text-nowrap">
											{!! Form::radio('pack[selected]', $plan->code, null, [ 'class'=>'plan-input required' ]) !!}
											{{ $plan->name }}
										</label>
									</div>
								</div>
								<div class="form-group error-container">
									@if ( $plan->is_free )
										{!! Form::select("pack[payment_interval][{$plan->code}]", [
											'year' => Lang::get('web/plans.free'),
										], null, [ 'class'=>'payment-interval-select form-control', 'disabled'=>'disabled' ]) !!}
									@else
										{!! Form::select("pack[payment_interval][{$plan->code}]", [
											'year' => Lang::get('web/plans.price.year') . ' ' . price($plan->price_year, $plan->infocurrency->toArray()),
											'month' => Lang::get('web/plans.price.month') . ' ' . price($plan->price_month, $plan->infocurrency->toArray()),
										], null, [ 'class'=>'payment-interval-select form-control', 'disabled'=>'disabled' ]) !!}
									@endif
								</div>
							</div>
						@endforeach
					</div>

					<div class="nav-area">
						<a href="{{ action('Corporate\SignupController@getUser')}}" class="btn btn-nav btn-nav-prev">{{ Lang::get('corporate/signup.previous') }}</a>
						{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-next pull-right' ]) !!}
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
					if ( element.hasClass('plan-input') ) {
						element.closest('.plans-container').append(error);
					} else {
						element.closest('.error-container').append(error);
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			var w = 0;
			form.find('.plan-row').each(function(){
				var cont = $(this);
				var chk = cont.find('input[name="pack[selected]"]');

				chk.on('click', function(){
					form.find('.payment-interval-select').removeClass('required').attr('disabled','disabled');
					cont.find('.payment-interval-select').addClass('required').removeAttr('disabled');
				});

				if ( chk.is(':checked') ) {
					chk.trigger('click');
				}

				if ( w < cont.find('.checkbox').width() ) {
					w = cont.find('.checkbox').width();
				}
			});
			form.find('.plan-row').css({ opacity: 1 }).find('.checkbox').width(w);

		});
	</script>
@endsection
