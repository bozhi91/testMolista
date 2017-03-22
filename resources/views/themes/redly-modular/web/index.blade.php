<?php



?>

@extends('layouts.web')

@section('content')

	@include('web.search.home')

	<div id="home">

		<div class="modular-slider">
			@include('components.home.home-slider')
		</div>

		<div class="modular-highlight-properties">
			@include('components.home.home-highlight-properties', ['colperpage' => 4 ])
		</div>

		<div class="modular-home-footer">
			@include('components.home.home-bottom-quick-search-area', [
				'quickSearchAreaTitleClasses' => 'col-xs-12 col-sm-12 col-md-8 Test-Modular-1' ,
			  	'quickSearchTagsClasses' => 'col-xs-12 col-sm-6 Test-Modular-2' ,
			  	'recentlyAddedPropertiesClasses' => 'hidden-xs hidden-sm col-md-4 Test-Modular-3'
			  ])
		</div>

	</div>

	@include('components.home.home-script')

@endsection
