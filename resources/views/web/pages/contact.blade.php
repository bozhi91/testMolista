@extends('layouts.web')

@section('content')

	<div id="pages">

		<div class="container">
			<h1>{{ $page->title }}</h1>

			@include('common.messages')
			
			<div class="row">

				<div class="cols-xs-12 col-sm-6">
					<div class="body">
						{!! $page->body !!}
					</div>
				</div>

				<div class="cols-xs-12 col-sm-6">
					{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Web\PagesController@post', $page->slug ], 'id'=>'contact-form' ]) !!}
						<div class="form-group error-container">
							{!! Form::label('interest', Lang::get('web/pages.interest')) !!}
							{!! Form::select('interest', [
								'buy' => Lang::get('web/pages.interest.buy'),
								'rent' => Lang::get('web/pages.interest.rent'),
								'sell' => Lang::get('web/pages.interest.sell'),
							], null, [ 'class'=>'form-control' ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('name', Lang::get('web/pages.name').' *') !!}
							{!! Form::text('name', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('web/pages.name.placeholder') ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('email', Lang::get('web/pages.email').' *') !!}
							{!! Form::email('email', null, [ 'class'=>'form-control required email', 'placeholder'=>Lang::get('web/pages.email.placeholder') ]) !!}
						</div>
						<div class="form-group error-container">
							{!! Form::label('phone', Lang::get('web/pages.phone')) !!}
							@if ( @$page->configuration['contact']['phone_required'] )
								{!! Form::text('phone', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('web/pages.phone.placeholder') ]) !!}
							@else
								{!! Form::text('phone', null, [ 'class'=>'form-control', 'placeholder'=>Lang::get('web/pages.phone.placeholder') ]) !!}
							@endif
						</div>
						<div class="form-group error-container">
							{!! Form::label('body', Lang::get('web/pages.message').' *') !!}
							{!! Form::textarea('body', null, [ 'class'=>'form-control required', 'placeholder'=>Lang::get('web/pages.message.placeholder') ]) !!}
						</div>
						<div class="text-right">
							{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-primary']) !!}
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#contact-form');

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
