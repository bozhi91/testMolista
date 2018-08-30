@extends('layouts.corporate')

@section('content')

	<div id="customers-login">

		<div class="container">
			<h1 class="text-center">{{ Lang::get('corporate/home.footer.admin.access') }}</h1>

			<div class="row">
				<div class="col-xs-12 col-sm-6 col-sm-offset-3">

					{!! Form::model(null, [ 'action'=>'Corporate\CustomersController@postIndex', 'method'=>'post', 'id'=>'login-form' ]) !!}
						{!! Form::hidden('action', $action, [ 'class'=>'action-input' ]) !!}

						<div class="intro">{!! Lang::get('corporate/customers.intro') !!}</div>

						<div class="content">

							@include('common.messages', [ 'dismissible'=>true ])

							<div class="form-group error-container">
								{!! Form::label('subdomain', Lang::get('corporate/customers.subdomain')) !!}
								<div class="input-group">
									{!! Form::text('subdomain', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('corporate/customers.subdomain.placeholder') ]) !!}
									<div class="input-group-addon">.{{ env('APP_DOMAIN', 'Contromia.com') }}</div>
								</div>
							</div>

							<div class="switch-rel switch-rel-login {{ $action == 'login' ? '' : 'hide' }}">
								<div class="form-group error-container">
									{!! Form::label('email', Lang::get('corporate/customers.email')) !!}
									{!! Form::text('email', null, [ 'class'=>'form-control required']) !!}
								</div>
								<div class="form-group error-container">
									{!! Form::label('password', Lang::get('corporate/customers.password')) !!}
									{!! Form::password('password', [ 'class'=>'form-control required']) !!}
								</div>
								<div class="buttons-area">
									<div class="row">
										<div class="col-xs-6">
											<a href="#" class="btn btn-link switch-trigger" data-action="remember">{{ Lang::get('corporate/customers.forgot') }}</a>
										</div>
										<div class="col-xs-6 text-right">
											{!! Form::button(Lang::get('corporate/customers.enter'), [ 'type'=>'submit', 'class'=>'btn btn-next' ]) !!}
										</div>
									</div>
								</div>
							</div>

							<div class="switch-rel switch-rel-remember {{ $action == 'remember' ? '' : 'hide' }}">
								<div class="buttons-area">
									<div class="row">
										<div class="col-xs-4">
											<a href="#" class="btn btn-link switch-trigger" data-action="login">{{ Lang::get('corporate/customers.login') }}</a>
										</div>
										<div class="col-xs-8 text-right">
											{!! Form::button(Lang::get('corporate/customers.remember'), [ 'type'=>'submit', 'class'=>'btn btn-next' ]) !!}
										</div>
									</div>
								</div>
							</div>

						</div>

					{!! Form::close() !!}

				</div>
			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#login-form');

			form.on('click', '.switch-trigger', function(e){
				e.preventDefault();

				var action = $(this).data().action;
				form.find('.action-input').val(action);
				form.find('.switch-rel').addClass('hide').filter('.switch-rel-'+action).removeClass('hide');
			});

			form.validate({
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
