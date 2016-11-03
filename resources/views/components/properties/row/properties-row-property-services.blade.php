<div class="services text-italic">
	{{ $item->services->sortBy('title')->implode('title',', ') }}
</div>