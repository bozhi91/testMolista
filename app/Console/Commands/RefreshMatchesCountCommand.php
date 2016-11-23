<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshMatchesCountCommand extends Command {

	protected $signature = 'stats:refresh-matches';
	protected $description = 'Refresh matches counts for the customers';

	public function handle() {
		$customers = \App\Models\Site\Customer::all();
		
		foreach($customers as $customer){
			$customer->matches_count = $customer->possible_matches->count();
			$customer->save();
		}
	}

}
