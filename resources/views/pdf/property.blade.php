@extends('layouts.pdf')

@section('content')

	<h1>{{$property->title}}</h1>
	<div class="location block">
		{{ implode(', ', array_filter([
			'district' => $property->district,
			'city' => $property->city->name,
			'state' => $property->state->name,
		])) }}
	</div>

	<div class="description block">
		{!! nl2p($property->description) !!}
	</div>

	<div class="price block">
		<h2 style="margin-bottom: 0px;">{{ Lang::get('account/properties.price') }}: {{ price($property->price, [ 'decimals'=>0 ]) }}</h2>
		<p>{{ number_format(round($property->price/$property->size),0,',','.') }} €/m²</p>
	</div>

	<div class="features block">
		<h2>{{ Lang::get('web/properties.features') }}</h2>
		<ul>
			<li>
				{{ Lang::get('account/properties.size') }}:
				{{ number_format($property->size,0,',','.') }} m²
			</li>
			<li>
				{{ number_format($property->rooms,0,',','.') }} 
				@if ($property->rooms == 1)
					{{ Lang::get('web/properties.more.room') }}
				@else
					{{ Lang::get('web/properties.more.rooms') }}
				@endif
			</li>
			<li>
				{{ number_format($property->baths,0,',','.') }}
				@if ($property->baths == 1)
					{{ Lang::get('web/properties.more.bath') }}
				@else
					{{ Lang::get('web/properties.more.baths') }}
				@endif
			</li>
			@if ( $property->ec || $property->ec_pending )
				<li>
					{{ Lang::get('account/properties.energy.certificate') }}:
					@if ( $property->ec_pending )
						{{ Lang::get('account/properties.energy.certificate.pending') }}</span>
					@else
						{{ $property->ec }}
					@endif
				</li>
			@endif
			@if ( $property->services->count() > 0 )
				@foreach ($property->services->sortBy('title') as $service)
					<li>{{ $service->title }}</li>
				@endforeach
			@endif
		</ul>
	</div>

	@if ( $property->images->count() > 1 )
		<div class="images block">
			@foreach ($property->images->sortBy('position') as $image)
				<p>
					<img src="{{ "{$property->image_path}/{$image->image}" }}" />
				</p>
			@endforeach
		</div>
	@endif

@endsection
