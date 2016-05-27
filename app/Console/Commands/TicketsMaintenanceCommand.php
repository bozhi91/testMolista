<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TicketsMaintenanceCommand extends Command
{
	protected $signature = 'tickets:maintenance';

	protected $description = 'Site, users, customers and properties maintenance on ticketing service';

	protected $client;

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		// For each site
		\App\Site::withTranslations()
					->chunk(10, function ($sites) {
			foreach ($sites as $site) 
			{
				$this->info("Processing {$site->title}");

				// Get adm
				$ticket_adm = $site->ticket_adm;

				// Site exists ?
				if ( $site->ticket_owner_token ) 
				{
					// Associate users
					$this->info("\tSite account exists: associate users");
					$ticket_adm->associateUsers( $site->users()->with('roles')->whereIn('id', $site->owners_ids)->get() );
				}
				else
				{
					$this->info("\tCreate site account");
					if ( !$ticket_adm->createSite() )
					{
						continue;
					}
					// Update site data
					$site = \App\Site::withTranslations()->find($site->id);
					$ticket_adm = $site->ticket_adm;
				}

				// Create contacts
				$this->info("\tAssociate customers (leads)");
				foreach ($site->customers()->where('ticket_contact_id','')->get() as $customer)
				{
					$ticket_adm->associateContact($customer);
				}

				// Create items
				$this->info("\tAssociate properties");
				foreach ($site->properties()->withTrashed()->withTranslations()->where('ticket_item_id','')->get() as $property)
				{
					$ticket_adm->associateItem($property);
				}
			}
		});

	}

}