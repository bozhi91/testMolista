@if ( $item->label)
	<div class="labels">
		<span class="label" style="background-color: {{ $item->label_color }};">{{ $item->label }}</span>
	</div>
@endif