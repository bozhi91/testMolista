@extends('layouts.email')

@section('content')

	<h1>{{ Lang::get('web/properties.recommendfriend.email.title', [ 'name'=> $name ]) }}</h1>

	<div class="intro">
		{!! Lang::get('web/properties.recommendfriend.email.intro', [
			'name' => $name,
			'email' => $email,
			'url' => $url,
			'message' => $message,
			'title' => @$property->title,
			'sitename' => @$site->title,
		]) !!}
	</div>

@endsection
