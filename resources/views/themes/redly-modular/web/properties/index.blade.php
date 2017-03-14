@extends('layouts.web', [
	'menu_section' => 'properties',
])

@section('content')

	<div id="properties">

		<div class="search-area">
			<div class="container">

				@include('components.properties.properties-results-label')

				@include('components.properties.properties-form-area')

			</div>
		</div>

		<div class="results-area">
			<div class="container">

				@include('components.properties.properties-list-properties')

			</div>
		</div>

	</div>

	@include('components.properties.properties-script')

@endsection
