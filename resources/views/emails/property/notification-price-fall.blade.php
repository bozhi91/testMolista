@extends('layouts.email')

@section('content')

	<div class="container">
		<div class="content">			
			<p>{{ Lang::get('account/properties.email.price.fall.intro', ['name' => $name]) }}</p>
			<p>{!! Lang::get('account/properties.email.price.fall', [
				'title' => "<a href='".$url."'>$title</a>", 'reference' => $reference, 'current' => $current ]) !!}</p>
		</div>
	</div>

@endsection