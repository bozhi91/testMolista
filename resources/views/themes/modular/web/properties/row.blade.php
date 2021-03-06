<div class="property-row">
	<div class="row">
		<div class="col-xs-12 col-sm-3">
			<div class="property-column image-column">

				@include('components.properties.row.properties-row-discount-label')

				@include('components.properties.row.properties-row-property-image')

				@include('components.properties.row.properties-row-property-price')

			</div>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="property-column text-column">
				<div class="row">
					<div class="col-xs-12 col-sm-9">

						@include('components.properties.row.properties-row-property-title')

						@include('components.properties.row.properties-row-property-location')

					</div>

					<div class="col-xs-12 col-sm-3">

						@include('components.properties.row.properties-row-button-more-info')
						
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12 col-sm-12">

						@include('components.properties.row.properties-row-property-description')

					</div>
				</div>

				<div class="row hidden-xs">
					<div class="col-sm-12">

						@include('components.properties.row.properties-row-property-metrics')

						@include('components.properties.row.properties-row-property-services')

					</div>
				</div>
			</div>
		</div>
	</div>
</div>