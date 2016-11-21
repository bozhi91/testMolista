<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	* The Artisan commands provided by your application.
	*
	* @var array
	*/
	protected $commands = [
		// Commands\Inspire::class,
		Commands\RoleMaintenanceCommand::class,
		Commands\TranslationsUpdateCommand::class,
		Commands\TranslationsBulkCommand::class,
		Commands\GeographyImportCommand::class,
		Commands\UploadMaintenanceCommand::class,
		Commands\CatchesImportCommand::class,
		Commands\ProcessStatsCommand::class,
		Commands\TicketsMaintenanceCommand::class,
		Commands\ParseWebsiteCommand::class,
		Commands\PlanMaintenanceCommand::class,
		Commands\MarketplaceUploadFeedCommand::class,
		Commands\GeographyLoadCountryCitiesCommand::class,
		Commands\InitGlobalStatsCommand::class,
		Commands\RefreshMatchesCountCommand::class,
	];

	/**
	* Define the application's command schedule.
	*
	* @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	* @return void
	*/
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('uploads:maintenance')->dailyAt('06:00');
		$schedule->command('stats:process yesterday')->dailyAt('03:00');
		$schedule->command('parser:process')->hourly();
		$schedule->command('stats:refresh-matches')->twiceDaily();
	}
}
