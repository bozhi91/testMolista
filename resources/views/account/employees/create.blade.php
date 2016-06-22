@extends('layouts.account')

@section('account_content')

	<div id="admin-employees" class="row">
		<div class="col-xs-12">

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">{{ Lang::get('account/employees.create.title') }}</h1>

			{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\EmployeesController@store', 'id'=>'edit-form' ]) !!}

				@include('account.user-form')

				<br />

				<div class="text-right">
					{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
					{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
				</div>

			{!! Form::close() !!}


		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#edit-form');

			// Form validation
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					email: {
						remote: {
							url: '{{ action('Ajax\UserController@getValidate', 'email') }}',
							type: 'get',
							data: {
								not_employee: true,
								exclude: {{ empty($user) ? 0 : $user->id }}
							}
						}
					},
					image: {
						required: function() {
							if ( form.find('.user-image-link').length > 0 ) {
								return false;
							}
							return form.find('select[name="signature"]').val() == 1;
						}
					}
				},
				messages: {
					email: {
						remote: "{{ trim( Lang::get('account/employees.email.used') ) }}"
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