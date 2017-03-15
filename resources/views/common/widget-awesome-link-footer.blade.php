@if ($widget['content'])
<a href="{{ $widget['content'] }}">
@endif
	@if(isset($widget['data']['image']))
		<img class="img-responsive" src="{{ $widget['data']['image'] }}">
	@endif
@if ($widget['content'])
</a>
@endif
