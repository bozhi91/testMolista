@extends('layouts.web')

@section('content')

	<div id="login-register">

		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">

					@include('common.messages', [ 'dismissible'=>true ])

					<h2>{{ Lang::get('passwords.reset.title') }}</h2>

					@if (session('status'))
						<div class="alert alert-success alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
							{{ session('status') }}
						</div>
					@endif

					{!! Form::open([ 'action'=>'Auth\PasswordController@sendResetLinkEmail', 'method'=>'POST', 'id'=>'reset-form', 'class'=>'form-horizontal' ]) !!}
						<div class="form-group">
							{!! Form::label('email', Lang::get('passwords.reset.email'), [ 'class'=>'col-md-4 control-label' ]) !!}
							<div class="col-md-6 error-container">
								{!! Form::email('email', old('email'), [ 'class'=>'form-control required email' ]) !!}
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								{!! Form::submit(Lang::get('passwords.reset.send.button'), [ 'class'=>'btn btn-primary pull-right' ]) !!}
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
				}
			});

		});
	</script>

@endsection
