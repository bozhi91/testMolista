<div class="property-image-slider">
	@if ( $property->images->count() > 0 )
		<div class="images-gallery">
			@include('web.properties.discount-label', [ 'item' => $property ])
			<div class="image-main text-center">
				<img src="{{ $property->main_image }}" alt="{{$property->title}}" class="img-responsive cursor-pointer trigger-image-thumbs" id="property-main-image" />
			</div>
			@if ( $property->images->count() > 1 )
				<div id="images-carousel" class="images-carousel carousel slide" data-interval="false">
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="row">
								@foreach ($property->images->sortBy('position')->values() as $key => $image)
									@if ( $key > 0 && $key%6 < 1 )
										</div></div><div class="item"><div class="row">
									@endif
									<div class="col-xs-4 col-sm-2">
										<a href="{{ $image->image_url }}" class="image-thumb" style="background-image: url('{{ $image->image_url_thumb }}');">
											<img src="{{ $image->image_url_thumb }}" alt="{{$property->title}}" class="hide" />
										</a>
									</div>
								@endforeach
							</div>
						</div>
					</div>
					<a class="left carousel-control hide" href="#images-carousel" role="button" data-slide="prev">
						&lsaquo;
					</a>
					<a class="right carousel-control hide" href="#images-carousel" role="button" data-slide="next">
						&rsaquo;
					</a>
				</div>
			@endif
		</div>
	@endif
</div>