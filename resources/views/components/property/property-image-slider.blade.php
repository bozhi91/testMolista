@if ( $media->count() > 0 )
	<div class="property-image-slider">
		<div class="images-gallery">
			@include('web.properties.discount-label', [ 'item' => $property ])

			@if($property->main_image)
				<div class="image-main text-center">
					<img src="{{ $property->main_image }}" alt="{{$property->title}}"
						 class="img-responsive cursor-pointer trigger-image-thumbs" id="property-main-image" />
				</div>
			@else
				<div class="image-main text-center">
					@include('web.properties.video', [ 'video'=> $media->first() ])
				</div>
			@endif

			@if ( $media->count() > 1 )
				<div id="images-carousel" class="images-carousel carousel slide" data-interval="false">
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="row">
								@foreach ($media as $key => $media_item)
									@if ( $key > 0 && $key%6 < 1 )
										</div></div><div class="item"><div class="row">
									@endif

									@if($media_item instanceof App\Models\Property\Images)
										<div class="col-xs-4 col-sm-2">
											<?php
                                            	$image =  str_replace("/watermark","", $media_item->image_url);
                                            	$image_tumb =  str_replace("/watermark","", $media_item->image_url_thumb);
											?>
											<a href="{{$image}}"
											   class="image-thumb mfp-image"
											   style="background-image: url('{{$image_tumb}}');">

												<div class="image-thumb-overlay">
													<div class="image-thumb-overlay-icon-container">

														<i class="berlanga-icon-photo"></i>
													</div>
												</div>
                                                <?php
													$image =  str_replace("/watermark","", $media_item->image_url);
													$image_tumb =  str_replace("/watermark","", $media_item->image_url_thumb);
                                                ?>
												<img src="{{ $media_item->image_url_thumb }}" alt="{{$property->title}}" class="hide" />
											</a>
										</div>
									@elseif($media_item instanceof App\Models\Property\Videos)
										<div class="col-xs-4 col-sm-2">
											<a href="{{ $media_item->link }}"
                                               <?php
                                               		$img = str_replace("/watermark","",$media_item->image_url);
                                               ?>
											   class="image-thumb mfp-iframe" style="background-image: url('{{ $img }}');">

												<div class="image-thumb-overlay video">
													<div class="image-thumb-overlay-icon-container">
														<i class="berlanga-icon-video"></i>
													</div>
												</div>

												<img src="{{ $media_item->image_url }}" alt="{{$property->title}}" class="hide" />
											</a>
										</div>
									@endif
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
	</div>
@endif
