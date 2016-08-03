@extends('layouts.account')

@section('account_content')

	<div class="site-domainname">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.domainname.h1') }}</h1>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\Site\DomainNameController@postIndex', 'id'=>'admin-site-domainname-form' ]) !!}

			<div class="form-group">
				<div class="row">
					<div class="col-xs-12 col-sm-6">
						<div class="error-container">
							{!! Form::label('domain', Lang::get('account/site.domainname.domain')) !!}
							{!! Form::text('domain', @$current_site->domain_default, [ 'class'=>'domain-input form-control url' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-6 hidden-xs">
						<label class="">&nbsp;</label>
						<div>
							{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
						</div>
					</div>
				</div>
				<div class="help-block">{!! Lang::get('account/site.domainname.domain.helper') !!}</div>
			</div>

			<div class="text-right visible-xs">
				{!! Form::button( Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary btn-block']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#admin-site-domainname-form');

			// Form validation
			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				rules: {
					"domain" : {
						remote: {
							url: '{{ action('Account\Site\DomainNameController@getCheck', 'domain') }}',
							type: 'get',
							data: {
								id: '{{@$current_site->id}}',
								domain: function() { return form.find('.domain-input').val(); }
							}
						},
					},
				},
				messages: {
					"domain" : {
						remote: "{{ print_js_string( Lang::get('account/site.domainname.domain.error') ) }}"
					}
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});
		});
	</script>

@endsection