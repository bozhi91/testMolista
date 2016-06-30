<ul class="{{ @$widget_class }}" id="{{ @$widget_id }}">
	@foreach ($widget['items'] as $item)
		<li>
			<a href="{{ $item['url'] }}" target="{{ $item['target'] }}" class="main-item {{ (rtrim(url()->current(),'/') == $item['url']) ? 'current' : '' }}">
				{{ $item['title'] }}
			</a>
		</li>
	@endforeach
</ul>
