@extends('layouts.web')

@section('content')

	<div id="login-register">

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('passwords.reset.title') }}</h2>

					{!! Form::open([ 'action'=>'Auth\PasswordController@reset', 'method'=>'POST', 'id'=>'reset-form', 'class'=>'form-horizontal' ]) !!}
						<input type="hidden" name="token" value="{{ $token }}">
						<div class="form-group">
							{!! Form::label('email', Lang::get('passwords.reset.email'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::email('email', @$email, [ 'class'=>'form-control required email' ]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('password', Lang::get('passwords.reset.password'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::password('password', [ 'class'=>'form-control required', 'minlength'=>'6' ]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('password_confirmation', Lang::get('passwords.reset.password.confirm'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::password('password_confirmation', [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{!! Form::submit(Lang::get('passwords.reset.reset.button'), [ 'class'=>'btn btn-primary pull-right' ]) !!}
							</div>
						</div>
					{!! Form::close() !!}

				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#reset-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				},
				rules: {
					password_confirmation: {
						equalTo: '#password'
					}


				}
			});

		});
	</script>

@endsection
