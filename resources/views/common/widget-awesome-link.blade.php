<a href="{{ $widget['content'] }}" class="quick-link" style='background: {{ $widget['data']['color'] }}'>
	@if(isset($widget['data']['image']))
		<div class="image" style='background-image: url({{$widget['data']['image']}})'></div>
	@endif
	<div class="text">{{ $widget['title'] }}</div>
	<div class="arrow">
		<span>&rsaquo;</span>
	</div>
</a>

