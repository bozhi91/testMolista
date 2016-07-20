<?php namespace App\Models;

use Florianv\LaravelSwap\Facades\Swap;

class CurrencyConverter 
{

	public static function __callStatic($name, $arguments)
	{
		// Conversion from static call
		$from = $to = false;
		if (preg_match('#convert([A-Z]+)to([A-Z]+)#', $name, $match))
		{
			return self::convert($arguments[0], $match[1], $match[2]);
		}

		throw new \BadMethodCallException("Method $name does not exits", 1);
	}

	static public function convert($amount, $from, $to)
	{
		$from = strtoupper($from);
		$to = strtoupper($to);

		// The same conversion??? Nothing to do here...
		if ( $from == $to )
		{
			return $amount;
		}

		// Get rate from currency_rates table
		$rate = \App\Models\CurrencyRate::whereDate('updated_at', '=', date('Y-m-d'))
											->where('from',$from)
											->where('to',$to)
											->value('rate');

		// Update required?
		if ( !$rate )
		{
			// Check
			try
			{
				$rate = Swap::quote("{$from}/{$to}")->getValue();
			}
			catch (\InvalidArgumentException $e)
			{
				\Log::error("Rate to convert from {$from} to {$to} not available");
				return $amount;
			}

			\App\Models\CurrencyRate::firstOrCreate([
				'from' => $from,
				'to' => $to,
			])->update([
				'rate' => $rate,
			]);

			\App\Models\CurrencyRate::firstOrCreate([
				'from' => $to,
				'to' => $from,
			])->update([
				'rate' => (1 / $rate),
			]);

		}

		return $amount * $rate;
	}

}
