<?php namespace App\Models\Site;

use Laravel\Cashier\Subscription as BaseSubscription;

class Subscription extends BaseSubscription
{
    public function user()
    {
        $model = getenv('STRIPE_MODEL') ?: config('services.stripe.model', 'User');

        return $this->belongsTo($model, 'site_id');
    }
}
