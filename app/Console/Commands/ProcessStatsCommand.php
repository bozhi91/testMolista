<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessStatsCommand extends Command
{
	protected $signature = 'stats:process {date? : The date to process}';

	protected $description = 'Process sites and users stats';

	public function handle()
	{
		$date = $this->argument('date');

		// Date fix
		switch ( $date )
		{
			case 'today':
			case 'yesterday':
				$obj = new \DateTime();
				$obj->add( \DateInterval::createFromDateString( $this->argument('date') ) );
				$date = $obj->format('Y-m-d');
				break;
		}

		// Invalid date
		if ( $date && !preg_match('#^\d{4}\-\d{2}\-\d{2}$#', $date) )
		{
			$this->error("ProcessStatsCommand -> invalid date provided: {$this->argument('date')}");
			return false;
		} 
		// Single date
		elseif ( $date )
		{
			$this->info("ProcessStatsCommand -> {$this->argument('date')}");
			$this->processDate($date);
		}
		// All dates
		else
		{
			$from = \App\Property::orderBy('published_at')->value('published_at');
			if ( !$from )
			{
				$this->warn("ProcessStatsCommand -> no properties in database");
				return false;
			}

			$this->info("ProcessStatsCommand -> all dates");

			for ($i=strtotime($from); $i<time(); $i+=(60*60*24))
			{
				$this->processDate( date('Y-m-d', $i) );

			}
		}

		// Delete stats cache
		$this->info("\tDeleting stats cache");
		\File::deleteDirectory(storage_path('app/stats'), true);
	}

	protected function processDate($date) {
		$this->info("\t{$date}");

		// Remove current stats
		\App\Models\Site\Stats::where([ 'date' => $date ])->delete();
		\App\Models\User\Stats::where([ 'date' => $date ])->delete();

		$this->processProperties($date);
		$this->processPropertiesToDate($date);
		$this->processClosures($date);
		$this->processVisits($date);
		$this->processLeads($date);
	}

	// Published properties
	protected function processProperties($date)
	{
		$properties = \App\Property::withTrashed()->where('published_at',$date)->get();

		foreach ($properties as $property)
		{
			if ( !$property->site_id )
			{
				continue;
			}

			// Site stats
			$data = [];
			$site_line = \App\Models\Site\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $property->site_id,
			]);
			switch ( $property->mode )
			{
				case 'sale':
					$data['sale'] = $site_line->sale + 1;
					$data['sale_price'] = ( ( $site_line->sale_price * $site_line->sale ) + $property->price ) / $data['sale'];
					$data['sale_sqm'] = ( ( $site_line->sale_sqm * $site_line->sale ) + $property->size ) / $data['sale'];
					break;
				case 'rent':
					$data['rent'] = $site_line->rent + 1;
					$data['rent_price'] = ( ( $site_line->rent_price * $site_line->rent ) + $property->price ) / $data['rent'];
					$data['rent_sqm'] = ( ( $site_line->rent_sqm * $site_line->rent ) + $property->size ) / $data['rent'];
					break;
			}
			$site_line->update($data);

			// Check if user is defined
			if ( !$property->publisher_id )
			{
				continue;
			}

			// User stats
			$data = [];
			$user_line = \App\Models\User\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $property->site_id,
				'user_id' => $property->publisher_id,
			]);
			switch ( $property->mode )
			{
				case 'sale':
					$data['sale'] = $user_line->sale + 1;
					break;
				case 'rent':
					$data['rent'] = $user_line->rent + 1;
					break;
			}
			$user_line->update($data);
		}
	}

	// Process properties to-date
	protected function processPropertiesToDate($date)
	{
		$stats = [];
		$properties = \App\Property::withTrashed()
						->whereNotNull('site_id')
						->whereDate('published_at','<=',$date)
						->where(function ($query) use ($date) {
							$query->whereDate('deleted_at', '>=', $date)
									->orWhereRaw('`deleted_at` IS NULL');
						})
						->get();

		foreach ($properties as $property)
		{
			if ( !array_key_exists($property->site_id,$stats) )
			{
				$stats[$property->site_id] = [
					'current_sale' => 0,
					'current_rent' => 0,
					'current_sale_price' => 0,
					'current_rent_price' => 0,
					'current_sale_sqm' => 0,
					'current_rent_sqm' => 0,
				];
			}

			$sale = $stats[$property->site_id]['current_sale'];
			$rent = $stats[$property->site_id]['current_rent'];
			$sale_price_total = $stats[$property->site_id]['current_sale_price'] * $sale;
			$rent_price_total = $stats[$property->site_id]['current_rent_price'] * $rent;
			$sale_sqm_total = $stats[$property->site_id]['current_sale_sqm'] * $sale;
			$rent_sqm_total = $stats[$property->site_id]['current_rent_sqm'] * $rent;

			switch ( $property->mode )
			{
				case 'sale':
					$stats[$property->site_id]['current_sale']++;
					$stats[$property->site_id]['current_sale_price'] =  ( $sale_price_total + $property->price ) / $stats[$property->site_id]['current_sale'];
					$stats[$property->site_id]['current_sale_sqm'] = ( $sale_sqm_total + ($property->price / $property->size) ) / $stats[$property->site_id]['current_sale'];
					break;
				case 'rent':
					$stats[$property->site_id]['current_rent']++;
					$stats[$property->site_id]['current_rent_price'] =  ( $rent_price_total + $property->price ) / $stats[$property->site_id]['current_rent'];
					$stats[$property->site_id]['current_rent_sqm'] = ( $rent_sqm_total + ($property->price / $property->size) ) / $stats[$property->site_id]['current_rent'];
					break;
			}
		}

		foreach ($stats as $site_id => $data) 
		{
			$site_line = \App\Models\Site\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $site_id,
			]);
			$site_line->update($data);
		}
	}

	// Closed transactions
	protected function processClosures($date)
	{
		$closures = \App\Models\Property\Catches::with('property')
						->where('transaction_date',$date)
						->whereIn('status',['sold','rent'])
						->get();

		foreach ($closures as $closure)
		{
			if ( !$closure->property || !$closure->property->site_id )
			{
				continue;
			}

			// Site stats
			$data = [];
			$site_line = \App\Models\Site\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $closure->property->site_id,
			]);
			switch ( $closure->status )
			{
				case 'sold':
					$data['sale_closed'] = $site_line->sale_closed + 1;
					break;
				case 'rent':
					$data['rent_closed'] = $site_line->rent_closed + 1;
					break;
			}
			$site_line->update($data);

			// Check if user is defined
			if ( !$closure->closer_id )
			{
				continue;
			}

			// User stats
			$data = [];
			$user_line = \App\Models\User\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $closure->property->site_id,
				'user_id' => $closure->closer_id,
			]);
			switch ( $closure->status )
			{
				case 'sold':
					$data['sale_closed'] = $user_line->sale_closed + 1;
					break;
				case 'rent':
					$data['rent_closed'] = $user_line->rent_closed + 1;
					break;
			}
			$user_line->update($data);
		}
	}

	protected function processLeads($date)
	{
		$leads = \App\Models\Site\Customer::whereDate('created_at','=',$date)->get();
		foreach ($leads as $lead) 
		{
			if ( !$lead->site_id )
			{
				continue;
			}

			// Site stats
			$data = [];
			$site_line = \App\Models\Site\Stats::firstOrCreate([
				'date' => $date,
				'site_id' => $lead->site_id,
			]);
			$data['leads'] = $site_line->leads + 1;
			$site_line->update($data);
		}
	}

	// [TODO]
	protected function processVisits($date)
	{
		/*
		Para site y por user
		'sale_visits' // Visitas realizadas para venta
		'rent_visits' //Visitas realizadas para alquiler
		*/
	}
}
