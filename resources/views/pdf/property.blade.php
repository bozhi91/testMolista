<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
		@font-face { 
			font-family: 'Montserrat'; 
			font-style: normal; 
			font-weight: 400;
			src: url('{{ public_path("fonts/montserrat-normal-normal.ttf") }}') format('truetype');
		}
		html, body { margin: 0px; padding: 0px; }
		body { font-family: 'Montserrat', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.2em; color: #1c1c1b; }
		table { width: 100%; border-spacing: 0; border-collapse: collapse; }
			table td { padding: 0px; vertical-align: top; }
		.content { width: 520px; margin: 0px auto; }
		.header { }
			.header td { vertical-align: middle; }
			.header .city-title { background: #26af8d; max-width: 320px; }
			.header .city { padding: 40px 20px 5px 20px; color: #fff; font-size: 18px; }
			.header .title { padding: 0px 20px 40px 20px; color: #fff; font-size: 24px; }
			.header .mode-price { background: #000000; min-width: 200px; }
			.header .mode { padding: 40px 20px 5px 20px; color: #838383; font-size: 18px; text-transform: uppercase; }
			.header .price { padding: 0px 20px 40px 20px; color: #fff; font-size: 24px; }
		.main-image { max-height: 325px; text-align: center; overflow: hidden; margin-bottom: 20px; }
			.main-image img { width: 100%; }
		.details { }
			.details .images { width: 140px; }
			.details .image { max-height: 93px; overflow: hidden; text-align: center; margin-bottom: 20px; padding-right: 15px; }
			.details .image img { width: 100%; }
		.info { font-size: 12px; line-height: 1em; }
		.facts { font-weight: 700; margin-bottom: 15px; }
		.services { color: #999; margin-bottom: 15px; }
		.certificate-image { padding-top: 5px; }
		.qr { text-align: right; }
		.footer { position: absolute; bottom: 0px; left: 0px; width: 100%; background: #26af8d; padding: 25px 0px 35px 0px; }
		.contact { font-size: 11px; color: #fff; line-height: 1em; }
			.contact .realtor-name { font-weight: 700; }
			.contact .powered-logo { font-size: 8px; width: 95px; vertical-align: bottom; }
	</style>
</head>

<body>

	<div class="page">

		<div class="content">

			<table class="header">
				<tr>
					<td class="city-title">
						<div class="city">{{ $property->city->name }}</div>
						<div class="title">{{ $property->title }}</div>
					</td>
					<td class="mode-price">
						<div class="mode">{{ Lang::get("pdf.property.mode.{$property->mode}") }}</div>
						<div class="price">{{ price($property->price, [ 'decimals'=>0 ]) }}</div>
					</td>
				</tr>
			</table>

			<div class="main-image">
				@if ( @$main_image )
					<img src="{{ "{$property->image_path}/{$main_image->image}" }}" />
				@endif
			</div>

			<table class="details">
				<tr>
					<td class="images">
						@foreach ($other_images as $image)
							<div class="image">
								<img src="{{ "{$property->image_path}/{$image->image}" }}" />
							</div>
						@endforeach
					</td>
					<td class="info">
						<div class="facts">
							<div>{{ number_format($property->size,0,',','.') }} m²</div>
							<div>
								{{ number_format($property->rooms,0,',','.') }} 
								@if ($property->rooms == 1)
									{{ Lang::get('web/properties.more.room') }}
								@else
									{{ Lang::get('web/properties.more.rooms') }}
								@endif
							</div>
							<div>
								{{ number_format($property->baths,0,',','.') }}
								@if ($property->baths == 1)
									{{ Lang::get('web/properties.more.bath') }}
								@else
									{{ Lang::get('web/properties.more.baths') }}
								@endif
							</div>
							<div>
								{{ number_format(round($property->price/$property->size),0,',','.') }} 
								{{ price_symbol($property->currency) }}/m²
							</div>
							<div>{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}</div>
						</div>
						<div class="services">
							{{ $property->services->sortBy('title')->implode('title',', ') }}
						</div>
						<div class="certificate-text">
							{{ Lang::get('account/properties.energy.certificate') }}:
							@if ( $property->ec_pending )
								{{ Lang::get('account/properties.energy.certificate.pending') }}
							@else
								{{ $property->ec }}
							@endif
						</div>
						<table class="certificate-qr">
							<tr>
								<td class="certificate">
									<div class="certificate-image">
										<img src="{{ public_path('images/pdf/certificate.png') }}" />
									</div>
								</td>
								<td class="qr">
									<img src="{{ $property->getQrFile(App::getLocale()) }}" />
								</td>
						</table>
					</td>
				</tr>
			</table>

		</div>

		<div class="footer">
			<div class="content">
				<table class="contact">
					<tr>
						<td class="realtor">
							@if ( @$property->site->signature['name'] )
								<div class="realtor-name">{{ $property->site->signature['name'] }}</div>
							@endif
							@if ( @$property->site->signature['address'] )
								<div class="realtor-address">{{ $property->site->signature['address'] }}</div>
							@endif
							@if ( @$property->site->signature['phone'] || @$property->site->signature['email'] )
								<div class="realtor-phone-email">{{ implode(' | ', array_filter([
									@$property->site->signature['phone'],
									@$property->site->signature['email'],
								])) }}</div>
							@endif
							<div class="realtor-url">{{ $property->site->main_url }}</div>
						</td>
						<td class="powered-logo">
							<div class="powered">Powered by</div>
							<div class="logo">
								<img src="{{ public_path('images/pdf/molista.png') }}" />
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>

</body>
</html>