@if ( !empty($marketplaces) && $marketplaces->count() > 0 )

	<div class="export-to-all-container">
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('export_to_all', Lang::get('account/properties.marketplaces.toall')) !!}
					{!! Form::select('export_to_all', [
						0 => Lang::get('general.no'),
						1 => Lang::get('general.yes'),
					], null, [ 'class'=>'form-control export-to-all-input' ]) !!}
				</div>
			</div>
		</div>
	</div>
	<div class="marketplaces-container" style="position: relative;">

		<hr />
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
							$marketplace_checkbox_attr = [ 'class' => 'marketplace-input' ];
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

							// If not publishable, disabled
							if ( $publishable !== true )
							{
								$marketplace_checkbox_attr['class'] .= ' marketplace-input-unpublished';
								$marketplace_checkbox_attr['disabled'] = 'disabled';
							}

							// If export to all, checked
							if ( @$item->export_to_all )
							{
								$marketplace_published = true;
								$marketplace_checkbox_attr['disabled'] = 'disabled';
							}

							// Get marketplace attributes
							$attributes = $current_site->marketplace_helper->getAttributes($marketplace);
						?>
						<tr>
							<td class="text-center" style="width: 60px;">
								@if ( $marketplace->pivot->marketplace_export_all )
									@if ( $marketplace_published )
										{!! Form::hidden("marketplaces_ids[{$marketplace->id}]", $marketplace->id) !!}
									@endif
									{!! Form::checkbox('null', 1, true, [ 'disabled'=>'disabled' ]) !!}
								@else
									{!! Form::checkbox("marketplaces_ids[{$marketplace->id}]", $marketplace->id, $marketplace_published, $marketplace_checkbox_attr) !!}
								@endif
							</td>
							<td>
								<span class="marketplace-name text-nowrap;" style="background-image: url({{ asset("marketplaces/{$marketplace->logo}") }});">{{ $marketplace->name }}</span>
							</td>
							<td>
								@if (!empty($attributes))
									@foreach ($attributes as $attribute)
									<div class="form-group error-container">
										<label>{{ \Lang::get('account/properties.attributes.'.$attribute['id']) }}</label>
										@if ($attribute['type'] == 'dropdown')
										<select class="form-control" name="marketplace_attributes[{{ $marketplace['id'] }}][{{ $attribute['id'] }}]">
											<option></option>
											@foreach ($attribute['values'] as $value)
											<option value="{{ $value['id'] }}" <?php echo $value['id'] == @$item['marketplace_attributes'][$marketplace['id']][$attribute['id']] ? 'selected="selected"' :'' ?>>{{ $value['label'] }}</option>
											@endforeach
										</select>
										@endif
									</div>
									@endforeach

									<hr>
								@endif

								@if ( $publishable === true )
									@if ( $marketplace->pivot->marketplace_export_all )
										{{ Lang::get('account/marketplaces.export_all.warning') }}
									@endif
								@else
									<div class="not-published-rel">
										{{ Lang::get('account/properties.marketplaces.error') }}<br />
										@if (is_array($publishable))
										<ul>
											@foreach ($publishable as $key => $message)
												<li>{{ translate_marketplace_error($message) }}</li>
											@endforeach
										</ul>
										@endif
									</div>
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<div class="marketplaces-overlay hide" style="position: absolute; top: 0px; left: 0px; height: 100%; width: 100%; background: #fff; opacity: 0.5;"></div>

	</div>

@endif
