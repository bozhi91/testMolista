<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlanNewBasicEnterpriseCommand extends Command
{

	protected $signature = 'plan:new-basic-enterprise';
	protected $description = 'Create new Basic and Enterprise plans for Molista, mark unsed plans as disabled';

	protected $plans = [
		[
			'code' => 'basic',
			'name' => 'Basic',
			'currency' => 'EUR',
			'price_year' => 490,
			'price_month' => 49,
			'max_employees' => 5,
			'max_space' => 4,
			'max_properties' => 100,
			'max_languages' => 3,
			'configuration' => [
				'support_email' => 1,
				'support_phone' => 0,
				'support_chat' => 1,
				'qr' => 1,
				'printing' => 1,
				'integrations' => 1,
				'reporting' => 1,
				'analytics' => 1,
				'responsive' => 1,
				'filters' => 1,
				'leads' => 1,
				'crm' => 1,
				'logs' => 1,
				'widgets' => 1,
			],
			'extras' => [
				'transfer' => 99,
			],
			'stripe_month_id' => 'MOL BASIC M',
			'stripe_year_id' => 'MOL BASIC Y',
		],
		[
			'code' => 'enterprise',
			'name' => 'Enterprise',
			'currency' => 'EUR',
			'price_year' => 990,
			'price_month' => 99,
			'max_employees' => false,
			'max_space' => false,
			'max_properties' => false,
			'max_languages' => false,
			'configuration' => [
				'support_email' => 1,
				'support_phone' => 1,
				'support_chat' => 1,
				'qr' => 1,
				'printing' => 1,
				'integrations' => 1,
				'reporting' => 1,
				'analytics' => 1,
				'responsive' => 1,
				'filters' => 1,
				'leads' => 1,
				'crm' => 1,
				'logs' => 1,
				'widgets' => 1,
			],
			'extras' => [
				'transfer' => '',
			],
			'stripe_month_id' => 'MOL ENTERPRISE M',
			'stripe_year_id' => 'MOL ENTERPRISE Y',
		],
		[
			'code' => 'basic_usd',
			'name' => 'Basic',
			'currency' => 'USD',
			'price_year' => 490,
			'price_month' => 49,
			'max_employees' => 5,
			'max_space' => 4,
			'max_properties' => 100,
			'max_languages' => 3,
			'configuration' => [
				'support_email' => 1,
				'support_phone' => 0,
				'support_chat' => 1,
				'qr' => 1,
				'printing' => 1,
				'integrations' => 1,
				'reporting' => 1,
				'analytics' => 1,
				'responsive' => 1,
				'filters' => 1,
				'leads' => 1,
				'crm' => 1,
				'logs' => 1,
				'widgets' => 1,
			],
			'extras' => [
				'transfer' => 99,
			],
			'stripe_month_id' => 'MOL BASIC M USD',
			'stripe_year_id' => 'MOL BASIC Y USD',
		],
		[
			'code' => 'enterprise_usd',
			'name' => 'Enterprise',
			'currency' => 'USD',
			'price_year' => 990,
			'price_month' => 99,
			'max_employees' => false,
			'max_space' => false,
			'max_properties' => false,
			'max_languages' => false,
			'configuration' => [
				'support_email' => 1,
				'support_phone' => 1,
				'support_chat' => 1,
				'qr' => 1,
				'printing' => 1,
				'integrations' => 1,
				'reporting' => 1,
				'analytics' => 1,
				'responsive' => 1,
				'filters' => 1,
				'leads' => 1,
				'crm' => 1,
				'logs' => 1,
				'widgets' => 1,
			],
			'extras' => [
				'transfer' => '',
			],
			'stripe_month_id' => 'MOL ENTERPRISE M USD',
			'stripe_year_id' => 'MOL ENTERPRISE Y USD',
		],
	];

	protected $stripe_plans = [];

	public function handle()
	{
		// Set Stripe api key
		\Stripe\Stripe::setApiKey( env('STRIPE_SECRET') );

		// Get all stripe plans
		$response = \Stripe\Plan::all([ 'limit' => 1000 ]);

		foreach ($response->data as $plan)
		{
			if ( preg_match('#^MOL #', $plan['id']) )
			{
				$this->stripe_plans[] = $plan['id'];
			}
		}

		$this->disableOldPlans();
		$this->createNewPlans();
		$this->updatePlanLevels();
	}

	public function disableOldPlans()
	{
		$this->info("Disabling old plans");

		\App\Models\Plan::where(function($query) {
			$query
				->where('code', 'like', 'pro%')
				->orWhere('code', 'like', 'plus%');
		})->each(function($plan){
			$this->info("\t{$plan->name} ({$plan->currency})");
			$plan->update([
				'enabled' => 0,
			]);
		});
	}

	public function createNewPlans()
	{
		$this->info("Create new plans");

		// Create plans
		foreach ($this->plans as $data)
		{
			$this->info("\t{$data['name']} ({$data['currency']})");

			// Check if plan exists on server
			if ( ! $plan = \App\Models\Plan::where('code', $data['code'])->first() )
			{
				// If not, create it
				$plan = \App\Models\Plan::create($data);
				$this->info("\t\tCreated on server");
			}

			// Mark as enabled
			$plan->update([
				'enabled' => 1,
			]);

			// Check if plan exists on stripe
			foreach (['Y','M'] as $period)
			{
				$stripe_id = $period == 'Y' ? $plan->stripe_year_id : $plan->stripe_month_id;

				if ( ! in_array($stripe_id, $this->stripe_plans) )
				{
					$this->info("\t\tCreated on stripe -> {$stripe_id}");

					\Stripe\Plan::create([
						'id' => $stripe_id,
						'amount' => ($period == 'Y' ? $plan->price_year : $plan->price_month) * 100,
						'currency' => strtolower($plan->currency),
						'interval' => $period == 'Y' ? 'year' : 'month',
						'name' => implode(' ', array_filter([
							"Molista {$plan->name}",
							$period == 'Y' ? 'Yearly' : 'Monthly',
							$plan->currency == 'EUR' ? false : "($plan->currency)",
						])),
						'statement_descriptor' => "Molista {$plan->name} {$period}",
					]);
				}
			}
		}
	}

	public function updatePlanLevels()
	{
		$this->info("Update plan levels");

		$groups = [];

		\App\Models\Plan::where('is_free', 0)->orderBy('price_month', 'asc')->each(function($plan) use (&$groups) {
			$groups[$plan->currency][$plan->code] = $plan;
		});

		foreach ($groups as $group)
		{
			$level = 0;

			foreach ($group as $plan)
			{
				$level++;

				$this->info("\t{$plan->name} ({$plan->currency}) => {$level}");

				$plan->update([
					'level' => $level,
				]);

				\App\Models\Stats::where('plan_id', $plan->id)->update([
					'plan_level' => $level,
				]);
			}
		}
	}

}
