<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class TicketsContactsSyncCommand extends Command
{
	protected $signature = 'tickets:contacts-sync';

	protected $description = 'Sync tickets contacts';

	public function __construct()
	{
		parent::__construct();
	}

	public function handle()
	{
		// For each site
		\App\Site::chunk(10, function ($sites) {
			foreach ($sites as $site) 
			{
				$this->info("Importing customers for site ID {$site->id}");
				$site->importTicketingCustomers();
			}
		});

	}

}