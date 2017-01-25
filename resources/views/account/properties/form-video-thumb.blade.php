<li class="handler ui-sortable-handle">
	<div class="property-video-container">
		<a href="{{ $video->image_url }}" data-link='{{ $video->link }}' target="_blank" class="thumb"
		   style="background-image: url('{{ $video->image_url }}')"></a>
	</div>
	<div class="options text-right">
		@if($isCreate)
			<input name="duplicate_videos[]" type="hidden" value="{{ $video->id }}">
		@else
			<input name="videos[]" type="hidden" value="{{ $video->id }}">
		@endif		
		<a href="#" class="video-delete-trigger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
	</div>
</li>
