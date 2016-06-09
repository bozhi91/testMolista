<?php
	if ( empty($plans) ) 
	{
		$plans = \App\Models\Plan::orderBy('is_free','desc')->orderBy('price_year','asc')->get()->keyBy('code');
	}
?>
<div style="padding: 30px;">
	<div class="row">
		@foreach (['free','pro','plus'] as $c)
			<div class="col-xs-4">
				<div class="plan-block plan-{{ $c }} text-center">
					<div class="plan-block-title">{{ $plans[$c]->name }}</div>
					<div class="plan-block-body">
						<div class="plan-block-item">
							<div class="plan-block-feature">{{ Lang::get('web/plans.price.year') }}</div>
							<div class="plan-block-price text-uppercase">
								@if ( @$plans[$c]->is_free ) 
									{{ Lang::get('web/plans.free') }}
								@else
									{{ price($plans[$c]->price_year,[ 'decimals'=>0 ]) }}
								@endif
							</div>
						</div>
						<div class="plan-block-item">
							<div class="plan-block-feature">{{ Lang::get('web/plans.price.year.month') }}</div>
							<div>
								@if ( @$plans[$c]->is_free ) 
									<strong class="text-uppercase">{{ Lang::get('web/plans.free') }}</strong>
								@else
									<strong>{{ price($plans[$c]->price_year/12,[ 'decimals'=>1 ]) }}</strong>
								@endif
							</div>
						</div>
						<div class="plan-block-item">
							<div class="plan-block-feature">{{ Lang::get('web/plans.price.month') }}</div>
							<div>
								@if ( @$plans[$c]->is_free ) 
									<strong class="text-uppercase">{{ Lang::get('web/plans.free') }}</strong>
								@else
									<strong>{{ price($plans[$c]->price_month,[ 'decimals'=>1 ]) }}</strong>
								@endif
							</div>
						</div>
						<div class="plan-block-item">
							{{ Lang::get('web/plans.employees') }}: 
							@if ( @$plans[$c]->max_employees )
								<strong>{{ number_format($plans[$c]->max_employees, 0, ',', '.') }}</strong>
							@else
								<strong class="text-lowercase">{{ Lang::get('web/plans.unlimited') }}</strong>
							@endif
						</div>
						<div class="plan-block-item">
							{{ Lang::get('web/plans.space') }}: 
							@if ( @$plans[$c]->max_space )
								<strong>{{ number_format($plans[$c]->max_space, 0, ',', '.') }}GB</strong>
							@else
								<strong class="text-lowercase">{{ Lang::get('web/plans.unlimited') }}</strong>
							@endif
						</div>
						<div class="plan-block-item">
							{{ Lang::get('web/plans.properties') }}: 
							@if ( @$plans[$c]->max_properties )
								<strong>{{ number_format($plans[$c]->max_properties, 0, ',', '.') }}</strong>
							@else
								<strong>{{ Lang::get('web/plans.unlimited') }}</strong>
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['integrations'] )
								{{ Lang::get('web/plans.integrations') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							{{ Lang::get('web/plans.languages') }}: 
							@if ( @$plans[$c]->max_languages )
								<strong>{{ number_format($plans[$c]->max_languages, 0, ',', '.') }}</strong>
							@else
								<strong>{{ number_format(count(LaravelLocalization::getSupportedLocales()), 0, ',', '.') }}</strong>
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['printing'] )
								{{ Lang::get('web/plans.printing') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['reporting'] )
								{{ Lang::get('web/plans.reporting') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['analytics'] )
								{{ Lang::get('web/plans.analytics') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['responsive'] )
								{{ Lang::get('web/plans.responsive') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							{{ Lang::get('web/plans.support') }}: 
							@if ( @$plans[$c]->configuration['support_email'] && @$plans[$c]->configuration['support_phone'] )
								<strong>{{ Lang::get('web/plans.support.email') }} / {{ Lang::get('web/plans.support.phone') }}</strong>
							@elseif ( @$plans[$c]->configuration['support_email'] )
								<strong>{{ Lang::get('web/plans.support.email') }}</strong>
							@elseif ( @$plans[$c]->configuration['support_phone'] )
								<strong>{{ Lang::get('web/plans.support.phone') }}</strong>
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['qr'] )
								{{ Lang::get('web/plans.qr') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['filters'] )
								{{ Lang::get('web/plans.filters') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['leads'] )
								{{ Lang::get('web/plans.leads') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['crm'] )
								{{ Lang::get('web/plans.crm') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['logs'] )
								{{ Lang::get('web/plans.logs') }}
							@else
								-
							@endif
						</div>
						<div class="plan-block-item">
							@if ( @$plans[$c]->configuration['widgets'] )
								{{ Lang::get('web/plans.widgets') }}
							@else
								-
							@endif
						</div>
						@foreach ($plans[$c]->extras as $extra_key => $extra_cost)
							<div class="plan-block-item plan-block-item-last">
								{{ Lang::get("web/plans.extras.{$extra_key}") }}:
								@if ( $extra_cost )
									<strong>{{ price($extra_cost,[ 'decimals'=>0 ]) }}</strong>
								@else
									<strong class="text-lowercase">{{ Lang::get('web/plans.included') }}</strong>
								@endif
							</div>
						@endforeach
					</div>
					<div class="plan-block-footer">
						<a href="{{ empty($buy_plan_url) ? 'javascript:;' : "{$buy_plan_url}?plan={$plans[$c]->code}" }}" class="plan-button text-uppercase">{{ Lang::get('web/plans.buy') }}</a>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>
