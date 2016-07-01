<?php
	/* Accepted variables -----------------------------------------------
		- $buy_plans
		- $buy_plan_url
		- $buy_button_text
		- $buy_button_hidden
	------------------------------------------------------------------ */

	// $buy_plans fallback
	if ( empty($buy_plans) ) 
	{
		$buy_plans = \App\Models\Plan::getEnabled();
	}
?>

<div class="row">
	@foreach ($buy_plans as $plan)
		<div class="col-xs-12 col-sm-4">
			<div class="plan-block plan-{{ $plan->code }} text-center">
				<div class="plan-block-title">{{ $plan->name }}</div>
				<div class="plan-block-body">

					<div class="plan-block-item">
						<div class="plan-block-feature">{{ Lang::get('web/plans.price.year') }}</div>
						<div class="plan-block-price text-uppercase">
							@if ( @$plan->is_free ) 
								{{ Lang::get('web/plans.free') }}
							@else
								{{ price($plan->price_year,[ 'decimals'=>0 ]) }}
							@endif
						</div>
					</div>
					<div class="plan-block-item">
						<div class="plan-block-feature">{{ Lang::get('web/plans.price.year.month') }}</div>
						<div>
							@if ( @$plan->is_free ) 
								<strong class="text-uppercase">{{ Lang::get('web/plans.free') }}</strong>
							@else
								<strong>{{ price($plan->price_year/12,[ 'decimals'=>1 ]) }}</strong>
							@endif
						</div>
					</div>
					<div class="plan-block-item">
						<div class="plan-block-feature">{{ Lang::get('web/plans.price.month') }}</div>
						<div>
							@if ( @$plan->is_free ) 
								<strong class="text-uppercase">{{ Lang::get('web/plans.free') }}</strong>
							@else
								<strong>{{ price($plan->price_month,[ 'decimals'=>1 ]) }}</strong>
							@endif
						</div>
					</div>

					<div class="plan-block-item">
						{{ Lang::get('web/plans.employees') }}: 
						@if ( @$plan->max_employees )
							<strong>{{ number_format($plan->max_employees, 0, ',', '.') }}</strong>
						@else
							<strong class="text-lowercase">{{ Lang::get('web/plans.unlimited') }}</strong>
						@endif
					</div>
					<div class="plan-block-item">
						{{ Lang::get('web/plans.space') }}: 
						@if ( @$plan->max_space )
							<strong>{{ number_format($plan->max_space, 0, ',', '.') }}GB</strong>
						@else
							<strong class="text-lowercase">{{ Lang::get('web/plans.unlimited') }}</strong>
						@endif
					</div>
					<div class="plan-block-item">
						{{ Lang::get('web/plans.properties') }}: 
						@if ( @$plan->max_properties )
							<strong>{{ number_format($plan->max_properties, 0, ',', '.') }}</strong>
						@else
							<strong>{{ Lang::get('web/plans.unlimited') }}</strong>
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['integrations'] )
							{{ Lang::get('web/plans.integrations') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						{{ Lang::get('web/plans.languages') }}: 
						@if ( @$plan->max_languages )
							<strong>{{ number_format($plan->max_languages, 0, ',', '.') }}</strong>
						@else
							<strong>{{ number_format(count(LaravelLocalization::getSupportedLocales()), 0, ',', '.') }}</strong>
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['printing'] )
							{{ Lang::get('web/plans.printing') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['reporting'] )
							{{ Lang::get('web/plans.reporting') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['analytics'] )
							{{ Lang::get('web/plans.analytics') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['responsive'] )
							{{ Lang::get('web/plans.responsive') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						{{ Lang::get('web/plans.support') }}: 
						@if ( empty($plan->configuration['support_email']) && empty($plan->configuration['support_chat']) && empty(@$plan->configuration['support_phone']) )
							-
						@else
							<strong><?php
								echo implode(' / ', array_filter([
									@$plan->configuration['support_email'] ? Lang::get('web/plans.support.email') : false,
									@$plan->configuration['support_chat'] ? Lang::get('web/plans.support.chat') : false,
									@$plan->configuration['support_phone'] ? Lang::get('web/plans.support.phone') : false,
								]));
							?></strong>
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['qr'] )
							{{ Lang::get('web/plans.qr') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['filters'] )
							{{ Lang::get('web/plans.filters') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['leads'] )
							{{ Lang::get('web/plans.leads') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['crm'] )
							{{ Lang::get('web/plans.crm') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['logs'] )
							{{ Lang::get('web/plans.logs') }}
						@else
							-
						@endif
					</div>
					<div class="plan-block-item">
						@if ( @$plan->configuration['widgets'] )
							{{ Lang::get('web/plans.widgets') }}
						@else
							-
						@endif
					</div>

					@if ( $plan->extras )
						@foreach ($plan->extras as $extra_key => $extra_cost)
							<div class="plan-block-item plan-block-item-last">
								{{ Lang::get("web/plans.extras.{$extra_key}") }}:
								@if ( $extra_cost )
									<strong>{{ price($extra_cost,[ 'decimals'=>0 ]) }}</strong>
								@else
									<strong class="text-lowercase">{{ Lang::get('web/plans.included') }}</strong>
								@endif
							</div>
						@endforeach
					@endif

				</div>

				@if ( empty($buy_button_hidden) )
					<div class="plan-block-footer">
						<a href="{{ empty($buy_plan_url) ? 'javascript:;' : "{$buy_plan_url}?plan={$plan->code}" }}" class="plan-button text-uppercase">
							{{ empty($buy_button_text) ? Lang::get('web/plans.buy') : $buy_button_text }}
						</a>
					</div>
				@endif
			</div>
			<div class="visible-xs" style="height: 50px;"></div>
		</div>
	@endforeach
</div>
