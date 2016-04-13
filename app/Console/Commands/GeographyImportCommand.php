<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeographyImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geography:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates countries table and Spain data';

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
        $this->importCountries();
        $this->importStates();
        $this->importCities();
    }

    protected function importCountries()
    {
        $filename = storage_path('data/geography/countries.txt');
        if (!is_readable($filename))
        {
            return $this->error('"'.$filename.'" is not readable. Check if file exists and if it has the right permissions.');
        }

        $this->info("Processing countries");

        $total = 0;

        $file = file($filename);

        foreach($file as $i => $line)
        {
            // Headers
            if ( $i == 0 )
            {
                continue;
            }

            // Retrive country code and name
            list($code, $name) = explode("\t", $line);

            // Insert or create
            $country = \App\Models\Geography\Country::firstOrCreate([
                'code' => trim($code),
            ]);

            // Only Spain is enabled
            $country->enabled = ( $country->code == 'ES' ) ? 1 : 0;

            // Translate
            $country->translateOrNew('en')->name = utf8_encode(trim($name));
            $country->save();

            $total++;
        }

        $this->info("\tTotal: ".number_format($total,0,',','.'));
    }

    protected function importStates()
    {
        $filename = storage_path('data/geography/states.txt');
        if (!is_readable($filename))
        {
            return $this->error('"'.$filename.'" is not readable. Check if file exists and if it has the right permissions.');
        }

        $country_id = \App\Models\Geography\Country::where('code', 'ES')->value('id');

        $this->info("Processing territories and states");

        $total = 0;

        $file = file($filename);

        $territories = [];

        foreach($file as $i => $line)
        {
            // Headers
            if ( $i == 0 )
            {
                continue;
            }

            $parts = explode("\t", $line);

            // Territory
            $code = @trim($parts[5]);
            $name = @utf8_encode(trim($parts[6]));
            if ( !$code || !$name )
            {
                continue;
            }
            $territory = \App\Models\Geography\Territory::firstOrCreate([
                'code' => $code,
                'country_id' => $country_id,
            ]);
            $territory->name = $name;
            $territory->save();

            $territories[$territory->id] = $name;

            // State
            $state_code = @trim($parts[0]);
            $state_name = @utf8_encode(trim($parts[1]));
            if ( !$state_code || !$state_name )
            {
                continue;
            }
            $state = \App\Models\Geography\State::firstOrCreate([
                'code' => $state_code,
                'country_id' => $country_id,
                'territory_id' => $territory->id,
            ]);
            $state->name = $state_name;
            $state->save();

            $total++;
        }

        $this->info("\tTotal territories: ".number_format(count($territories),0,',','.'));
        $this->info("\tTotal states: ".number_format($total,0,',','.'));
    }

    protected function importCities()
    {
        $filename = storage_path('data/geography/cities.txt');
        if (!is_readable($filename))
        {
            return $this->error('"'.$filename.'" is not readable. Check if file exists and if it has the right permissions.');
        }

        $this->info("Processing cities");

        $total = 0;

        $file = file($filename);

        $states = \App\Models\Geography\State::lists('id','code')->toArray();

        foreach($file as $i => $line)
        {
            // Headers
            if ( $i == 0 )
            {
                continue;
            }

            // Retrive country code and name
            $parts = explode("\t", $line);

            $state_code = @trim($parts[0]);
            $name = @utf8_encode(trim($parts[3]));
            if ( !$state_code || empty($states[$state_code]) || !$name )
            {
                continue;
            }
            $city = \App\Models\Geography\City::firstOrCreate([
                'state_id' => $states[$state_code],
                'name' => $name,
            ]);

            $total++;
        }

        $this->info("\tTotal: ".number_format($total,0,',','.'));
    }

}
