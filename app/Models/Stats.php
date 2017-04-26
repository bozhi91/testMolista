<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
	protected $table = 'stats';

	protected $guarded = [];

	public $timestamps = false;

	protected static $currency_rates;
	protected static $countries_rel;

	public function site()
	{
		return $this->belongsTo('App\Site')->with('domains');
	}

	public function plan()
	{
		return $this->belongsTo('App\Models\Plan');
	}

	public function scopeWithDateRange($query, $daterange)
	{

		if ( preg_match('#^(\d{2})\/(\d{2})\/(\d{4}) - (\d{2})\/(\d{2})\/(\d{4})$#', $daterange, $matches) )
		{
			$query
				->whereDate('date_created', '>=', "{$matches[3]}-{$matches[2]}-{$matches[1]}")
				->whereDate('date_created', '<=', "{$matches[6]}-{$matches[5]}-{$matches[4]}")
				;
		}

	}

	static public function getConsolidatedStats()
	{
		$stats = [
			'free' => 0,
			'paying' => 0,
			'revenue' => 0,
		];

		$query = self::selectRaw("`plan_level`, COUNT(*) as total_sites, SUM(`monthly_fee`) as total_revenues")
					->whereNotIn('site_id', explode(',', env('EXCLUDE_SITES_FROM_STATS')))
					->whereIn('site_id', function($q){
						$subquery = $q->select('id')->from('sites');
						$subquery->where('enabled', 1);
						return $subquery;
					})
					->groupBy('plan_level');

		foreach ($query->get() as $data)
		{
			if ( $data->plan_level < 1 )
			{
				$stats['free'] += $data->total_sites;
			}
			else
			{
				$stats['paying'] += $data->total_sites;
			}

			$stats['revenue'] += $data->total_revenues;
		};

		return $stats;
	}

	static public function processStats()
	{
		self::$currency_rates = [ 'EUR' => 1, ];

		self::$countries_rel = \App\Models\Geography\Country::withTranslations()->lists('name','id')->all();

		\App\Site::with('plan')->chunk(25, function ($sites) {
			foreach ($sites as $site)
			{
				$old = \App\Models\Stats::firstOrCreate([
					'site_id' => $site->id,
				]);

				$stat = [
					'site_id' => $site->id,
					'plan_id' => $site->plan_id,
					'plan_level' => $site->plan->level,
					'payment_interval' => $site->payment_interval,
					'payment_method' => $site->payment_method,
					'monthly_fee' => self::getMonthlyFee($site),
					'date_created' => $site->created_at->format('Y-m-d'),
					'address' => self::getInvoicingAddress($site),
					'infowindow' => self::getInfowindow($site),
					'lat' => $old->lat,
					'lng' => $old->lng,
				];

				if ( !$stat['lng'] ||  !$stat['lng'] || $stat['address'] != $old->address )
				{
					$response = \Geocoder::geocode('json', [
						'address' => $stat['address'],
					]);

					$response = json_decode($response);
					if ( @$response->status == 'OK' )
					{
						if ( @$response->results && is_array($response->results) )
						{
							$first = array_shift($response->results);
							$stat['lat']	= $first->geometry->location->lat;
							$stat['lng']	= $first->geometry->location->lng;
						}
					}

					if ( !$stat['lng'] ||  !$stat['lng'] )
					{
						$stat['lat'] = config('app.lat_default');
						$stat['lng'] = config('app.lng_default');
					}
				}

				$old->update($stat);
			}
		});
	}
	static public function getMonthlyFee($site)
	{
		$currency_rates = self::$currency_rates;

		if ( $site->plan && !$site->plan->is_free )
		{
			if ( !array_key_exists($site->plan->currency, $currency_rates) )
			{

				$currency_rates[$site->plan->currency] = \App\Models\CurrencyConverter::convert(1, $site->plan->currency, 'EUR');
			}

			switch ( $site->payment_interval )
			{
				case 'month':
					return $site->plan->price_month * $currency_rates[$site->plan->currency];
				case 'year':
					return ($site->plan->price_year / 12 ) * $currency_rates[$site->plan->currency];
			}
		}

		return 0;
	}
	static public function getInvoicingAddress($site)
	{
		$countries_rel = self::$countries_rel;

		$data = $site->invoicing;

		if ( empty($data['country']) && @$data['country_id'] && array_key_exists($data['country_id'], $countries_rel) )
		{
			$data['country'] = $countries_rel[$data['country_id']];
		}

		if ( empty($data['country']) )
		{
			$data['country'] = 'Spain';
		}

		return implode(', ', array_filter([
			@$data['street'],
			@$data['zipcode'],
			@$data['city'],
			@$data['country'],
		]));
	}
	static public function getInfowindow($site)
	{
		return implode('<br />', array_filter([
			"Site ID {$site->id}",
			$site->main_url,
			"Plan: {$site->plan->name}" . ($site->plan->is_free ? '' : " ({$site->plan->currency})"),
			$site->plan->is_free ? false : "Payment: {$site->payment_method} / {$site->payment_interval}"
		]));
	}
}
