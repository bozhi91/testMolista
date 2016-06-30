@extends('layouts.web')

@section('content')

	<style type="text/css">
		#suspended { padding-top: 50px; padding-bottom: 50px; }
		#suspended .intro { font-size: 18px; padding: 40px 0px; }
	</style>

	<div class="container" id="suspended">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-sm-offset-3">
				<div class="text-center">
					<h1>{{ Lang::get('web/suspended.title') }}</h1>
					<div class="intro">{!! Lang::get('web/suspended.body', [
						'weburl' => env('APP_URL')
					]) !!}</div>
					<a href="{{ action('AccountController@index') }}" class="btn btn-primary">{{ Lang::get('web/suspended.goto.account') }}</a>
				</div>
			</div>
		</div>
	</div>

@endsection
