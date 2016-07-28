@extends('layouts.account')

@section('account_content')

	<div id="account-marketplaces">

		@if ( session('success') )
			<div class="alert alert-success">
				{!! session('success') !!}
			</div>
		@else

	        @include('common.messages', [ 'dismissible'=>true ])

			<h1 class="page-title">{{ Lang::get('account/marketplaces.h1') }}: {{ $marketplace->name }}</h1>

			{!! Form::model(@$contact_data, [ 'id'=>'marketplace-form', 'action'=>[ 'Account\MarketplacesController@postContact', $marketplace->code ] ]) !!}

				<h4>{{ Lang::get('account/marketplaces.contact.title', [
					'marketplace' => $marketplace->name
				]) }}</h4>

				<div class="row">
					<div class="col-xs-12 col-sm-6">
						{!! Lang::get('account/marketplaces.contact.intro') !!}
					</div>
				</div>

				<br />

				<div class="row">
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('name', Lang::get('account/marketplaces.contact.name')) !!}
							{!! Form::text('name', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('phone', Lang::get('account/marketplaces.contact.phone')) !!}
							{!! Form::text('phone', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="form-group error-container">
							{!! Form::label('email', Lang::get('account/marketplaces.contact.email')) !!}
							{!! Form::text('email', null, [ 'class'=>'form-control required email' ]) !!}
						</div>
					</div>
				</div>

				<div class="text-right">
					{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
					{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
				</div>

			{!! Form::close() !!}

		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var form = $('#marketplace-form');

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
