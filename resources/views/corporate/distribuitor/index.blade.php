@extends('layouts.corporate')

@section('content')

<div id="distribuitors-page" >
	<div class="container">
		<div class="row">
			<div class="col-sm-2 text-center">
				<img src="{{ asset("images/corporate/sello.png") }}" />
			</div>
			<div class="title-block col-sm-10">
				<h1>{{ Lang::get('corporate/home.distributor.title') }}</h1>

				<div class="row">
					<p class="col-xs-8">
						{!! Lang::get('corporate/home.distributor.description2', ['email' => env('MAIL_CONTACT', 'admin@molista.com')]) !!}
					</p>
				</div>

				{!! Form::model(null, [
				'action'=>'Corporate\DistribuitorController@postContact',
				'method'=>'POST',
				'id'=>'distribuitors-form'
				]) !!}

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('name', Lang::get('corporate/home.distributor.label.name')) !!}
							{!! Form::text('name', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('company', Lang::get('corporate/home.distributor.label.company')) !!}
							{!! Form::text('company', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('email', Lang::get('corporate/home.distributor.label.email')) !!}
							{!! Form::text('email', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('phone', Lang::get('corporate/home.distributor.label.phone')) !!}
							{!! Form::text('phone', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('workers', Lang::get('corporate/home.distributor.label.workers')) !!}
							<br />
							
							{!! Form::select('workers', 
								[
									'1 - 10' => '1 - 10',
									'11 - 50' => '11 - 50',
									'51 - 100' => '51 - 100',
									'+ 100' => '+ 100',
								], null,
								[ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<div class="form-group error-container">
							{!! Form::label('message', Lang::get('corporate/home.distributor.label.message')) !!}
							{!! Form::textarea('message', null, [ 'class'=>'form-control required' ]) !!}
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-5">
						<button type="submit" class="btn text-uppercase btn-distributor">
							{{ Lang::get('general.send') }}
						</button>
					</div>
				</div>

				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
</div>
</div>

<script type="text/javascript">
    ready_callbacks.push(function () {
        var cont = $('#distribuitors-page');
    });
</script>

@endsection
