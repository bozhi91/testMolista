<div role="tabpanel" class="tab-pane tab-main active" id="tab-data">
	{!! Form::model(Auth::user(), [ 'method'=>'POST', 'files'=>true, 'action'=>'AccountController@updateProfile', 'id'=>'user-profile-form' ]) !!}

		@include('account.user-form', [
			'user_image' => empty(Auth::user()->image) ? false : Auth::user()->image_directory . '/' . Auth::user()->image,
			'user_email' => Auth::user()->email,
		])

		<br />

		<div class="text-right">
			{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
		</div>

	{!! Form::close() !!}
</div>

<script type="text/javascript">
	ready_callbacks.push(function() {
		var form = $('#user-profile-form');

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
