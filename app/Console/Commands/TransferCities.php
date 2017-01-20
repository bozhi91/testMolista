<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TransferCities extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'cities:transfer';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Transfer cities from customer queries table to customers cities';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$customer_queries = \App\Models\Site\CustomerQueries::all();
		foreach($customer_queries as $query){
			if($query->customer_id && $query->city_id){
				$relation = new \App\Models\Site\CustomerCity();
				$relation->customer_id = $query->customer_id;
				$relation->city_id = $query->city_id;
				$relation->save();
			}
		}
	}
}
