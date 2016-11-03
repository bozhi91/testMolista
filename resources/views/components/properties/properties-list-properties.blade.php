<div class="properties-list-properties">
	@if ( count($properties) < 1)
		<div class="alert alert-info">{{ Lang::get('web/properties.empty') }}</div>.
	@else
		@include('web.properties.index-sort')

		<ul class="list-unstyled property-list">
			@foreach ($properties as $property)
				<li>
					@include('web.properties.row', [ 'item'=>$property ])
				</li>
			@endforeach
		</ul>
		<div class="pagination-area text-center">
			{!! $properties->appends( Input::except('page') )->render() !!}
		</div>
	@endif
</div>