@extends('layouts.admin')

@section('content')

	<style type="text/css">
		.site-link { color: #333333; font-size: 18px; }
	</style>

	<div class="container">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="list-title">{{ Lang::get('admin/menu.reports.themes') }}</h1>

		<div class="row">
			@foreach ($themes as $theme)
				<div class="col-sm-4 col-md-3">
					<div class="panel panel-default text-center">
						<div class="panel-heading">{{ $theme['title'] }}</div>
						<div class="panel-body">
							<a href="{{ action('Admin\SitesController@index', [ 'theme'=>$theme['key'] ]) }}" class="site-link">{{ @number_format($theme['total'], 0, ',', '.') }}</a>
						</div>
					</div>
				</div>
			@endforeach
		</div>

	</div>

@endsection