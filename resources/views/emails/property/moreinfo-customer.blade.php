@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('web/properties.moreinfo.email.customer.title', [ 'title'=>$property->title ]) }}</h1>

	<div class="intro">
		{!! Lang::get('web/properties.moreinfo.email.customer.intro', [
			'name' => @$customer->first_name,
			'url' => @$property->full_url,
			'title' => @$property->title,
			'sitename' => @$site->title,
		]) !!}
	</div>

@endsection
