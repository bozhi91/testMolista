<div class="services property-services">
	{{ $property->services->sortBy('title')->implode('title',', ') }}
</div>