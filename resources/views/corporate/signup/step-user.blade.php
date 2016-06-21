@extends('corporate.signup.index', [
	'step' => 'user',
])

<?php
	$user_type = old('user.type') ? old('user.type') : (empty($data['user']['type']) ? 'new' : $data['user']['type']);
?>

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postUser', 'method'=>'post', 'id'=>'signup-form', 'class'=>'step-form' ]) !!}
				{!! Form::hidden('user[type]', $user_type) !!}

				<div class="user-tab user-tab-new {{ $user_type == 'new' ? '' : 'hide' }}">
					<h2 class="text-center">{{ Lang::get('corporate/signup.user.new.h2') }}</h2>
					<div class="step-content">
						<div class="form-group error-container">
							{!! Form::label('user[new][name]', Lang::get('corporate/signup.user.new.name'), [ 'class'=>'input-label' ]) !!}
							{!! Form::text('user[new][name]', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/signup.user.new.name.placeholder') ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('user[new][email]', Lang::get('corporate/signup.user.new.email'), [ 'class'=>'input-label' ]) !!}
							{!! Form::text('user[new][email]', null, [ 'class'=>'form-control required email', 'placeholder'=>Lang::get('corporate/signup.user.new.email.placeholder') ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('user[new][password]', Lang::get('corporate/signup.user.new.password'), [ 'class'=>'input-label' ]) !!}
							{!! Form::password('user[new][password]', [ 'class'=>'form-control required', 'minlength'=>6, 'maxlength'=>20, 'placeholder'=>Lang::get('corporate/signup.user.new.password.placeholder') ]) !!}
						</div>
						<div class="switch-area">
							<div class="checkbox error-container">
								<label>
									{!! Form::checkbox('user[new][accept]', 1, null, [ 'class'=>'required' ]) !!}
									<a href="javascript: alert('[TODO] link to terms');" target="_blank">{{ Lang::get('corporate/signup.user.new.accept') }}</a>
								</label>
							</div>
							<div class="checkbox">
								<label>
									<a href="#" class="user-type-switch" data-rel="old">{{ Lang::get('corporate/signup.user.new.have.account') }}</a>
								</label>
							</div>
						</div>
						<div class="nav-area text-center">
							{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-next' ]) !!}
						</div>
					</div>
				</div>

				<div class="user-tab user-tab-old {{ $user_type == 'old' ? '' : 'hide' }}">
					<h2 class="text-center">{{ Lang::get('corporate/signup.user.old.h2') }}</h2>
					<div class="step-content">
						<div class="form-group error-container">
							{!! Form::label('user[old][email]', Lang::get('corporate/signup.user.new.email'), [ 'class'=>'input-label' ]) !!}
							{!! Form::text('user[old][email]', null, [ 'class'=>'form-control required email', 'placeholder'=>Lang::get('corporate/signup.user.new.email.placeholder') ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('user[old][password]', Lang::get('corporate/signup.user.new.password'), [ 'class'=>'input-label' ]) !!}
							{!! Form::password('user[old][password]', [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/signup.user.new.password.placeholder') ]) !!}
						</div>
						<div class="switch-area">
							<div class="error-container">
								{{ Lang::get('corporate/signup.user.old.no.account') }}
								<a href="#" class="user-type-switch" data-rel="new"><strong>{{ Lang::get('corporate/signup.user.old.create.account') }}</strong></a>
							</div>
							<div class="error-container">
								{{ Lang::get('corporate/signup.user.old.password.forgot') }}
								<a href="javascript: alert('[TODO] link to password remind');" target="_blank"><strong>{{ Lang::get('corporate/signup.user.old.password.click') }}</strong></a>
							</div>
						</div>
						<div class="nav-area text-center">
							{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-nav btn-nav-next' ]) !!}
						</div>
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

			form.on('click','.user-type-switch',function(e){
				e.preventDefault();

				var t = $(this).data().rel;

				form.find('input[name="user[type]"]').val(t);
				form.find('.user-tab').addClass('hide').filter('.user-tab-' + t).removeClass('hide');
			});

		});
	</script>
@endsection
