@extends('layouts.corporate')

@section('content')

	<div id="contact" class="contact">
		<div class="container">
<h1>contact</h1>

@include('common.messages')

{!! Form::model(null, [
	'action'=>'Corporate\InfoController@postContact',
	'method'=>'POST',
	'id'=>'contact-form'
]) !!}
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('name', 'Nombre') !!}
			{!! Form::text('name', null, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="text-right">
			{!! Form::button('Enviar', [ 'type'=>'submit', 'class'=>'btn btn-primary' ]) !!}
		</div>
	</div>
</div>

{!! Form::close() !!}

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#contact');
			var form = $('#contact-form');

			form.validate({
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				}
			});
		});
	</script>

@endsection
