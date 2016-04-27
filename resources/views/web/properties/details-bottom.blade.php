<div class="bottom-links quick-search-area">
	<div class="row">
		<div class="cols-xs-12 col-sm-9">
			<h2>{{ Lang::get('web/properties.related.title') }}</h2>
			<div class="row">
				@foreach ($property->related_properties as $item)
					<div class="col-xs-12 col-sm-4">
						@include('web.properties.pill', [ 'item'=>$item])
					</div>
				@endforeach
			</div>
		</div>
		<div class="cols-xs-12 col-sm-3">
			@include('web.search.quick')
		</div>
	</div>
</div>
