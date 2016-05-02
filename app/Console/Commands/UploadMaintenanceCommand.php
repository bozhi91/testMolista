<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UploadMaintenanceCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'uploads:maintenance';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Removes unused uploads';

    /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {
        $this->info('Uploads maintenance');

        $allowed = [
            date('Ymd'),
            date('Ymd', time()-(60*60*24)),
        ];

        $dirs = array_filter( glob( public_path('sites/uploads/*') ), 'is_dir');

        if ( empty($dirs) )
        {
            $dirs = [];
        }

        foreach ($dirs as $dirpath)
        {
            $dirname = basename($dirpath);

            if ( in_array($dirname, $allowed) )
            {
                continue;
            }

            \File::deleteDirectory($dirpath);

            $this->info("\tRemove {$dirname}");
        }

    }

}
