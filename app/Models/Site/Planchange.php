<?php namespace App\Models\Site;

use Illuminate\Database\Eloquent\SoftDeletes;

class Planchange extends \Illuminate\Database\Eloquent\Model
{
	use SoftDeletes;

    protected $table = 'sites_planchanges';

	protected $guarded = [];

	protected $dates = ['deleted_at'];

	protected $casts = [
		'old_data' => 'array',
		'new_data' => 'array',
		'invoicing' => 'array',
	];

	public static function boot()
	{
		parent::boot();

		// Whenever a site is updated
		static::saved (function($planchange){
			$planchange->site->updateSiteSetup();
		});
	}

	public function site()
	{
		return $this->belongsTo('App\Site')->withTranslations();
	}

	public function plan()
	{
		return $this->belongsTo('App\Models\Plan');
	}

	public function getPlanNameAttribute()
	{
		return $this->plan->name;
	}

	public function getPlanPriceAttribute()
	{
		$price_key = "price_{$this->payment_interval}";
		return $this->plan->$price_key;
	}

	public function getStripePlanIdAttribute()
	{
		$stripe_id_key = "stripe_{$this->payment_interval}_id";
		return $this->plan->$stripe_id_key;
	}

	public function getIbanAccountAttribute()
	{
		return @$this->new_data['iban_account'];
	}

	public function getLocaleNameAttribute()
	{
		return \App\Models\Locale::where('locale',$this->locale)->value('native');
	}

	public function getSummaryAttribute()
	{
		$summary = (object) [
			'plan_id' => $this->plan_id,
			'plan_name' => $this->plan->name,
			'plan_code' => $this->plan->code,
			'plan_price' => $this->plan_price,
			'payment_interval' => $this->payment_interval,
			'payment_method' => $this->payment_method,
			'iban_account' => $this->iban_account,
			'stripe_plan_id' => $this->stripe_plan_id,
			'status' => $this->status,
		];

		return $summary;
	}

	public function scopePending($query)
	{
		return $query->where("{$this->getTable()}.status", 'pending');
	}

	public function scopeActive($query)
	{
		return $query->where("{$this->getTable()}.status", 'accepted')->whereNull("{$this->getTable()}.deleted_at");
	}

}
