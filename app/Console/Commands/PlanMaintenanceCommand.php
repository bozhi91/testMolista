<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class PlanMaintenanceCommand extends Command
{
	protected $signature = 'plan:init';

	protected $description = 'Create plans and modify default plan_id in sites table';

	protected $defaults = [
		'enabled' => 1,
		'level' => 0,
		'code' => false,
		'name' => false,
		'is_free' => 0,
		'price_year' => 0,
		'stripe_year_id' => '',
		'price_month' => 0,
		'stripe_month_id' => '',
		'max_employees' => '',
		'max_properties' => '',
		'max_languages' => '',
		'max_space' => '',
	];
	protected $configuration = [
		'support_email' => 1,
		'support_phone' => 1,
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
	];
	protected $extras = [
		'transfer' => 299,
	];

	public function handle()
	{

		// Create plan: Free
		$free = \App\Models\Plan::where('code','free')->first();
		if ( !$free )
		{
			$free = \App\Models\Plan::saveModel(array_merge($this->defaults, [
				'level' => 0,
				'code' => 'free',
				'name' => 'Free',
				'is_free' => 1,
				'max_employees' => 1,
				'max_properties' => 20,
				'max_languages' => 1,
				'max_space' => 1,
				'configuration' => array_merge($this->configuration, [
					'support_email' => 0,
					'support_phone' => 0,
					'qr' => 0,
					'printing' => 0,
					'analytics' => 0,
				]),
				'extras' => array_merge($this->extras, [
				]),
			]));
		}

		// Create plan: Pro
		$pro = \App\Models\Plan::where('code','pro')->first();
		if ( !$pro )
		{
			$pro = \App\Models\Plan::saveModel(array_merge($this->defaults, [
				'level' => 1,
				'code' => 'pro',
				'name' => 'Pro',
				'price_year' => 259,
				'stripe_year_id' => 'MOL PRO Y',
				'price_month' => 29,
				'stripe_month_id' => 'MOL PRO M',
				'max_employees' => 5,
				'max_properties' => 250,
				'max_space' => 4,
				'configuration' => array_merge($this->configuration, [
					'support_phone' => 0,
				]),
				'extras' => array_merge($this->extras, [
				]),
			]));
		}

		// Create plan: Plus
		$plus = \App\Models\Plan::where('code','plus')->first();
		if ( !$plus )
		{
			$plus = \App\Models\Plan::saveModel(array_merge($this->defaults, [
				'level' => 2,
				'code' => 'plus',
				'name' => 'Plus',
				'price_year' => 599,
				'stripe_year_id' => 'MOL PLUS Y',
				'price_month' => 59,
				'stripe_month_id' => 'MOL PLUS M',
				'max_space' => 8,
				'configuration' => array_merge($this->configuration, [
				]),
				'extras' => array_merge($this->extras, [
					'transfer' => '',
				]),
			]));
		}

		// Update plan_id default value
		\DB::statement("ALTER TABLE sites MODIFY COLUMN `plan_id` BIGINT(20) UNSIGNED NULL DEFAULT {$free->id}");

		// Default plan_id for null values
		\App\Site::whereNull('plan_id')->update([
			'plan_id' => $free->id,
		]);
	}

}