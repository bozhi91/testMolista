@extends('layouts.web')

@section('content')

	<div id="login-register">

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('web/customers.login.title') }}</h2>

					{!! Form::model(null, [ 'action'=>'Web\CustomersController@postLogin', 'method'=>'POST', 'id'=>'login-form', 'class'=>'form-horizontal' ]) !!}
						<div class="form-group">
							{!! Form::label('email', Lang::get('web/customers.login.email'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::email('email', null, [ 'class'=>'form-control required email' ]) !!}
							</div>
						</div>
						<div class="form-group">
							{!! Form::label('password', Lang::get('web/customers.login.password'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::password('password', [ 'class'=>'form-control required' ]) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{!! Form::submit(Lang::get('web/customers.login.button'), [ 'class'=>'btn btn-primary pull-right' ]) !!}
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
