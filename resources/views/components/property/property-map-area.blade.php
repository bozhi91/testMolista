<div class="property-map-area">
	<div class="map-area">
		@if ( $property->show_address )
			<div class="visible-address">
				{!! $property->full_address !!}
			</div>
		@endif
		<div id="property-map" class="map"></div>
	</div>
</div>

<script type="text/javascript">
	google.maps.event.addDomListener(window, 'load', function(){
		var mapLatLng = { lat: {{$property->lat_public}}, lng: {{$property->lng_public}} };

		var property_map = new google.maps.Map(document.getElementById('property-map'), {
			zoom: 14,
			center: mapLatLng,
			styles: {!! Theme::config('gmaps-style') !!}
		});

		@if ( @$property->show_address )
			var property_marker = new google.maps.Marker({
				position: mapLatLng,
				map: property_map,
				icon: '{{ asset( Theme::config('gmaps-marker') ) }}'
			});
		@else
			var property_marker = new google.maps.Circle({
				strokeColor: '{{ Theme::config('gmaps-circle') }}',
				strokeOpacity: 0.8,
				strokeWeight: 0,
				fillColor: '{{ Theme::config('gmaps-circle') }}',
				fillOpacity: 0.25,
				map: property_map,
				center: mapLatLng,
				radius: 500
			});
		@endif
	});
</script>