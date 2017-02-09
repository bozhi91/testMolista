@if ( !empty($site_setup['widgets']['header']) )
	@foreach ($site_setup['widgets']['header'] as $widget)
		@if ( $widget['type'] == 'menu' )
			@include('common.widget-menu', [
				'widget' => $widget,
				'widget_class' => 'nav navbar-nav header-menu',
			])
		@endif
	@endforeach
@endif