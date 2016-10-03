<?php
	$themes = [];
	$theme_current = false;
	foreach (Config::get('themes.themes') as $theme => $def)
	{
		$def['theme'] = $theme;
		if ( $theme == $site->theme )
		{
			$theme_current = $def;
		}
		elseif ( @$def['public'] || $theme == $site->custom_theme )
		{
			$themes[$theme] = $def;
		}
	}

	ksort($themes);
	$themes = array_values($themes);

	if ( $theme_current )
	{
		array_unshift($themes, $theme_current);
	}

	$themes_total = count($themes);
	$themes_per_page = 9;
	$themes_pages_counter = 0;
	$themes_pages_total = ceil(count($themes)/$themes_per_page);
	$themes_pages_from = $themes_pages_counter * $themes_per_page + 1;
	$themes_pages_to = $themes_pages_counter * $themes_per_page + $themes_per_page;
	if ( $themes_pages_to > $themes_total )
	{
		$themes_pages_to = $themes_total;
	}

	$locale = app()->getLocale();
	$currency = strtolower($current_site->site_currency);
	$banner_image = "images/banners/theme-custom-{$locale}-{$currency}.jpg";
?>

<div class="error-container theme-select-area">
	<label>{{ Lang::get('account/site.configuration.theme') }}</label>
	<div id="theme-carousel" class="carousel">

		<div class="carousel-inner" role="listbox">
			<div class="item active" data-page="{{ $themes_pages_counter }}" data-showing="{{ Lang::get('account/site.configuration.theme.pages.showing', [ 'from'=>$themes_pages_from, 'to'=>$themes_pages_to, 'total'=>$themes_total ]) }}">
				<div class="row">
					@foreach ($themes as $key => $def)
						@if ( $key > 0 && $key%$themes_per_page == 0 )
							<?php 
								$themes_pages_counter++; 
								$themes_pages_from = $themes_pages_counter * $themes_per_page + 1;
								$themes_pages_to = $themes_pages_counter * $themes_per_page + $themes_per_page;
								if ( $themes_pages_to > $themes_total )
								{
									$themes_pages_to = $themes_total;
								}
							?>
							</div></div>
							<div class="item" data-page="{{ $themes_pages_counter }}" data-showing="{{ Lang::get('account/site.configuration.theme.pages.showing', [ 'from'=>$themes_pages_from, 'to'=>$themes_pages_to, 'total'=>$themes_total ]) }}"><div class="row">
						@endif
						<div class="col-xs-12 col-sm-4">
							<div class="theme-pill">
								<div class="theme-pill-preview" style="background-image: url('{{ asset("images/themes/{$def['theme']}/previews/home.jpg") }}');"></div>
								<div class="theme-pill-title">{{ empty($def['title']) ? ucfirst($def['theme']) : $def['title'] }}</div>
								<ul class="list-inline theme-pill-previews">
									<li>
										<a href="{{ asset("images/themes/{$def['theme']}/previews/home.jpg") }}" class="theme-pill-preview-trigger">
											<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
											{{ Lang::get('account/site.configuration.theme.preview.home') }}
										</a>
									</li>
									<li>
										<a href="{{ asset("images/themes/{$def['theme']}/previews/product.jpg") }}" class="theme-pill-preview-trigger">
											<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
											{{ Lang::get('account/site.configuration.theme.preview.product') }}
										</a>
									</li>
								</ul>
								<div class="theme-pill-button">
									<label class="btn btn-primary">
										{!! Form::radio('theme', $def['theme'], null, [ 'class'=>'theme-radio-input required', 'style'=>'opacity: 0; display: none;' ]) !!}
										{{ Lang::get('account/site.configuration.theme.install') }}
									</label>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-8">
				@if ( $themes_pages_total > 1 )
					<ul class="list-inline theme-pages">
						<li data-target="#theme-carousel" data-slide="prev"><</li>
						@for ($i=0; $i<$themes_pages_total; $i++)
							<li data-target="#theme-carousel" data-slide-to="{{ $i }}" class="theme-pages-number {{ $i ? '' : 'active' }}">{{ $i+1 }}</li>
						@endfor
						<li data-target="#theme-carousel" data-slide="next">></li>
					</ul>
				@endif
			</div>
			<div class="col-xs-12 col-sm-4 text-right hidden-xs">
				<span class="theme-pages-showing"></span>
			</div>
		</div>

		@if ( env('WHITELABEL_MOLISTA', false) && file_exists( public_path($banner_image) ) )
			<div class="theme-banner">
				<a href="mailto:ib@molista.com" target="_blank"><img src="{{ asset($banner_image) }}" alt="" class="img-responsive" /></a>
			</div>

		@endif


	</div>
</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var carousel = $('#theme-carousel');

			// Theme preview
			carousel.find('.theme-pill-preview-trigger').each(function(){
				$(this).magnificPopup({
					type: 'image',
					closeOnContentClick: false,
					mainClass: 'mfp-img-mobile',
					image: {
						verticalFit: true
					}
				});
			});

			// Mark pill as current
			carousel.find('.theme-radio-input').on('change', function(){
				carousel.find('.theme-pill').removeClass('current');
				$(this).closest('.theme-pill').addClass('current');
			}).filter(':checked').eq(0).trigger('change');

			// Pagination callback
			carousel.on('slid.bs.carousel', function () {
				carousel.find('.theme-pages-number').removeClass('active');

				var cur = carousel.find('.carousel-inner .item.active');
				if ( cur.length > 0 ) {
					carousel.find('.theme-pages-showing').show().text( cur.data().showing );
					carousel.find('.theme-pages-number[data-slide-to='+cur.data().page+']').addClass('active');
				} else {
					carousel.find('.theme-pages-showing').hide();
				}
			});

			// Show first
			carousel.trigger('slid.bs.carousel');

		});
	</script>
