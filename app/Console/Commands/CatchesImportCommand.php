<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CatchesImportCommand extends Command
{

	protected $signature = 'properties:catches';
	protected $description = 'Populates catches table with property info';

	public function handle()
	{
		$this->info("Properties catches");

		$total = 0;

		$properties = \App\Property::withTrashed()->whereNotIn('id', function($query){
			$query->distinct()->select('property_id')->from('properties_catches');
		})->with('catches')->get();

		if ( $properties->count() > 0 )
		{
			$sites = [];


			foreach ($properties as $property)
			{
				// No site ID ?
				if ( !$property->site_id )
				{
					$this->error("Property ID {$property->id} does not have site_id");
					continue;
				}

				// Get site
				$site = @$sites[$property->site_id];

				// No site yet, get it
				if ( !$site )
				{
					$site = \App\Site::with('users')->find($property->site_id);
					if ( !$site )
					{
						$this->error("Could not find site with ID {$property->site_id}");
						continue;
					}
					$sites[$property->site_id] = $site;
				}

				// Default owner
				$site_owners = array_values( $site->owners_ids );
				if ( empty($site_owners) )
				{
					$this->error("Site ID {$site->id} has no owner");
					continue;
				}

				// Create catch
				$property->catches()->create([
					'employee_id' => $site_owners[0],
					'catch_date' => $property->created_at->format('Y-m-d H:i:s'),
					'price_original' => $property->price,
					'price_min' => $property->price,
					'status' => 'active',
				]);

				$total++;
			}
		}

		$this->info("Catches created: " . number_format($total, 0, ',', '.') );

		foreach (\App\Property::withTrashed()->with('catches')->get() as $property)
		{
			if ( !$property->catch_current )
			{
				continue;
			}

			$property->publisher_id = $property->catch_current->employee_id;
			$property->save();
		}

	}

}
