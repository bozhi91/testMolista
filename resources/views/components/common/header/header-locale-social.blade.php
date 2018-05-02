
<!-- Mostrar el plan actual y un boton para actualizar el plan. -->
<?php $plan= App\Http\Controllers\Account\ReportsController::getPlan();?>
<?php $protocol =isset($_SERVER['HTTPS']) ? 'https://' : 'http://';?>

<div class="row">
	<div class="col-sm-4"></div>
	<div class="col-sm-4"></div>

	<div class="col-sm-4">
		<h4 id="planTag" style="margin-bottom:0px; margin-top:0px;">
			<p>Plan actual:<b><?php echo  $plan;?></b>
				<a href="/account/payment/upgrade" target='_blank'>
					<button type="button" class="btn btn-info">Actualizar</button>
				</a></p>
		</h4>
	</div>
</div>

<ul class="nav navbar-nav navbar-right header-locale-social">
	@if ( !empty($site_setup['locales_select']) )

		<li class="dropdown locale-select">
			<a href="#" class="main-item dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
				<span class="locale-placeholder">
					{{ Lang::get('web/header.lang') }} <span class="caret"></span>
					<span class="locale-line">
				</span> 
			</a>
			<ul class="dropdown-menu">
				@foreach($site_setup['locales_select'] as $locale => $locale_name)
					<li><a href="{{ ( $locale == LaravelLocalization::getCurrentLocale() ) ? 'javascript:;' : LaravelLocalization::getLocalizedURL($locale) }}">{{ $locale_name }}</a></li>
				@endforeach
			</ul>
		</li>
	@endif
	@if ( !empty($site_setup['social_media']) )
		<li class="social-media">
			<ul class="list-inline">
				@foreach ($site_setup['social_media'] as $key=>$value)
					<li>
						<a href="{{ $value }}" class="social-media-item" target="_blank">
							<i class="fa fa-{{$key}}" aria-hidden="true"></i>
						</a>
					</li>
				@endforeach

			</ul>

		</li>
	@endif
</ul>
