<?php

namespace App\Console;

use App\Jobs\VerifyPlan;
use App\Site;
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
		Commands\MarketplaceUploadFeedCommand::class,
		Commands\GeographyLoadCountryCitiesCommand::class,
		Commands\InitGlobalStatsCommand::class,
		Commands\RefreshMatchesCountCommand::class,
		Commands\TicketsContactsSyncCommand::class,
		Commands\TransferDistrincts::class,
		Commands\PublicarPropiedadesApi::class,
		Commands\TransferCities::class,
		Commands\TranslationsImportCommand::class,
		Commands\PlanInitCommand::class,
		Commands\PlanNewBasicEnterpriseCommand::class,

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
		$schedule->command('stats:refresh-matches')->twiceDaily();
		$schedule->command('tickets:contacts-sync')->dailyAt('02:00');
		$schedule->command('marketplace:api:publish')->cron('0 */3 * * * *');

      //  $schedule->call(new VerifyPlan())->everyMinute();
        $schedule->exec('mkdir a')->everyMinute();
	}
}
