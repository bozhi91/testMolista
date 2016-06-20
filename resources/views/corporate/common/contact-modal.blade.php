<div id="contact-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="col-xs-12 col-sm-10 col-sm-offset-2">
					<h4 class="modal-title">{{ Lang::get('corporate/general.contact.ready') }}</h4>
					<p class="modal-title">{{ Lang::get('corporate/general.contact.data') }}</p>
				</div>
			</div>
			<div class="modal-body">
				@if ( session('contact_success') )
					@include('common.messages')
				@else
					@if ( session('contact_error') )
						@include('common.messages', [ 'dismissible'=>true ])
					@endif
					{!! Form::model(null, [
						'action'=>'Corporate\InfoController@postContact',
						'method'=>'POST',
						'id'=>'contact-form'
					]) !!}
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<div class="form-group error-container">
									{!! Form::label('name', Lang::get('corporate/general.contact.name')) !!}
									{!! Form::text('name', null, [ 'class'=>'form-control required' , 'placeholder'=>Lang::get('corporate/general.contact.name.placeholder') ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<div class="form-group error-container">
									{!! Form::label('email', Lang::get('corporate/general.contact.email')) !!}
									{!! Form::text('email', null, [ 'class'=>'form-control required email' , 'placeholder'=>Lang::get('corporate/general.contact.email.placeholder') ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<div class="form-group error-container">
									{!! Form::label('phone', Lang::get('corporate/general.contact.phone')) !!}
									{!! Form::text('phone', null, [ 'class'=>'form-control required' , 'placeholder'=>Lang::get('corporate/general.contact.phone.placeholder') ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-8 col-sm-offset-2">
								<div class="form-group error-container">
									{!! Form::label('details', Lang::get('corporate/general.contact.details')) !!}
									{!! Form::textarea('details', null, [ 'class'=>'form-control required' , 'rows'=>'4' , 'placeholder'=>Lang::get('corporate/general.contact.details.placeholder') ]) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
								<div class="text-center">
									{!! Form::button(Lang::get('corporate/general.contact.send'), [ 'type'=>'submit', 'class'=>'btn btn-primary btnBdrYlw moreinfo-button' ]) !!}
								</div>
							</div>
						</div>
					{!! Form::close() !!}
				@endif
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		$('#contact-form').validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			}
		});

		@if ( session('contact_error') || session('contact_success') )
			$('#contact-modal').modal('show');
		@endif
	});
</script>
