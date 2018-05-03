<!-- Mostrar el plan actual y un boton para actualizar el plan. -->
@include("layouts.currentPlan")

<nav id="header" class="navbar navbar-default {{ @$header_class }}">
	<div class="container header-container">
		@include('components.common.header.header-logo')
		<div class="collapse navbar-collapse" id="app-navbar-collapse">

			@include('components.common.header.header-main-menu')

			@include('components.common.header.header-search-trigger-xs')

			@include('components.common.header.header-locale-social')

		</div>
	</div>
</nav>