<div class="bottom-links quick-search-area">
	<h2>{{ Lang::get('web/properties.related.title') }}</h2>
	<div class="row">
		@foreach ($related_properties as $item)
			<div class="col-xs-12 col-sm-4">
				@include('web.properties.pill', [ 'item'=>$item])
			</div>
		@endforeach
	</div>
</div>
