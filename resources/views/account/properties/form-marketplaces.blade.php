@if ( !empty($marketplaces) && $marketplaces->count() > 0 )
	<p>{{ Lang::get('account/properties.marketplaces.intro') }}</p>
	<br />
	<div class="table-responsive">
		<table class="table table-striped">
			<tbody>
				@foreach ($marketplaces as $marketplace)
					<?php
						$marketplace_published = @$item->marketplaces_ids[$marketplace->id] ? true : false;
						$marketplace_items_max = intval($marketplace->pivot->marketplace_maxproperties);
						$marketplace_items_current = $marketplace->properties->count();
						if ( !$marketplace_published && $marketplace_items_max > 0 && $marketplace_items_max <= $marketplace_items_current )
						{
							$publishable = [ 
								Lang::get('account/marketplaces.maxproperties.error', [
									'maxproperties' => number_format($marketplace_items_max,0,',','.'),
								]),
							];
						}
						else
						{
							$publishable = $current_site->marketplace_helper->checkReadyProperty($marketplace,$item);
						}
					?>
					<tr>
						<td class="text-center" style="width: 60px;">
							@if ( $publishable === true )
								{!! Form::checkbox("marketplaces_ids[{$marketplace->id}]", $marketplace->id) !!}
							@endif
						</td>
						<td>
							<span class="marketplace-name text-nowrap;" style="background-image: url({{ asset("marketplaces/{$marketplace->logo}") }});">{{ $marketplace->name }}</span>
						</td>
						<td>
							@if ( $publishable === true )
							@else
								{{ Lang::get('account/properties.marketplaces.error') }}<br />
								<ul>
									@foreach ($publishable as $key => $message)
										<li>{{ $message }}</li>
									@endforeach
								</ul>
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif