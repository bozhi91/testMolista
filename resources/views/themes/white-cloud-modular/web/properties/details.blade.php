@extends('layouts.web', [
	'menu_section' => 'properties',
	'use_google_maps' => true,
])

@section('content')

	<div id="property">

		<div class="container">

			<div class="header hidden-xs">
				<div class="row">
					<div class="col-xs-12 col-sm-8">
						<div class="modular-property-title">
							@include('components.property.property-title')
						</div>
						<div class="modular-property-price">
							@include('components.property.property-price')
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="modular-property-more-info-button">
							@include('components.property.property-moreinfo-button')
						</div>
					</div>
				</div>
			</div>

			<div class="header-xs visible-xs">
				<div class="row">
					<div class="col-xs-12">
						<div class="modular-property-title">
							@include('components.property.property-title')
						</div>
					</div>
					<div class="col-xs-12">
						<div class="modular-property-location">
							@include('components.property.property-location')
						</div>
					</div>
					<div class="col-xs-12">
						<div class="modular-property-price">
							@include('components.property.property-price')
						</div>
					</div>
				</div>
			</div>

			<div class="content">
				
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-7 col-lg-8">
						
						@include('components.property.property-image-slider')

						@include('components.property.htmlArea', [ 'property'=>$property ] )
						@include('components.property.property-view-in-3d-iframe')
						
					</div>
					<div class="col-xs-12 col-sm-6 col-md-5 col-lg-4">

						<div class="white-cloud-property-right-block">
							
							<div class="white-cloud-modular-location hidden-xs">
							@include('components.property.property-location')
							</div>

							<div class="white-cloud-modular-description">
							@include('components.property.property-description')
							</div>

							<div class="white-cloud-modular-services">
							@include('components.property.property-services')
							</div>

							<div class="white-cloud-modular-metrics">
							@include('components.property.property-metrics')
							</div>

							<div class="white-cloud-modular-energy">
							@include('components.property.property-energy-certification')
							</div>

							<div class="white-cloud-modular-pdf">
							@include('components.property.property-download-pdf')
							</div>

							<div class="visible-xs white-cloud-modular-moreinfo-button">
							@include('components.property.property-moreinfo-button')
							</div>
							
							<div class="white-cloud-modular-share-button">
							@include('components.property.property-share-button')
							</div>

							<div class="white-cloud-modular-view-in-3d">
							@include('components.property.property-view-in-3d-button')
							</div>

						</div>

					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						@include('components.property.property-map-area')
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 col-sm-12">
						@include('components.property.property-related-properties' , [ 'related_properties'=>$property->related_properties ] )
					</div>
				</div>

			</div>


		</div>

	</div>

	@include('components.property.property-script')

@endsection
