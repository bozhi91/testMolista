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
		Commands\GeographyImportCommand::class,
		Commands\UploadMaintenanceCommand::class,
		Commands\CatchesImportCommand::class,
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
	}
}
