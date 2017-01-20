@extends('layouts.resellers')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">

				<div class="row">
					<div class="col-md-6 col-md-offset-4 error-container">
						<h2>{{ Lang::get('auth.login.title') }}</h2>

						@include('common.messages')

					</div>
				</div>

				{!! Form::model(null, [ 'action'=>'Resellers\AuthController@postLogin', 'method'=>'POST', 'id'=>'login-form', 'class'=>'form-horizontal' ]) !!}
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
						</div>
					</div>
				{!! Form::close() !!}
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
