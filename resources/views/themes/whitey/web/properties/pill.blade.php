<div class="property-pill">
	<div class="padder">

		<div class="image">
			
			@include('components.properties.pill.properties-pill-image')

			@include('components.properties.pill.properties-pill-discount-label')

			@include('components.properties.pill.properties-pill-labels')

		</div>

		<div class="text">

			@include('components.properties.pill.properties-pill-title')

			@include('components.properties.pill.properties-pill-location')

		</div>

		<div class="price">

			<div class="row">

				<div class="col-xs-6 col-sm-12 col-md-6">
					<div class="price-price pill-price">
						@include('components.properties.pill.properties-pill-price')
					</div>
				</div>

				<div class="col-xs-6 col-sm-12 col-md-6">
					<div class="price-button pill-price">
						@include('components.properties.pill.properties-pill-button')
					</div>
				</div>

			</div>

		</div>
	</div>
</div>

<script type="text/javascript">

	ready_callbacks.push(function(){
		$('.property-pill .padder .text').matchHeight({ byRow : false });
		$('.property-pill .padder .price .pill-price').matchHeight({ byRow : false });
	});

</script>