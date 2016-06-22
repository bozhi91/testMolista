@extends('layouts.account')

@section('account_content')

	@if (session('status'))
		<div class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
			{{ session('status') }}
		</div>
	@else
		@include('common.messages', [ 'dismissible'=>true ])
	@endif

	<h1 class="page-title">{{ Lang::get('account/profile.h1') }}</h1>

	{!! Form::model(Auth::user(), [ 'method'=>'POST', 'files'=>true, 'action'=>'AccountController@updateProfile', 'id'=>'user-profile-form' ]) !!}

		@include('account.user-form', [
			'user_image' => empty(Auth::user()->image) ? false : Auth::user()->image_directory . '/' . Auth::user()->image,
		])

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

	{!! Form::close() !!}

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#user-profile-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					image: {
						required: function() {
							if ( form.find('.user-image-link').length > 0 ) {
								return false;
							}
							return form.find('select[name="signature"]').val() == 1;
						}
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
