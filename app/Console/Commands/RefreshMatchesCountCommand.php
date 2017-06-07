<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshMatchesCountCommand extends Command {

	protected $signature = 'stats:refresh-matches';
	protected $description = 'Refresh matches counts for the customers';

	public function handle() {

		\App\Models\Site\Customer::orderBy('id')->chunk(500, function($customers){
			foreach($customers as $customer)
			{
				$customer->matches_count = $customer->possible_matches->count();
				$customer->save();
			}
		});
		
	}

}
