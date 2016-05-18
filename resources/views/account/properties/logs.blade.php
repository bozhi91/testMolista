@if ( $logs->count() > 0 )
	@foreach ($logs as $key=>$log)
		<?php
			if ( empty($locale) )
			{
				$log_title = Lang::get("account/properties.logs.type.{$log->type}");
				$sort_value = ( $log->type == 'created' ) ? 0 : $log->created_at->format('YmdHis') - 5;
			}
			else
			{
				$log_title = Lang::get("account/properties.logs.type.updated") . " <span class=\"text-lowercase\">({$locale})</span>";
				$sort_value = $log->created_at->format('YmdHis');
			}
		?>
		<tr class="logs-row">
			<td data-date="{{ $sort_value }}">{{ $log->created_at->format("d/m/Y H:i" )}}</td>
			<td>{{ @$log->user->name ? $log->user->name : Lang::get('account/properties.logs.responsible.unknown') }}</td>
			<td>
				{!! $log_title !!}
				@if ( $log->type == 'updated' || !empty($locale) )
					<a href="#popup-log-{{$log->id}}" class="popup-log-trigger hidden-xs" title="{{ Lang::get('account/properties.logs.view') }}"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
					<div id="popup-log-{{$log->id}}" class="app-popup-block-white app-popup-block-large mfp-hide">
						<p><strong>{{ @$log->user->name ? "{$log->user->name} - " : '' }}{!! $log_title !!}</strong></p>
						<div class="log-detail">
							<ul class="log-detail-empty hide">
								<li>{{ Lang::get('account/properties.logs.empty.details') }}</li>
							</ul>
							<div class="log-detail-content hide">
								<div class="log-detail-header">
									<div class="row">
										<div class="col-xs-3"></div>
										<div class="col-xs-9">
											<div class="row">
												<div class="col-xs-6">{{ Lang::get('account/properties.logs.value.old') }}</div>
												<div class="col-xs-6">{{ Lang::get('account/properties.logs.value.new') }}</div>
											</div>
										</div>
									</div>
								</div>
								@foreach ($log->customFields as $field => $custom_message)
									@if ( @$log->old_value[$field] || @$log->new_value[$field] )
										<div class="log-detail-row">
											<div class="row">
												<div class="col-xs-3"><strong>{{ $custom_message }}</strong></div>
												<div class="col-xs-9">
													<div class="row">
														<div class="col-xs-6">
															@include('account.properties.logs-value', [ 
																'field' => $field,
																'values' => $log->old_value,
																'property' => $property,
															])
														</div>
														<div class="col-xs-6">
															@include('account.properties.logs-value', [ 
																'field' => $field,
																'values' => $log->new_value,
																'property' => $property,
															])
														</div>
													</div>
												</div>
											</div>
										</div>
									@endif
								@endforeach
							</div>
						</div>
					</div>
				@endif
			</td>
		</tr>
	@endforeach
@endif
