@extends('layouts.web')

@section('content')

	<div id="login-register">

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('auth.login.title') }}</h2>

					{!! Form::model(null, [ 'action'=>'Auth\AuthController@showLoginForm', 'method'=>'POST', 'id'=>'login-form', 'class'=>'form-horizontal' ]) !!}
						<div class="form-group">
							{!! Form::label('email', Lang::get('auth.login.email'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::email('email', null, [ 'class'=>'form-control required email' ]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('password', Lang::get('auth.login.password'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::password('password', [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										{!! Form::checkbox('remember', 1) !!}
										{{ Lang::get('auth.login.remember') }}
									</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{!! Form::submit(Lang::get('auth.login.button'), [ 'class'=>'btn btn-primary pull-right' ]) !!}
								<a class="btn-remember" href="{{ action('Auth\PasswordController@reset') }}">{{ Lang::get('auth.login.forgot') }}</a>
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#login-form');

			form.validate({
				ignore: '',
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
