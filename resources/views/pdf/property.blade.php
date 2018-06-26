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

		@page { size: 595px 806px; }

		{!! $css !!}

		.description { line-height: 1.2em !important }
	</style>
</head>

<body id="pdf-property">

	<div class="page">

		<div class="header">
			<div class="container">
				<table>
					<tr>
						<td class="header-logo">
							@if ( $property->site->logo )
								<img src="{{ public_path("sites/{$property->site->id}/{$property->site->logo}") }}" alt="" style="max-width: 300px">
							@endif
						</td>
						<td class="header-data">
							@if ( @$property->site->signature['phone'] || @$property->site->signature['email'] )
								<p>{{ implode(' | ', array_filter([
									@$property->site->signature['phone'],
									@$property->site->signature['email'],
								])) }}</p>
							@endif
							@if ( @$property->site->main_url )
								<p>{{ preg_replace('#^(https?://)#', '', $property->site->main_url) }}</p>
							@endif
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="container">
			<div class="main-image">
				@if ( @$main_image )
					<img src="{{ $main_image }}" />
				@endif
			</div>
		</div>

		<div class="container">
			<div class="secondary-images">
				<table>
					<tr>
						<td>
							<div class="image">
								@if ( @$other_images[0] )
									<img src="{{ $other_images[0] }}" />
								@endif
							</div>
						</td>
						<td class="sep"></td>
						<td>
							<div class="image">
								@if ( @$other_images[1] )
									<img src="{{ $other_images[1] }}" />
								@endif
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="title-price">
			<table>
				<tr>
					<td class="city-title">
						<div class="text">
							<div class="city">{{ $property->city->name }}</div>
							<div class="title">{{ $property->title }}</div>
						</did>
					</td>
					<td class="sep"></td>
					<td class="mode-price">
						<div class="text">
							<div class="mode">{{ Lang::get("pdf.property.mode.{$property->mode}") }}</div>
							<div class="price">{{ price($property->price, $property->infocurrency->toArray()) }}</div>
						</did>
					</td>
				</tr>
			</table>
		</div>


		<div class="container">
			<table class="details">
				<tr>
					<td class="qr-area">
						@if ($plan == 7 || $plan == 9)
							<img src="{{ $property->getQrFile(App::getLocale()) }}" />
						@endif
					</td>
					<td class="sep"></td>
					<td class="info">
						<table>
							<tr>
								<td>
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
											{{ @number_format(round($property->price/$property->size),0,',','.') }}
											{{ $property->infocurrency->symbol }}/m²
										</div>
										<div>{{ Lang::get('account/properties.ref') }}: {{ $property->ref }}</div>
									</div>
									<div class="certificate-text">
										{{ Lang::get('account/properties.energy.certificate') }}:
										@if ( $property->ec_pending )
											{{ Lang::get('account/properties.energy.certificate.pending') }}
										@else
											{{ $property->ec }}
										@endif
									</div>
								</td>
								<td class="certificate">
									<div class="certificate-image">
										<img src="{{ public_path('images/pdf/certificate.png') }}" />
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>

		@if ( $property->services->count() > 0 )
			<div class="container">
				<table class="services">
					<tr>
						<td>
							{{ $property->services->sortBy('title')->implode('title',', ') }}
						</td>
					</tr>
				</table>
			</div>
		@endif

		@if ( $property->description && $property->site->pdf_extended )
		<div class="container">
			<table class="description services">
				<tr>
					<td>
						{!! nl2p($property->description) !!}
					</td>
				</tr>
			</table>
		</div>
		@endif

		<div class="footer">
			<div class="container">
				<table class="contact">
					<tr>
						<td class="realtor">
							@if ( @$property->site->signature['name'] )
								<div class="realtor-name">{{ $property->site->signature['name'] }}</div>
							@endif
							@if ( @$property->site->signature['address'] )
								<div class="realtor-address">{{ $property->site->signature['address'] }}</div>
							@endif
						</td>
						<td class="powered-logo">
							@if (!$property->site->hide_molista)
							<div class="powered">Powered by</div>
							<div class="logo">
								<img src="{{ public_path('images/pdf/molista.png') }}" />
							</div>
							@endif
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>

</body>
</html>
