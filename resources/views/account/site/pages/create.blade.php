@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/site.pages.create.title') }}</h1>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\Site\PagesController@store', 'id'=>'create-form' ]) !!}

			<div class="custom-tabs">

				<ul class="nav nav-tabs main-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#tab-general" aria-controls="tab-general" role="tab" data-toggle="tab">{{ Lang::get('account/site.pages.tab.general') }}</a></li>
				</ul>

				<div class="tab-content">

					<div role="tabpanel" class="tab-pane tab-main active" id="tab-general">
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label("i18n[title][en]", Lang::get('account/site.pages.column.type')) !!}
									{!! Form::select("type", $types, null, [ 'class'=>'form-control required' ]) !!}
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group error-container">
									{!! Form::label("i18n[title][en]", Lang::get('account/site.pages.title')) !!}
									{!! Form::text("i18n[title][en]", null, [ 'class'=>'form-control required' ]) !!}
								</div>
							</div>
						</div>
					</div>

				</div>

				<br />

				<div class="text-right">
					{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
					{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
				</div>

				<br />

			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#create-form');

			// Form validation
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