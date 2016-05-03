@extends('layouts.web')

@section('content')

	<div id="login-register">
		<div class="container">

			<div class="row">
				<div class="col-xs-12 col-md-10 col-md-offset-1">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('web/customers.register.title') }}</h2>

					{!! Form::model(null, [ 'action'=>'Web\CustomersController@postRegister', 'method'=>'POST', 'id'=>'register-form' ]) !!}

						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! form::label('first_name', Lang::get('web/customers.register.name.first')) !!}
									{!! form::text('first_name', null, [ 'class'=>'form-control required']) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! form::label('last_name', Lang::get('web/customers.register.name.last')) !!}
									{!! form::text('last_name', null, [ 'class'=>'form-control required']) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! form::label('email', Lang::get('web/customers.register.email')) !!}
									{!! form::text('email', null, [ 'class'=>'form-control required email']) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! form::label('password', Lang::get('web/customers.register.password')) !!}
									<div class="input-group">
										{!! form::password('password', [ 'class'=>'form-control required']) !!}
										<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password cursor-pointer" aria-hidden="true"></span></div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! form::label('phone', Lang::get('web/customers.register.phone')) !!}
									{!! form::text('phone', null, [ 'class'=>'form-control required']) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
							</div>
						</div>
						<div class="form-group">
							<div class="text-right">
								{!! Form::submit(Lang::get('web/customers.register.button'), [ 'class'=>'btn btn-primary pull-right' ]) !!}
							</div>
						</div>

					</div>
				</div>

			{!! Form::close() !!}

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#register-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					email: {
						remote: {
							url: '{{ action('Web\CustomersController@getCheck', 'email') }}',
							type: 'get'
						}
					}
				},
				messages: {
					email: {
						remote: "{{ print_js_string( Lang::get('web/customers.register.email.used') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.on('click', '.show-hide-password', function(e){
				e.preventDefault();
				form.find('input[name="password"]').togglePassword();
			});

		});
	</script>
@endsection
