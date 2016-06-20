@extends('corporate.signup.index', [
	'step' => 'site',
])

@section('signup_content')

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">

			@include('common.messages', [ 'dismissible'=>true ])

			{!! Form::model($data, [ 'action'=>'Corporate\SignupController@postSite', 'method'=>'post', 'id'=>'signup-form' ]) !!}

				<h2 class="text-center">{{ Lang::get('corporate/signup.site.h2') }}</h2>

				<div class="plans-container">
					<div class="form-group error-container">
						<div class="text-center">
							{!! Form::label('site[subdomain]', Lang::get('corporate/signup.site.subdomain')) !!}
						</div>
						{!! Form::text('site[subdomain]', null, [ 'class'=>'form-control required text-center alphanumericHypen', 'placeholder'=>Lang::get('corporate/signup.site.subdomain.placeholder') ]) !!}
					</div>
					<div class="address-area text-center hide">
						<p>{{ Lang::get('corporate/signup.site.subdomain.sample') }}</p>
						<p><strong><span class="subdomain-text"></span>.{{ \Config::get('app.application_domain') }}</strong></p>
					</div>

					@if ( @$data['plan']['max_languages'] == 1 )
						<div class="form-group error-container">
							<div class="text-center">
								{!! Form::label('site[language]', Lang::get('corporate/signup.site.language')) !!}
							</div>
							{!! Form::select('site[language]', $languages, null, [ 'class'=>'form-control required' ]) !!}
						</div>
					@endif
				</div>

				<div class="nav-area">
					<a href="{{ action('Corporate\SignupController@getPack')}}" class="btn btn-primary">{{ Lang::get('corporate/signup.previous') }}</a>
					{!! Form::button(Lang::get('corporate/signup.next'), [ 'type'=>'submit', 'class'=>'btn btn-primary pull-right' ]) !!}
				</div>

			{!! Form::close() !!}

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#signup-form');
			var address_area = form.find('.address-area');

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					"site[subdomain]": {
						remote: {
							url: '{{action('Ajax\SiteController@getValidate','subdomain')}}',
							data: { 
								subdomain: function() {
									return form.find('input[name="site[subdomain]"]').val();
								}
							}
						}
					}
				},
				messages: {
					"site[subdomain]": {
						remote: "{{ print_js_string( Lang::get('admin/sites.subdomain.error.taken') ) }}",
						alphanumericHypen: "{{ print_js_string( Lang::get('validation.alphanumericHypen') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			form.on('keyup','input[name="site[subdomain]"]',function(){
				var val = $(this).val();

				if ( val ) {
					address_area.removeClass('hide').find('.subdomain-text').text(val);
				} else {
					address_area.addClass('hide');
				}
			});
			form.find('input[name="site[subdomain]"]').trigger('keyup');

		});
	</script>
@endsection
