<li class="handler ui-sortable-handle">
	<div class="property-video-container">
		<a href="{{ $video->image_url }}" data-link='{{ $video->link }}' target="_blank" class="thumb"
		   style="background-image: url('{{ $video->image_url }}')"></a>
	</div>
	<div class="options text-right">
		<a href="#" class="video-delete-trigger" data-action="{{ action('Account\PropertiesController@deleteVideo', [
				'property_id' => $property_id, 'video_id' => $video->id]) }}">
			<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
		</a>
	</div>
</li>
