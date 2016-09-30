<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitGlobalStatsCommand extends Command
{
	protected $signature = 'stats:init-global';

	protected $description = 'Initialize global stats';

	protected $sites;

	public function handle()
	{
		\App\Models\Stats::processStats();
	}

}	