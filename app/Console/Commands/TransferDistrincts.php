<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Site\District;
use App\Property;

class TransferDistrincts extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'districts:transfer';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Transfer distrincts from properties table to distrincts table';

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
		$this->saveDistricts();
	}

	
	private function saveDistricts(){
		$properties = Property::where('district', '<>', '')->get();
		foreach($properties as $property){	
			$district = $this->getDistrict($property->site_id
					, $property->district);
			
			if($district) {
				$property->district_id = $district->id;
				$property->save();
			}
		}
	}	
	
	
	/** 
	 * @param integer $siteId
	 * @param string $name
	 * @return District|null
	 */
	private function getDistrict($siteId, $name){
		$existing = District::where('name', $name)
				->where('site_id', $siteId)->first();
		
		if($existing) {
			return $existing;
		}
		
		$new = new District();
		$new->site_id = $siteId;
		$new->name = $name;
		if($new->save()){
			return $new;
		}
	}
}
